<?php require("php/connectdb.php"); ?>
<?php
    session_start();
    if (!isset($_SESSION['idPro'])) {
        header("Location: Connexion.html");
        exit();
    }
    function getSallesProprietaire($connexion, $idPro) {
        $query = "SELECT * FROM salle WHERE idPro = '$idPro'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        $salles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $salles[] = $row;
        }
        return $salles;
    }
    
    function voirSalle($connexion, $id_salle) {
        $query = "SELECT nomS, lieus, capacite FROM salle WHERE idSalle = '$id_salle'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        return mysqli_fetch_assoc($result);
    }

    function getMachinesSalle($connexion, $id_salle) {
        $query= "SELECT DISTINCT machine.* FROM machine Join capteurs on machine.idM=capteurs.idM WHERE capteurs.idSalle = '$id_salle'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        $machines = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $machines[] = $row;
        }
        return $machines;
    }

    function getcapteur($connexion, $machines) {
        $capteurs = [];
        foreach ($machines as $machine) {
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM capteurs JOIN type ON capteurs.idT = type.idT WHERE idM = $idM";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            
            while ($row = mysqli_fetch_assoc($result)) {
                $capteurs[] = $row;
            }
        }
        return $capteurs;
    }
    function getTemperatureData($connexion, $capteurs) {

        $temperatures = [];

        foreach ($capteurs as $capteur) {

            $idCap = (int)$capteur['idCap'];

            // Dernière température
            $query = "SELECT valeur FROM donnee WHERE IdCap = $idCap AND Nom = 'Temperature' ORDER BY date DESC LIMIT 1";

            // Moyenne température du jour
            $queryAvg = "SELECT AVG(valeur) AS moyenne
            FROM donnee
            WHERE IdCap = $idCap
            AND Nom = 'Temperature'
            AND DATE(date) = CURDATE()
            ";

            $result = mysqli_query($connexion, $query);
            $resultAvg = mysqli_query($connexion, $queryAvg);

            if (!$result || !$resultAvg) {
                die("SQL Error: " . mysqli_error($connexion));
            }

            $donnee = mysqli_fetch_assoc($result);
            $moyenne = mysqli_fetch_assoc($resultAvg);

            $temperatures[$idCap] = [
                'valeurT' => $donnee ? $donnee['valeur'] : null,
                'moyenneT' => $moyenne['moyenne']
            ];
        }

        return $temperatures;
    }
    function getVoltageData($connexion, $capteurs) {

        $voltages = [];

        foreach ($capteurs as $capteur) {

            $idCap = (int)$capteur['idCap'];

            // Dernier voltage
            $query = "SELECT valeur
            FROM donnee
            WHERE IdCap = $idCap
            AND Nom = 'Voltage'
            ORDER BY date DESC
            LIMIT 1
            ";

            // Moyenne voltage du jour
            $queryAvg = "SELECT AVG(valeur) AS moyenne FROM donnee WHERE IdCap = $idCap
            AND Nom = 'Voltage'
            AND DATE(date) = CURDATE()
            ";

            $result = mysqli_query($connexion, $query);
            $resultAvg = mysqli_query($connexion, $queryAvg);

            if (!$result || !$resultAvg) {
                die("SQL Error: " . mysqli_error($connexion));
            }

            $donnee = mysqli_fetch_assoc($result);
            $moyenne = mysqli_fetch_assoc($resultAvg);

            $voltages[$idCap] = [
                'valeurV' => $donnee ? $donnee['valeur'] : null,
                'moyenneV' => $moyenne['moyenne']
            ];
        }

        return $voltages;
    }

    function cartealerte($connexion, $machines) {
        $alertes = [];// liste de sortie
        foreach ($machines as $machine) {// pour chaque machine de la salle, on récupère les alertes non résolues
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM alerte WHERE idM = '$idM' AND resolu = 0";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            while ($alerte = mysqli_fetch_assoc($result)) {
                $alertes[] = $alerte;
            }
        }
        return $alertes;
    }

    function popupAlerte($connexion, $machines, $derniereAlerte) {
        foreach ($machines as $machine) {
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM alerte WHERE idM = $idM AND resolu = 0 AND idA > $derniereAlerte ORDER BY idA DESC LIMIT 1";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            $alerte = mysqli_fetch_assoc($result);
            if ($alerte) {
                $alertes[] = $alerte;
            } else {
                return null;
            }
        }
        return $alertes;
    }
    function calculernbVestiaire($connexion, $idSalle) {
        $query = "SELECT COUNT(*) AS nbVestiaires FROM vestiaire WHERE idSalle = '$idSalle'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion));
        }
        $row = mysqli_fetch_assoc($result);
        return $row['nbVestiaires'];
     }
    /* ========================= */
    /* SALLE SELECTIONNEE        */
    /* ========================= */
    $idPro = $_SESSION['idPro'];
    $salles_proprietaire = getSallesProprietaire($connexion, $_SESSION['idPro']);

    if (isset($_GET['id'])) {
        $id_salle = (int)$_GET['id'];
        $salle    = voirSalle($connexion, $id_salle);
        $machines = getMachinesSalle($connexion, $id_salle);
        
        $capteurs = getcapteur($connexion, $machines);
        $temperatureData = getTemperatureData($connexion, $capteurs);
        $voltageData = getVoltageData($connexion, $capteurs);
        $alertes  = cartealerte($connexion, $machines);
        $maxAlertId = 0;
        foreach ($alertes as $a) {
            if ($a['idA'] > $maxAlertId) $maxAlertId = $a['idA'];
        }

        if (isset($_GET['derniereAlerte'])) {
            $lastAlertId = (int)$_GET['derniereAlerte'];
            $newAlerte = null;
            if (is_array($alertes) && count($alertes) > 0) {
                foreach ($alertes as $alerte) {
                    if (is_array($alerte) && isset($alerte['idA']) && $alerte['idA'] > $lastAlertId) {
                        $newAlerte = $alerte;
                        break;
                    }
                }
            }
            header('Content-Type: application/json');
            if ($newAlerte && is_array($newAlerte)) {
                echo json_encode([
                    "idAlerte" => (int)$newAlerte["idA"],
                    "date"     => $newAlerte["date"],
                    "machine"  => (int)$newAlerte["idM"],
                    "message"  => "Une nouvelle alerte a été déclenchée pour la machine " . (int)$newAlerte["idM"] . " (valeur: " . $newAlerte["ValeurA"] . ")"
                ]);
            } else {
                echo json_encode([]);
            }
            exit();
        }

    } else {
        $salle = $salles_proprietaire[0];
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'cartealerte') {
            $alerte = cartealerte($connexion, $machines);
            header('Content-Type: application/json');
            echo json_encode($alerte);
            exit();
        }
        if ($_GET['action'] == 'getTemperatureData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $temperatureData = getTemperatureData($connexion, $capteurs);
            $result   = [];
            foreach ($capteurs as $capteur) {
                $idCap = (int)$capteur['idCap'];
                if (isset($temperatureData[$idCap])) {
                    $result[] = [
                        'idCap'   => $idCap,
                        'valeur'  => $temperatureData[$idCap]['valeurT'],
                        'moyenne' => $temperatureData[$idCap]['moyenneT']
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        if ($_GET['action'] == 'getVoltageData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $voltageData = getVoltageData($connexion, $capteurs);
            $result   = [];
            foreach ($capteurs as $capteur) {
                $idCap = (int)$capteur['idCap'];
                if (isset($voltageData[$idCap])) {
                    $result[] = [
                        'idCap'   => $idCap,
                        'valeur'  => $voltageData[$idCap]['valeurV'],
                        'moyenne' => $voltageData[$idCap]['moyenneV']
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }
    // les graphiques
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Salle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <img src="img/rasp.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        <ul class="nav-links">
            <li><a href="Accueil.html">Accueil</a></li>
            <li><a href="#">Produits</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">À propos</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>

    <!-- ============================================================ -->
    <!-- MAIN LAYOUT                                                   -->
    <!-- ============================================================ -->
    <div class="container py-4">
        <div class="row">

            <!-- ===================================================== -->
            <!-- SIDEBAR — liste des salles                             -->
            <!-- ===================================================== -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-4 h-100">
                    <div class="card-body p-0">
                        <h3 class="px-3 pt-3 mb-2">Salles du propriétaire</h3>
                        <div class="list-group list-group-flush">
                            <?php foreach ($salles_proprietaire as $s): ?>
                                <a href="salle3.php?id=<?= $s['idSalle']; ?>"
                                   class="list-group-item list-group-item-action px-3 py-2">
                                    <div class="fw-semibold small"><?= $s['nomS']; ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= $s['lieus']; ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin col-md-3 (sidebar) -->

            <!-- ===================================================== -->
            <!-- CONTENU PRINCIPAL                                      -->
            <!-- ===================================================== -->
            <main class="col-md-9">

                <!-- ================================================= -->
                <!-- DÉTAILS DE LA SALLE                                -->
                <!-- ================================================= -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3>Détails de la salle</h3>
                        <p><strong>Nom :</strong> <?= $salle['nomS']; ?></p>
                        <p><strong>Emplacement :</strong> <?= $salle['lieus']; ?></p>
                        <p><strong>Capacité :</strong> <?= $salle['capacite']; ?></p>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- CRÉNEAUX DE RÉSERVATION                            -->
                <!-- ================================================= -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4>Créneaux de réservation</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure de début</th>
                                    <th>Heure de fin</th>
                                    <th>Réservé par</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-07-01</td>
                                    <td>09:00</td>
                                    <td>11:00</td>
                                    <td>Entreprise A</td>
                                </tr>
                                <tr>
                                    <td>2024-07-01</td>
                                    <td>13:00</td>
                                    <td>15:00</td>
                                    <td>Entreprise B</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- ONGLETS                                             -->
                <!-- ================================================= -->
                <ul class="nav nav-tabs" id="salleTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#analyse">Analyse</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alertes">Alertes</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#Vuedensemble">Vue d'ensemble</button>
                    </li>
                </ul>

                <div class="tab-content mt-4">

                    <!-- ============================================= -->
                    <!-- ONGLET ANALYSE                                 -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade show active" id="analyse">

                        <h4 class="mb-3">Machines</h4>

                        <div class="row g-3">
                            <?php foreach ($machines as $machine): ?>
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">

                                        <h5 class="card-title"><?= $machine['noM']; ?></h5>
                                        <p><strong>Température max :</strong> <?= $machine['valeurMaxTemp']; ?>°C</p>
                                        <p><strong>Voltage max :</strong> <?= $machine['valeurMaxWatt']; ?>V</p>

                                        <h6>Capteurs associés</h6>

                                        <?php foreach ($capteurs as $capteur): ?>
                                            <?php if ($capteur['idM'] == $machine['idM']): ?>
                                                
                                                <div class="mb-3 p-2 border rounded bg-light">
                                                    <?php if ($capteur ['nomT'] == 'Multi-Sensor'): ?>
                                                        <p><strong>Type :</strong> <?= $capteur['nomT']; ?></p>
                                                        <?php $d = isset($temperatureData[$capteur['idCap']]) ? $temperatureData[$capteur['idCap']] : null; ?>
                                                        <?php if($d['valeurT'] !== null): ?>
                                                            
                                                            <p><strong>Température :</strong> <span id="valeurt_<?= $capteur['idCap'] ?>"><?= $d ? $d['valeurT'] : 'N/A'; ?></span> °C</p>
                                                            <p><strong>Moyenne du jour :</strong> <span id="moyennet_<?= $capteur['idCap'] ?>"><?= $d ? $d['moyenneT'] : 'N/A'; ?></span> °C</p>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ($capteur ['nomT'] == 'Prise Intelligente'): ?>
                                                        <p><strong>Type :</strong> <?= $capteur['nomT']; ?></p>
                                                        <?php $d = isset($voltageData[$capteur['idCap']]) ? $voltageData[$capteur['idCap']] : null; ?>
                                                        <?php if ($d['valeurV'] != null): ?>
                                                            <p><strong>Voltage :</strong> <span id="valeurv_<?= $capteur['idCap'] ?>"><?= $d ? $d['valeurV'] : 'N/A'; ?></span> V</p>
                                                            <p><strong>Moyenne du jour :</strong> <span id="moyennev_<?= $capteur['idCap'] ?>"><?= $d ? $d['moyenneV'] : 'N/A'; ?></span> V</p>
                                                    
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <div class="graph-placeholder bg-secondary text-white text-center py-2 rounded">
                                                        Graphique du capteur
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    <!-- fin onglet analyse -->

                    <!-- ============================================= -->
                    <!-- ONGLET ALERTES                                 -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade" id="alertes">

                        <h4 class="mb-3">Résumé des alertes</h4>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <canvas id="graph_alertes"></canvas>
                            </div>
                        </div>

                        <h4 class="mb-3">Alertes à résoudre</h4>
                        <div class="list-group">
                            <?php foreach ($alertes as $alerte): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Numéro de l'alerte : <?= $alerte['idA']; ?></h5>
                                    <p><strong>ID machine :</strong> <?= $alerte['idM']; ?></p>
                                    <p><strong>Date :</strong> <?= $alerte['date']; ?></p>
                                    <p><strong>Valeur :</strong> <?= $alerte['ValeurA']; ?></p>
                                </div>
                                <form method="POST" action="resoudre_alerte.php">
                                    <input type="hidden" name="id" value="<?= $alerte['idA']; ?>">
                                    <button class="btn btn-danger btn-sm">Résoudre</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    <!-- fin onglet alertes -->

                    <!-- ============================================= -->
                    <!-- ONGLET DIAGNOSTIC                              -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade" id="Vuedensemble">
                        <h4 class="mb-3">État de la salle</h4>
                        <div class="row g-3">

                            <!-- Porte -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Porte</h5>
                                        <?php foreach ($capteurs as $capteur): ?>
                                            <?php if ($capteur['nomT'] == 'Porte'): ?>
                                                <p><strong>État : </strong>
                                                <span id="etat-porte"><?= $capteur['etatT'] ?></span>
                                                </p>

                                    
                                            <?php endif; ?>
                                        
                                        <?php endforeach; ?>
                                       
                                    </div>
                                </div>
                            </div>

                            <!-- Climatisation -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Climatisation</h5>
                                        <p><strong>État :</strong>
                                            <?= $diagnostic['clim'] ? 'Allumée' : 'Éteinte'; ?>
                                        </p>
                                        <form method="POST" action="toggle_clim.php">
                                            <button class="btn btn-primary btn-sm">Basculer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Vestiaires -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Vestiaires</h5>
                                        <p><strong>Compteur :</strong>
                                            <?= $diagnostic['vestiaires']; ?> personnes
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- fin onglet diagnostic -->

                </div>
                <!-- fin tab-content -->

            </main>
            <!-- fin col-md-9 (main) -->

        </div>
        <!-- fin row -->

    </div>
    <!-- fin container -->

    <!-- ============================================================ -->
    <!-- POPUP ALERTES                                                 -->
    <!-- ============================================================ -->
    <div class="modal fade" id="alerteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Alerte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="alerteMessage"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const currentSalleId = <?= isset($_GET['id']) ? (int)$_GET['id'] : (int)$salle['idSalle']; ?>;
        let lastId = <?= $maxAlertId; ?>;
    </script>
    <script src="java/salle3.js"></script>

</body>
</html>