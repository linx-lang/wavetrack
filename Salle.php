<?php require("php/connectdb.php"); ?>
<?php
    session_start();
    if (!isset($_SESSION['idPro'])) {
        header("Location: Connexion.html");
        exit();
    }    
    function voirSalle($connexion, $id_salle) {
        $query = "
            SELECT nomS, lieus, capacite FROM salle WHERE idSalle = '$id_salle'
            ";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        return mysqli_fetch_assoc($result);
    }

    $idPro = 7;
    $id_salle = 4; 
    $salle    = voirSalle($connexion, $id_salle);

    // Date Planning

    // Fonction pour obtenir le lundi de la semaine à partir d'une date
    function getLundi($date = null) { // $date = null permet de rendre le paramètre optionnel
        // Si aucune date n'est fournie, on prend la date actuelle
        if (!$date) {
            $date = date("Y-m-d"); 
        }
        $ts = strtotime($date); // Convertir la date en timestamp 
        $jourDeLaSemaine = date('N', $ts); // 1 = lundi, 2 = mardi, etc.
        return date("Y-m-d", strtotime("-" . ($jourDeLaSemaine - 1) . " days", $ts));
    }

    // Récupérer le lundi de la semaine à afficher (soit depuis l'URL, soit par défaut le lundi de la semaine actuelle)
    $lundi = isset($_GET['lundi']) ? $_GET['lundi'] : getLundi();

    // Si on est lundi et qu'aucune date n'est passé en paramètre, 
    // on force l'affichage de la semaine actuelle
    if (date("N") == 1 && !isset($_GET["lundi"])) {
        $lundi = getLundi();
    }

    $dimanche = date("Y-m-d", strtotime("$lundi +6 days"));
    $lundiAvant = date("Y-m-d", strtotime("$lundi -7 days"));
    $lundiApres = date("Y-m-d", strtotime("$lundi +7 days"));

    // Gestion des créneaux (ajout, modification, suppression)

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
        $action = $_POST["action"];

        if ($action === "ajouter") {
            // Récupération des données du formulaire
            $nomArtiste = (string)$_POST["nomArtiste"]; 
            $date = $_POST["date"];
            $heureDebutCreneau = (int)$_POST["heureDebutCreneau"]; // 

            $erreurCreneau = "";
            $idArt = 0;

            $aujourdhui = date("Y-m-d");
            // Si la date choisie est antérieure (inférieure) à la date d'aujourd'hui
            if ($date < $aujourdhui) {
                $erreurCreneau = "Impossible de rajouter ou modifier un créneau sur une date déjà passée.";
            }

            if ($erreurCreneau === "") {

                // Récupérer les horaires d'ouverture du jour

                // Convertit la date choisie en un numéro de jour de la semaine (1=lundi, 2=mardi, etc.)
                $jourSemaine = date("N", strtotime($date)); 
                $sqlOuverture = "
                        SELECT horaireDeb, horaireFin 
                        FROM ouverture 
                        WHERE idSalle = $id_salle AND idj = $jourSemaine
                       ";
                $resOuverture = mysqli_query($connexion, $sqlOuverture); 
                $ouvertureSalle = mysqli_fetch_assoc($resOuverture);
                
                // Si la salle n'a pas d'horaire d'ouverture/ est fermée pour le jour choisi
                if ($ouvertureSalle === false) {
                    $erreurCreneau = "La salle {$salle['nomS']} est fermé ce jour-là. Il est donc impossible de rajouter un créneau.";
                } else {

                    // Convertit les heures d'ouverture et de fermeture en nombre entier
                    $heureOuvSalle = (int)substr($ouvertureSalle["horaireDeb"], 0, 2);
                    $heureFermSalle = (int)substr($ouvertureSalle["horaireFin"], 0, 2);
                    if ($heureFermSalle === 0) {
                        $heureFermSalle = 24;
                    }
                    
                    if ($heureDebutCreneau < $heureOuvSalle || ($heureDebutCreneau + 2) > $heureFermSalle) {
                        $erreurCreneau = "Il est impossible de rajouter un créneau en dehors des heures d'ouverture.";
                    }

                    // Vérifier si l'artiste choisit existe déjà
                    $res = mysqli_query($connexion, "
                        SELECT idArt
                        FROM artiste
                        WHERE nom='$nomArtiste'
                    ");
                    if (mysqli_num_rows($res) > 0) {
                        // Si l'artiste existe, on reprend son idArt
                        $row = mysqli_fetch_assoc($res);
                        $idArt = (int)$row["idArt"];
                    } else {
                        // Sinon, on crée l'artiste dans la base de donnée
                        mysqli_query($connexion, "
                            INSERT INTO artiste (nom) VALUES ('$nomArtiste')
                        ");
                        $idArt = mysqli_insert_id($connexion);
                    }
                }
                if ($erreurCreneau !== "") {
                    // Si il y a une erreur, elle sera affiché avec l'alerte php'
                } else {

                    // Calcul des heures (Formatage pour la base de donnée)
                    $heureDeb = sprintf("%02d:00:00", $heureDebutCreneau);
                    $heureFin = sprintf("%02d:00:00", $heureDebutCreneau + 2);
                    
                    // Eviter le chevauchement de créneaux

                    // On vérifie s'il existe déjà un créneau pour : 
                            //la même salle, 
                            //le même jour 
                            // et qui chevauche les heures de début et fin du nouveau créneau
                    $sqlChevauchement = "
                        SELECT *
                        FROM creneaux
                        WHERE idSalle = $id_salle
                        AND date = '$date'
                        AND (heureDeb < '$heureFin')
                        AND (heureFin > '$heureDeb')
                    ";
                    $resChevauchement = mysqli_query($connexion, $sqlChevauchement);
                    
                    // Si le créneau en chevauche un autre
                    if (mysqli_num_rows($resChevauchement) > 0) {
                        $erreurCreneau = "Impossible de choisir ce créneau car il chevauche un autre créneau déjà existant.";
                        } else { 

                        // Sinon, on ajoute le créneau dans la base de donnée
                        mysqli_query($connexion, "
                            INSERT INTO creneaux (heureDeb, heureFin, date, idArt, idSalle)
                            VALUES ('$heureDeb', '$heureFin', '$date', '$idArt', '$id_salle')
                        ");

                        header("Location: Salle.php?lundi=" . urlencode($lundi));
                        exit;
                    }
                }
            }    
        };

        if ($action === "modifier") {
            // Récupération des données du créneau à modifier
            $idC = (int)$_POST["idC"];
            $nomArtiste = (string)$_POST["nomArtiste"];
            $date = $_POST["date"];
            $heureDebutCreneau = (int)$_POST["heureDebutCreneau"];

            $erreurCreneau = "";
            $idArt = 0;
            $heureDeb = 0;
            $heureFin = 0;

            $aujourdhui = date("Y-m-d");
            if ($date < $aujourdhui) {            
                $erreurCreneau = "Impossible de rajouter ou modifier un créneau dans une date déjà passée.";
            }

            // Récupération des horaires d'ouverture du jour
            if ($erreurCreneau === "") {
                $jourSemaine = date("N", strtotime($date));
                $sqlOuverture = "
                    SELECT horaireDeb, horaireFin 
                    FROM ouverture 
                    WHERE idSalle = $id_salle AND idj = $jourSemaine
                ";
                $resOuverture = mysqli_query($connexion, $sqlOuverture);
                $ouvertureSalle = mysqli_fetch_assoc($resOuverture);
                
                if (!$ouvertureSalle) {
                    $erreurCreneau = "Aucun horaire d'ouverture défini pour ce jour.";
                } else {
                    $heureOuv = (int)substr($ouvertureSalle["horaireDeb"], 0, 2);
                    $heureFerm = (int)substr($ouvertureSalle["horaireFin"], 0, 2);

                    if ($heureFerm === 0) {
                        $heureFerm = 24;
                    }
                    
                    if ($heureDebutCreneau < $heureOuv || ($heureDebutCreneau + 2) > $heureFerm) {
                        $erreurCreneau = "Impossible de rajouter un créneau en dehors des heures d'ouverture.";
                        }

                    // Mettre à jour les heures (Formatage pour la base de donnée)
                    $heureDeb = sprintf("%02d:00:00", $heureDebutCreneau);
                    $heureFin = sprintf("%02d:00:00", $heureDebutCreneau + 2);
                    
                    // Chercher si un artiste a déjà ce nom
                    $res = mysqli_query($connexion, "
                        SELECT idArt 
                        FROM artiste
                        WHERE artiste.nom='$nomArtiste'
                    ");

                    if (mysqli_num_rows($res) > 0) {
                        // Si l'artiste existe alors on prend l'idArt
                        $row = mysqli_fetch_assoc($res);
                        $idArt = (int)$row["idArt"];
                    } else {
                        // Sinon, on crée l'artiste et on prend le nouvel id
                        mysqli_query($connexion, "
                            INSERT INTO artiste (nom) 
                            VALUES ('$nomArtiste')
                        ");
                        $idArt = mysqli_insert_id($connexion);
                    }
                }
                if ($erreurCreneau !== "") {
                    // Si il y a une erreur, elle sera affiché avec l'alerte php
                } else {
                    // Eviter le chevauchement de créneaux
                    
                    // On vérifie s'il existe déjà un créneau pour : 
                            // la même salle, 
                            // le même jour 
                            // et qui chevauche les heures de début et fin du nouveau créneau
                            // On exclut le créneau actuel pour ne pas le comparer à lui-même
                    $sqlChevauchement = "
                        SELECT *
                        FROM creneaux
                        WHERE idSalle = $id_salle
                        AND date = '$date'
                        AND (heureDeb < '$heureFin')
                        AND (heureFin > '$heureDeb')
                        AND idC != $idC
                    ";
                    $resChevauchement = mysqli_query($connexion, $sqlChevauchement);

                    if (mysqli_num_rows($resChevauchement) > 0) {
                        $erreurCreneau = "Impossible de choisir ce créneau car il chevauche un autre créneau déjà existant.";
                    } else { 

                        mysqli_query($connexion, "
                            UPDATE creneaux
                            SET idArt='$idArt', Date='$date', heureDeb='$heureDeb', heureFin='$heureFin'
                            WHERE idC=$idC AND idSalle=$id_salle
                        ");

                        header("Location: Salle.php?lundi=" . urlencode($lundi));
                        exit;
                    }
                }
            }
        };

        if ($action === "supprimer") {
            $idC = (int)$_POST["idC"];

            if ($idC > 0) {
                mysqli_query($connexion, "
                DELETE FROM creneaux WHERE idC=$idC AND idSalle=$id_salle
                ");
            }

            header("Location: Salle.php?lundi=" . urlencode($lundi));
            exit;
        };
    };

    // Chargement des créneaux de la semaine affichée
    
    // Récupération de tous les créneaux de la salle affichée pour la semaine affichée
    $sqlCreneaux = "
        SELECT c.idC, c.date, c.heureDeb, c.heureFin, c.idArt, a.nom
        FROM creneaux c
        JOIN artiste a ON a.idArt = c.idArt
        WHERE c.date BETWEEN '$lundi' AND '$dimanche' AND c.idSalle = $id_salle
        ORDER BY c.date, c.heureDeb
    ";
    $resCreneaux = mysqli_query($connexion, $sqlCreneaux);

    // Formatage des créneaux dans un tableau pour le javascript
    $listeCreneaux = [];
    while ($row = mysqli_fetch_assoc($resCreneaux)) {
        $listeCreneaux[] = [
            "idC"       => (int)$row["idC"],
            "artist"    => (string)$row["nom"],
            "date"      => $row["date"],
            "heureDebutCreneau" => (int)substr($row["heureDeb"], 0, 2),
            "idArt"     => (int)$row["idArt"]
        ];
    }
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
            <div class="planning-header">
                <h2>Planning</h2>
                <div class="planning-nav">
                    <a href="Salle.php?lundi=<?= urlencode($lundiAvant) ?>" class="boutton">« Semaine précédente</a>
                    <span>
                        Semaine du <?= date("d/m/Y", strtotime($lundi)) ?>
                        au <?= date("d/m/Y", strtotime($dimanche)) ?>
                    </span>
                    <a href="Salle.php?lundi=<?= urlencode($lundiApres) ?>" class="boutton">Semaine suivante »</a>
                </div>
                <button class="boutton" data-bs-toggle="modal" data-bs-target="#ajoutModal">Ajouter un créneau</button>
            </div>

            <?php if (!empty($erreurCreneau)) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($erreurCreneau) ?>
                </div>
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
                        <!-- Les lignes horaires & créneaux seront ajoutés en JS -->
                    </tbody>
                </table>
            </div>
    </div>
</main>

<!-- Modal/Pop-up pour ajouter un créneau -->
<div class="modal fade" id="ajoutModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="date" id="ajoutDate">
            <input type="hidden" name="heureDebutCreneau" id="ajoutheureDebutCreneau">

            <div class="modal-header">
                <h5 class="modal-title">Ajouter un créneau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Artiste</label>
                    <input type="text" name="nomArtiste" id="ajoutNomArt" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="ajoutDate" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Heure de début</label>
                    <select name="heureDebutCreneau" id="ajoutheureDebutCreneau" class="form-select" required>
                        <?php for ($h = 0; $h <= 22; $h++): ?>
                        <option value="<?= $h ?>"><?= sprintf("%02d", $h) ?>h</option>
                        <?php endfor; ?>
                    </select>
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

<!-- Modal/Pop-up pour modifier un créneau -->
<div class="modal fade" id="modifModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="modifier">
      <input type="hidden" name="idC" id="modifIdC">

      <div class="modal-header">
        <h5 class="modal-title">Modifier le créneau</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Artiste</label>
                <input type="text" name="nomArtiste" id="modifNomArt" class="form-control" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Date</label>
                <input type="date" name="date" id="modifDate" class="form-control" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Heure de début</label>
                <select name="heureDebutCreneau" id="modifHeure" class="form-select" required>
                    <?php for ($h = 0; $h <= 22; $h++): ?>
                    <option value="<?= $h ?>"><?= sprintf("%02d", $h) ?>h</option>
                    <?php endfor; ?>
                </select>
                <div class="form-text">Chaque créneau dure 2h automatiquement.</div>
            </div>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal/Pop-up pour supprimer un créneau -->
<div class="modal fade" id="suppModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="supprimer">
      <input type="hidden" name="idC" id="suppIdC">

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

<script>
    // Tableaux des heures d'ouverture et fermeture de la salle
    var ouvertureSalleparJourDebut = [];
    var ouvertureSalleparJourFin = [];

    <?php
    // On récupère les horaires d'ouverture de la salle
    // pour chacun des 7 jours (idj = 1 à 7)
    $resultatHorairesJours = mysqli_query($connexion, "
            SELECT idj, horaireDeb, horaireFin 
            FROM ouverture 
            WHERE idSalle=$id_salle
            ");

    // Pour chaque jour, on génère du javascript qui remplit les tableaux.
    while ($horaires = mysqli_fetch_assoc($resultatHorairesJours)) { 
        $jours = $horaires["idj"]; 

        // Convertit les heures d'ouverture et de fermeture en nombre entier
        $heureOuverture = (int)substr($horaires["horaireDeb"], 0, 2);
        $heureFermeture = (int)substr($horaires["horaireFin"], 0, 2);
        if ($heureFermeture === 0) {
            $heureFermeture = 24;
        }

        // On écrit du javascript pour remplir les tableaux ouvertureDeb et ouvertureFin
        echo "ouvertureSalleparJourDebut[$jours] = $heureOuverture;";
        echo "ouvertureSalleparJourFin[$jours] = $heureFermeture;";
    }
    ?>
</script>

<!-- Variables globales contenant : 
    - les créneaux de la semaine affichée (tableau PLANNING_SLOTS)
    - la date du lundi de la semaine affichée (PLANNING_MONDAY)
-->
<script>
    window.PLANNING_SLOTS = <?= json_encode($listeCreneaux) ?>;
    window.PLANNING_MONDAY = "<?= $lundi ?>";
</script>

<script src="java/salle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>