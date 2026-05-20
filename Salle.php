<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Connexion MySQL
$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur MySQL : " . mysqli_connect_error());
}

/* ----------------------
   LOGIQUE PLANNING
----------------------- */

function getMonday($date = null) {
    if (!$date) $date = date("Y-m-d");
    $ts = strtotime($date);
    $dow = date('N', $ts); // 1 = lundi
    return date("Y-m-d", strtotime("-" . ($dow - 1) . " days", $ts));
}

$monday = isset($_GET['monday']) ? $_GET['monday'] : getMonday();
$sunday = date("Y-m-d", strtotime("$monday +6 days"));
$prevMonday = date("Y-m-d", strtotime("$monday -7 days"));
$nextMonday = date("Y-m-d", strtotime("$monday +7 days"));

$errorMessage = "";

/* ----------------------
   TRAITEMENT FORMULAIRES
----------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    if ($action === "add") {
        $idArt = isset($_POST["idArt"]) ? (int)$_POST["idArt"] : 0;
        $date = isset($_POST["date"]) ? $_POST["date"] : "";
        $startHour = isset($_POST["startHour"]) ? (int)$_POST["startHour"] : -1;

        if ($idArt <= 0 || $date === "" || $startHour < 0) {
            $errorMessage = "Tous les champs sont obligatoires.";
        } else {
            $heureDeb = sprintf("%02d:00:00", $startHour);
            $heureFin = sprintf("%02d:00:00", $startHour + 2);

            // Vérifier chevauchement
            $sqlCheck = "
                SELECT * FROM creneaux
                WHERE date = ?
                  AND NOT (? <= heureDeb OR ? >= heureFin)
            ";
            $stmt = mysqli_prepare($conn, $sqlCheck);
            mysqli_stmt_bind_param($stmt, "sss", $date, $heureDeb, $heureFin);
            mysqli_stmt_execute($stmt);
            $resCheck = mysqli_stmt_get_result($stmt);

            if ($resCheck && mysqli_num_rows($resCheck) > 0) {
                $errorMessage = "Chevauchement détecté avec un autre créneau.";
            } else {
                $sqlInsert = "INSERT INTO creneaux (heureDeb, heureFin, date, idArt)
                              VALUES (?, ?, ?, ?)";
                $stmt2 = mysqli_prepare($conn, $sqlInsert);
                mysqli_stmt_bind_param($stmt2, "sssi", $heureDeb, $heureFin, $date, $idArt);
                if (!mysqli_stmt_execute($stmt2)) {
                    $errorMessage = "Erreur lors de l'ajout du créneau.";
                } else {
                    header("Location: Salle.php?monday=" . urlencode($monday) . "#tab-planning");
                    exit;
                }
            }
        }
    };

    if ($action === "edit") {
        $idC = isset($_POST["idC"]) ? (int)$_POST["idC"] : 0;
        $idArt = isset($_POST["idArt"]) ? (int)$_POST["idArt"] : 0;
        $date = isset($_POST["date"]) ? $_POST["date"] : "";
        $startHour = isset($_POST["startHour"]) ? (int)$_POST["startHour"] : -1;

        if ($idC <= 0 || $idArt <= 0 || $date === "" || $startHour < 0) {
            $errorMessage = "Champs invalides.";
        } else {
            $heureDeb = sprintf("%02d:00:00", $startHour);
            $heureFin = sprintf("%02d:00:00", $startHour + 2);

            // Vérifier chevauchement
            $sqlCheck = "
                SELECT * FROM creneaux
                WHERE date = ?
                AND idC <> ?
                AND NOT (? <= heureDeb OR ? >= heureFin)
            ";
            $stmt = mysqli_prepare($conn, $sqlCheck);
            mysqli_stmt_bind_param($stmt, "siss", $date, $idC, $heureDeb, $heureFin);
            mysqli_stmt_execute($stmt);
            $resCheck = mysqli_stmt_get_result($stmt);

            if ($resCheck && mysqli_num_rows($resCheck) > 0) {
                $errorMessage = "Chevauchement détecté.";
            } else {
                $sqlUpdate = "
                    UPDATE creneaux
                    SET heureDeb=?, heureFin=?, date=?, idArt=?
                    WHERE idC=?
                ";
                $stmt2 = mysqli_prepare($conn, $sqlUpdate);
                mysqli_stmt_bind_param($stmt2, "sssii", $heureDeb, $heureFin, $date, $idArt, $idC);
                mysqli_stmt_execute($stmt2);

                header("Location: Salle.php?monday=" . urlencode($monday) . "#tab-planning");
                exit;
            }
        }
    }

    if ($action === "delete") {
        $idC = isset($_POST["idC"]) ? (int)$_POST["idC"] : 0;

        if ($idC > 0) {
            mysqli_query($conn, "DELETE FROM creneaux WHERE idC=$idC");
        }

        header("Location: Salle.php?monday=" . urlencode($monday) . "#tab-planning");
        exit;
    }

}


/* ----------------------
   CHARGEMENT ARTISTES
----------------------- */

