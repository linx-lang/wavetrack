<?php
require_once 'php/connectdb.php';
session_start();

// Si idZone n'est pas défini, on met 0 (toutes les zones)
if (!isset($_POST['idZone'])) {
    $_POST['idZone'] = 0;
}

// FONCTION 1 : Récupérer tous les types
function recupererTousLesTypes($connexion) {
    $listTypes = [];
    $requete  = "SELECT idT, nomT, valeurT FROM type ORDER BY idT ASC";
    $resultat = mysqli_query($connexion, $requete);
    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $listTypes[] = $ligne;
    }
    return $listTypes;
}

// FONCTION 2 : Récupérer toutes les zones du propriétaire
function recupererToutesLesZones($connexion) {
    $listZones = [];
    $requete = "
        SELECT idZone, nomZone
        FROM zone
        WHERE idSalle IN (
            SELECT idSalle FROM salle WHERE idPro = " . $_SESSION['idPro'] . "
        )
        ORDER BY idZone ASC
    ";
    $resultat = mysqli_query($connexion, $requete);
    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $listZones[] = $ligne;
    }
    return $listZones;
}

// FONCTION 3 : Récupérer les machines (toutes ou par zone)
function recupererLesMachines($connexion) {
    $idZone = $_POST['idZone'];
    $machines = [];

    if ($idZone != 0) {
        $requete = "SELECT idM, noM, valeurMaxTemp, valeurMaxWatt, idZone FROM machine WHERE idZone = $idZone";
    } else {
        $requete = "
            SELECT m.idM, m.noM, m.valeurMaxTemp, m.valeurMaxWatt, m.idZone
            FROM machine AS m
            JOIN zone AS z ON m.idZone = z.idZone
            JOIN salle AS s ON z.idSalle = s.idSalle
            WHERE s.idPro = " . $_SESSION['idPro'];
    }

    $resultat = mysqli_query($connexion, $requete);
    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $machines[] = $ligne;
    }
    return $machines;
}

// FONCTION 3bis : Récupérer le climatiseur de la salle (1 max)
function recupererClimatiseur($connexion, $idSalle) {
    $requete = "
        SELECT c.idClim, c.etat, c.idB
        FROM climatisation AS c
        JOIN bouton AS b ON c.idB = b.idB
        WHERE b.idSalle = $idSalle
    ";
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat && mysqli_num_rows($resultat) > 0) {
        return mysqli_fetch_assoc($resultat);
    }
    return null;
}

// FONCTION 4 : Ajouter une machine
function ajouterMachine($connexion) {
    $nomMachine    = $_POST['nomMachine'];
    $valeurMaxWatt = $_POST['valeurMaxWatt'];
    $valeurMaxTemp = $_POST['valeurMaxTemp'];
    $idType        = $_POST['idType'];
    $idZone        = $_POST['idZone'];

    $requeteType = mysqli_query($connexion, "SELECT nomT FROM type WHERE idT = $idType");
    $ligneType   = mysqli_fetch_assoc($requeteType);
    $nomType     = $ligneType['nomT'];

    $nomComplet = $nomType . " - " . $nomMachine;

    mysqli_query($connexion, "
        INSERT INTO machine (noM, valeurMaxTemp, valeurMaxWatt, idZone)
        VALUES ('$nomComplet', '$valeurMaxTemp', '$valeurMaxWatt', '$idZone')
    ");

    header("Location: machine.php");
    exit;
}

// FONCTION 5 : Modifier une machine
function modifierMachine($connexion) {
    $idM           = $_POST['idM'];
    $nomMachine    = $_POST['nomMachine'];
    $valeurMaxWatt = $_POST['valeurMaxWatt'];
    $valeurMaxTemp = $_POST['valeurMaxTemp'];
    $idType        = $_POST['idType'];

    $requeteType = mysqli_query($connexion, "SELECT nomT FROM type WHERE idT = $idType");
    $ligneType   = mysqli_fetch_assoc($requeteType);
    $nomType     = $ligneType['nomT'];

    $nomComplet = $nomType . " - " . $nomMachine;

    mysqli_query($connexion, "
        UPDATE machine
        SET noM = '$nomComplet', valeurMaxTemp = '$valeurMaxTemp', valeurMaxWatt = '$valeurMaxWatt'
        WHERE idM = $idM
    ");

    header("Location: machine.php");
    exit;
}

// FONCTION 6 : Supprimer une machine
function supprimerMachine($connexion) {
    $idM = $_POST['idM'];
    mysqli_query($connexion, "DELETE FROM machine WHERE idM = $idM");
    header("Location: machine.php");
    exit;
}

// FONCTION 7 : Ajouter le climatiseur de la salle (+ son bouton)
function ajouterClimatiseur($connexion) {
    $idSalle = $_POST['idSalle'];

    $resultatMax = mysqli_query($connexion, "SELECT MAX(idB) AS maxId FROM bouton");
    $ligneMax    = mysqli_fetch_assoc($resultatMax);
    $idB         = $ligneMax['maxId'] + 1;

    mysqli_query($connexion, "
        INSERT INTO bouton (idB, etatb, prix, idSalle)
        VALUES ($idB, 0, 67.44, $idSalle)
    ");

    mysqli_query($connexion, "
        INSERT INTO climatisation (etat, idB)
        VALUES (0, $idB)
    ");

    header("Location: machine.php");
    exit;
}

// FONCTION 8 : Supprimer le climatiseur de la salle
function supprimerClimatiseur($connexion) {
    $idClim = $_POST['idClim'];
    mysqli_query($connexion, "DELETE FROM climatisation WHERE idClim = $idClim");
    header("Location: machine.php");
    exit;
}

// POINT D'ENTRÉE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    ajouterMachine($connexion);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifier') {
    modifierMachine($connexion);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    supprimerMachine($connexion);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouterClimatiseur') {
    ajouterClimatiseur($connexion);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimerClimatiseur') {
    supprimerClimatiseur($connexion);
}

$listTypes   = recupererTousLesTypes($connexion);
$listZones   = recupererToutesLesZones($connexion);
$tabMachines = recupererLesMachines($connexion);

// Récupérer l'idSalle du propriétaire connecté
$resultatSalle = mysqli_query($connexion, "SELECT idSalle FROM salle WHERE idPro = " . $_SESSION['idPro']);
$ligneSalle    = mysqli_fetch_assoc($resultatSalle);
$idSalle       = $ligneSalle['idSalle'];

// Récupérer le climatiseur de la salle (null si aucun)
$clim = recupererClimatiseur($connexion, $idSalle);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>WaveTrack - Machines</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/inscriptionZoneProduit.css">
    <link rel="stylesheet" href="css/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="img/rasp.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        <ul class="nav-links">
            <li><a href="#">Accueil</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>

<div class="container mt-5">

    <h3>Machines</h3>

    <!-- ======= Sélecteur de zone ======= -->
    <form method="POST" id="formZone">
        <label class="form-label">Choisir la zone :</label>
        <select name="idZone" id="idZone" class="form-select mb-3" style="max-width: 300px;" onchange="document.getElementById('formZone').submit()">
            <option value="0" <?= ($_POST['idZone'] == 0) ? 'selected' : '' ?>>Toutes les zones</option>
            <?php foreach ($listZones as $uneZone): ?>
                <option value="<?= $uneZone['idZone'] ?>" <?= ($_POST['idZone'] == $uneZone['idZone']) ? 'selected' : '' ?>>
                    <?= $uneZone['nomZone'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="container">

        <!-- ======= Bloc climatiseur (visible seulement sur "Toutes les zones") ======= -->
        <?php if ($_POST['idZone'] == 0): ?>
            <div class="card mb-4" style="max-width: 400px; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px;">
                <p class="mb-3"><strong>Climatiseur de la salle</strong></p>

                <?php if ($clim != null): ?>
                    <!-- Un climatiseur existe : on l'affiche avec un bouton supprimer -->
                    <div class="d-flex align-items-center gap-3">
                        <span>Climatiseur n°<?= $clim['idClim'] ?> — État : <?= ($clim['etat'] == 1) ? 'ON' : 'OFF' ?></span>
                        <form method="POST">
                            <input type="hidden" name="action"  value="supprimerClimatiseur">
                            <input type="hidden" name="idZone"  value="0">
                            <input type="hidden" name="idClim"  value="<?= $clim['idClim'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer le climatiseur ?')">
                                Supprimer
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Pas de climatiseur : on propose d'en ajouter un -->
                    <p class="mb-3">Y a-t-il un climatiseur dans cette salle ?</p>
                    <div class="d-flex gap-2">
                        <form method="POST">
                            <input type="hidden" name="action"  value="ajouterClimatiseur">
                            <input type="hidden" name="idZone"  value="0">
                            <input type="hidden" name="idSalle" value="<?= $idSalle ?>">
                            <button type="submit" class="btn btn-success">Oui</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('reponseNon').style.display='inline'">Non</button>
                        <span id="reponseNon" style="display:none; align-self:center; color:#6c757d;">Aucun climatiseur ajouté.</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- ======= Liste des machines ======= -->
        <h4>Machines enregistrées</h4>

        <?php if (empty($tabMachines)): ?>
            <p><em>Aucune machine enregistrée pour l'instant.</em></p>
        <?php else: ?>
            <ul class="list-group mb-4">
                <?php foreach ($tabMachines as $uneMachine): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= $uneMachine['noM'] ?></span>
                        <div class="d-flex align-items-center gap-3">
                            <strong><?= $uneMachine['valeurMaxTemp'] ?>°C / <?= $uneMachine['valeurMaxWatt'] ?>W</strong>

                            <button class="btn btn-sm btn-warning"
                                onclick="remplirFormModif(<?= $uneMachine['idM'] ?>, '<?= $uneMachine['noM'] ?>', <?= $uneMachine['valeurMaxTemp'] ?>, <?= $uneMachine['valeurMaxWatt'] ?>)">
                                Modifier
                            </button>

                            <form method="POST">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="idZone" value="<?= $_POST['idZone'] ?>">
                                <input type="hidden" name="idM"    value="<?= $uneMachine['idM'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette machine ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- ======= Formulaire d'ajout (visible seulement si une zone est sélectionnée) ======= -->
        <?php if ($_POST['idZone'] != 0): ?>

            <h4>Ajouter une machine</h4>

            <form method="POST" style="max-width: 400px;">
                <input type="hidden" name="action" value="ajouter">
                <input type="hidden" name="idZone" value="<?= $_POST['idZone'] ?>">

                <div class="mb-3">
                    <label class="form-label">Type de capteur</label>
                    <select name="idType" id="idType" class="form-select" required>
                        <option value="">-- Choisir un type --</option>
                        <?php foreach ($listTypes as $unType): ?>
                            <option value="<?= $unType['idT'] ?>"><?= $unType['nomT'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nom de la machine</label>
                    <input type="text" name="nomMachine" class="form-control" placeholder="Ex : Clim Salle 1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valeur maximale (Watt)</label>
                    <input type="number" name="valeurMaxWatt" class="form-control" placeholder="Ex : 120" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valeur maximale (°C)</label>
                    <input type="number" name="valeurMaxTemp" class="form-control" placeholder="Ex : 45" required>
                </div>

                <button type="submit" class="btn btn-primary">Ajouter la machine</button>
            </form>

        <?php else: ?>
            <p class="text-muted"><em>Sélectionnez une zone pour pouvoir ajouter une machine.</em></p>
        <?php endif; ?>

        <!-- ======= Formulaire de modification (caché par défaut) ======= -->
        <div id="formModifContainer" style="display: none; max-width: 400px;">
            <h4>Modifier la machine</h4>
            <form method="POST">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="idZone" value="<?= $_POST['idZone'] ?>">
                <input type="hidden" name="idM" id="modifIdM">

                <div class="mb-3">
                    <label class="form-label">Type de capteur</label>
                    <select name="idType" id="modifIdType" class="form-select" required>
                        <option value="">-- Choisir un type --</option>
                        <?php foreach ($listTypes as $unType): ?>
                            <option value="<?= $unType['idT'] ?>"><?= $unType['nomT'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nom de la machine</label>
                    <input type="text" name="nomMachine" id="modifNom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valeur maximale (Watt)</label>
                    <input type="number" name="valeurMaxWatt" id="modifWatt" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valeur maximale (°C)</label>
                    <input type="number" name="valeurMaxTemp" id="modifTemp" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('formModifContainer').style.display = 'none'">Annuler</button>
            </form>
        </div>

    </div>

    <div style="text-align: right; margin-top: 20px;">
        <a href="salle.php" class="btn btn-secondary">Continuer</a>
    </div>

</div>

<script src="java/machine.js"></script>
</body>
</html>