$artists = [];
$resArtists = mysqli_query($conn, "SELECT idArt, nom FROM artiste ORDER BY nom ASC");
if ($resArtists === false) {
    die("Erreur SQL artistes : " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($resArtists)) {
    $artists[] = $row;
}

/* ----------------------
   CHARGEMENT CRENEAUX
----------------------- */

$sqlSlots = "
    SELECT c.idC, c.date, c.heureDeb, c.heureFin, c.idArt, a.nom
    FROM creneaux c
    JOIN artiste a ON a.idArt = c.idArt
    WHERE c.date BETWEEN '$monday' AND '$sunday'
    ORDER BY c.date, c.heureDeb
";
$resSlots = mysqli_query($conn, $sqlSlots);
if ($resSlots === false) {
    die("Erreur SQL créneaux : " . mysqli_error($conn));
}

$slots = [];
while ($row = mysqli_fetch_assoc($resSlots)) {
    $slots[] = [
        "idC"       => (int)$row["idC"],
        "artist"    => $row["nom"],
        "date"      => $row["date"],
        "startHour" => (int)substr($row["heureDeb"], 0, 2),
        "idArt"     => (int)$row["idArt"]
    ];
}

/* ----------------------
   SIMULATIONS EXISTANTES
----------------------- */

// Simulation des salles du propriétaire
$salles = [
    ['id' => 1, 'nom' => 'Salle 1', 'adresse' => '--- rue -- --------------, Toulouse, 31---'],
    ['id' => 2, 'nom' => 'Salle 2', 'adresse' => '--- avenue -- --------------, Toulouse, 31---'],
];

$salleId = isset($_GET['salle']) ? (int)$_GET['salle'] : $salles[0]['id'];
$salleCourante = null;
foreach ($salles as $salle) {
    if ($salle['id'] === $salleId) {
        $salleCourante = $salle;
        break;
    }
}
if (!$salleCourante) {
    $salleCourante = $salles[0];
}

// --- Simulation de données capteurs ---
$temperatureActuelle = 22;
$temperatureMoyenneJour = 20;
$portesOuverturesJour = 14;
$porteEtat = "Fermée";
$humidite = 45;
$son = 32;

// --- Simulation d’alertes ---
$alertes = [
    ['type' => 'Surchauffe', 'message' => 'Température supérieure à 28°C', 'niveau' => 'Élevé', 'date' => '2026-04-16 21:32'],
    ['type' => 'Porte ouverte', 'message' => 'Porte principale ouverte après fermeture', 'niveau' => 'Moyen', 'date' => '2026-04-15 02:10'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>WaveTrack - Salle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CSS perso -->
    <link rel="stylesheet" href="css/salle.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

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

<main>
    <div class="conteneur">
        <aside class="colonne-gauche">
            <div class="menu">
                <h1 class="onglet">Salles</h1>
            </div>
            <div class="sousmenu">
                <?php foreach ($salles as $salle): ?>
                    <a href="salle.php?salle=<?= $salle['id'] ?>"
                       class="sous-onglet <?= $salle['id'] === $salleCourante['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($salle['nom']) ?>
                    </a>
                <?php endforeach; ?>
                <a href="ajouter_salle.php" class="sous-onglet ajouter">+ Ajouter une salle</a>
            </div>
        </aside>

        <section class="infos">
            <div class="header-salle">
                <div>
                    <h1><?= htmlspecialchars($salleCourante['nom']) ?></h1>
                    <p class="adresse"><?= htmlspecialchars($salleCourante['adresse']) ?></p>
                    <p class="maj">Dernière mise à jour : il y a 5 min</p>
                </div>
                <button class="edit-btn">Modifier la salle</button>
            </div>

            <!-- Slider -->
            <div class="slider">
                <input type="radio" name="slider" id="s1" checked>
                <input type="radio" name="slider" id="s2">
                <input type="radio" name="slider" id="s3">
                <input type="radio" name="slider" id="s4">
                <input type="radio" name="slider" id="s5">

                <div class="slides">
                    <div class="slide" style="background-image:url('img/imgSalle-1.png');"></div>
                    <div class="slide" style="background-image:url('img/imgSalle-2.png');"></div>
                    <div class="slide" style="background-image:url('img/imgSalle-3.png');"></div>
                    <div class="slide" style="background-image:url('img/imgSalle-4.png');"></div>
                    <div class="slide" style="background-image:url('img/imgSalle-5.png');"></div>
                </div>

                <div class="thumbnails">
                    <label for="s1" style="background-image:url('img/imgSalle-1.png');"></label>
                    <label for="s2" style="background-image:url('img/imgSalle-2.png');"></label>
                    <label for="s3" style="background-image:url('img/imgSalle-3.png');"></label>
                    <label for="s4" style="background-image:url('img/imgSalle-4.png');"></label>
                    <label for="s5" style="background-image:url('img/imgSalle-5.png');"></label>
                </div>
            </div>

            <!-- Onglets -->
            <div class="tabs">
                <button class="tab-btn active" data-tab="analyse">Analyse</button>
                <button class="tab-btn" data-tab="planning">Planning</button>
                <button class="tab-btn" data-tab="alertes">Alertes</button>
            </div>

            <!-- Analyse -->
            <div class="tab-content active" id="tab-analyse">
                <div class="cards">
                    <div class="card">
                        <h3>Température</h3>
                        <p><strong>Actuelle :</strong> <?= $temperatureActuelle ?> °C</p>
                        <p><strong>Moyenne du jour :</strong> <?= $temperatureMoyenneJour ?> °C</p>
                        <div class="mini-graph">Graphique à venir</div>
                    </div>

                    <div class="card">
                        <h3>Portes</h3>
                        <p><strong>Ouvertures aujourd’hui :</strong> <?= $portesOuverturesJour ?></p>
                        <p><strong>État actuel :</strong> <?= $porteEtat ?></p>
                    </div>

                    <div class="card">
                        <h3>Autres capteurs</h3>
                        <p><strong>Humidité :</strong> <?= $humidite ?> %</p>
                        <p><strong>Niveau sonore :</strong> <?= $son ?> dB</p>
                    </div>
                </div>
            </div>

            <!-- Planning -->
            <div class="tab-content" id="tab-planning">
                <div class="planning-header">
                    <h2>Planning</h2>
                    <div class="planning-nav">
                        <a href="Salle.php?monday=<?= urlencode($prevMonday) ?>#tab-planning" class="boutton">« Semaine précédente</a>
                        <span>
                            Semaine du <?= date("d/m/Y", strtotime($monday)) ?>
                            au <?= date("d/m/Y", strtotime($sunday)) ?>
                        </span>
                        <a href="Salle.php?monday=<?= urlencode($nextMonday) ?>#tab-planning" class="boutton">Semaine suivante »</a>
                    </div>
                    <button class="boutton" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter un créneau</button>
                </div>

                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger mt-2"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <div class="timetable-container mt-3">
                    <table id="timetable">
                        <thead>
                            <tr>
                                <th class="time-col">Heure</th>
                                <!-- Les jours seront ajoutés en JS -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les lignes horaires + créneaux seront ajoutés en JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Alertes -->
            <div class="tab-content" id="tab-alertes">
                <h2>Alertes</h2>
                <?php if (empty($alertes)): ?>
                    <p>Aucune alerte pour le moment.</p>
                <?php else: ?>
                    <ul class="liste-alertes">
                        <?php foreach ($alertes as $a): ?>
                            <li class="alerte alerte-<?= strtolower($a['niveau']) ?>">
                                <div class="alerte-header">
                                    <span class="type"><?= htmlspecialchars($a['type']) ?></span>
                                    <span class="niveau"><?= htmlspecialchars($a['niveau']) ?></span>
                                </div>
                                <p><?= htmlspecialchars($a['message']) ?></p>
                                <span class="date"><?= htmlspecialchars($a['date']) ?></span>
                                <button class="btn-resolu">Marquer comme résolue</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </section>
    </div>
</main>

<!-- Modal ajout créneau -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="date" id="addDate">
            <input type="hidden" name="startHour" id="addStartHour">

            <div class="modal-header">
                <h5 class="modal-title">Ajouter un créneau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Artiste</label>
                    <select name="idArt" class="form-select" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($artists as $a): ?>
                            <option value="<?= (int)$a["idArt"] ?>"><?= htmlspecialchars($a["nom"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Jour</label>
                    <select id="addDay" class="form-select"></select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Heure de début</label>
                    <select id="addHour" class="form-select"></select>
                    <div class="form-text">Chaque créneau dure 2h automatiquement.</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="ModifModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="idC" id="editIdC">

      <div class="modal-header">
        <h5 class="modal-title">Modifier le créneau</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Artiste</label>
          <select name="idArt" class="form-select" required>
            <?php foreach ($artists as $a): ?>
              <option value="<?= (int)$a["idArt"] ?>"><?= htmlspecialchars($a["nom"]) ?></option>
            <?php endforeach; ?>
          </select>
          
          <div class="mb-2">
            <label class="form-label">Jour</label>
            <select id="addDay" class="form-select">
                <?php foreach ($creneaux as $c): ?>
                    <option value="<?= (int)$c["idC"] ?>"><?= htmlspecialchars($c["date"]) ?></option>
                <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Heure de début</label>
            <select id="addHour" class="form-select"></select>
            <div class="form-text">Chaque créneau dure 2h automatiquement.</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="SuppModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="idC" id="deleteIdC">

      <div class="modal-header">
        <h5 class="modal-title">Supprimer le créneau</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Es-tu sûre de vouloir supprimer ce créneau ?</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-danger">Supprimer</button>
      </div>
    </form>
  </div>
</div>

<footer>
    <p>
        Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia -
        <a href="Contactapres.html" class="contactfooter">Nos Contacts</a>
    </p>
</footer>

<!-- Variables PHP → JS -->
<script>
    window.PLANNING_SLOTS = <?= json_encode($slots) ?>;
    window.PLANNING_MONDAY = "<?= $monday ?>";
</script>

<!-- JS -->
<script src="java/salle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
