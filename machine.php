<?php
// On connecte la base de données et on démarre la session
require_once 'php/connectdb.php';
session_start();

// FONCTION 1 : Récupérer tous les types depuis la base

function recupererTousLesTypes($connexion) {
 
    $listTypes = []; // tableau vide qui va accueillir les types
 
    $requete  = "SELECT idT, nomT, valeurT FROM type ORDER BY idT ASC";
    $resultat = mysqli_query($connexion, $requete);
 
    // On parcourt chaque ligne et on l'ajoute au tableau
    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $listTypes[] = $ligne;
    }
 
    return $listTypes;
}

function recupererToutesLesZones($connexion) {
 
    $listZones = []; // tableau vide qui va accueillir les zones
 
    $requete  = "
        SELECT idZone, nomZone
        FROM zone
        WHERE idSalle IN (
            SELECT idSalle
            FROM salle
            WHERE idPro = " . $_SESSION['idPro'] . "
        )
        ORDER BY idZone ASC
        ";
    $resultat = mysqli_query($connexion, $requete);
 
    // On parcourt chaque ligne et on l'ajoute au tableau
    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $listZones[] = $ligne;
    }
 
    return $listZones;
}

// FONCTION 2 : Récupérer toutes les machines depuis la base




function recupererToutesLesMachines($connexion) {
    
    if (empty($_POST['idZone'])) {
        return []; // aucune zone choisie → aucune machine affichée
    }

    $idZone = (int) $_POST['idZone'];
    $tabMachines = []; // tableau vide qui va accueillir les machines
 
    $requete  = "
        SELECT idM, noM, valeurMaxTemp, valeurMaxWatt 
        FROM machine
        WHERE idZone = '$idZone' 
        ORDER BY idM ASC";
    
    
    $resultat = mysqli_query($connexion, $requete);

    while ($ligne = mysqli_fetch_assoc($resultat)) {
        $tabMachines[] = $ligne;
    }
 
    return $tabMachines;
}
 

// FONCTION 3 : Ajouter une machine dans la base

function ajouterMachine($connexion) {
 
    // --- Vérification : tous les champs sont-ils remplis ? ---
    if (empty($_POST['nomMachine']) || empty($_POST['valeurMaxTemp']) || empty($_POST['valeurMaxWatt']) || empty($_POST['idZone'])) {
        echo "<script>alert('Merci de remplir tous les champs !');</script>";
        return; // on arrête la fonction ici
    }


    // --- Récupération et nettoyage des données du formulaire ---
    $nomMachine = mysqli_real_escape_string($connexion, $_POST['nomMachine']); // sécurise le texte
    $valeurMaxWatt  = (float) $_POST['valeurMaxWatt']; // convertit en nombre décimal
    $valeurMaxTemp  = (float) $_POST['valeurMaxTemp']; // convertit en nombre décimal
    $idType     = (int)   $_POST['idType'];    // convertit en nombre entier
    $idZone     = (int)   $_POST['idZone'];    // convertit en nombre entier

    // --- On cherche le nom du type choisi pour construire le nom complet ---
    $requeteType = mysqli_query($connexion, "SELECT nomT FROM type WHERE idT = $idType");
    $ligneType   = mysqli_fetch_assoc($requeteType);
    $nomType     = $ligneType['nomT']; // ex : "Climatisation"
 
    // Le nom complet combine le type et le nom saisi, ex : "Climatisation - Clim Salle 1"
    $nomComplet = $nomType . " - " . $nomMachine;
 
    // --- Vérification : cette machine existe-t-elle déjà ? ---
    $verification    = mysqli_query($connexion, "SELECT idM FROM machine WHERE noM = '$nomComplet'");
    $machineExisteDeja = mysqli_num_rows($verification) > 0;
 
    if ($machineExisteDeja) {
        echo "<script>alert('ce capteur est deja branché a la meme machine!');</script>";
        return; // on arrête sans insérer
    }
 
    // --- Tout est bon : on insère la machine en base ---
    mysqli_query($connexion, "
        INSERT INTO machine (noM, valeurMaxTemp, valeurMaxWatt, idZone)
        VALUES ('$nomComplet', '$valeurMaxTemp', '$valeurMaxWatt', '$idZone')
    ");
 
    // Redirige vers la page pour éviter un double envoi si on rafraîchit
    header("Location: machine.php");
    exit;
}
 

// POINT D'ENTRÉE : le formulaire a-t-il été soumis ?

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ajouterMachine($connexion);
}
 
// On récupère les données à afficher dans la page
$listTypes    = recupererTousLesTypes($connexion);
$listZones    = recupererToutesLesZones($connexion);
$tabMachines = recupererToutesLesMachines($connexion);
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
            <li><a href="#">Produits</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">À propos</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>
    
<div class="container mt-5">
 
    <h3>Ajouter une machine</h3>
 
    <form method="POST">
        <div class="row">
 
            <!-- ======= Colonne gauche : formulaire ======= -->
            <div class="col-md-4">
                <h4>Choisir la zone</h4>
                <select name="idZone" id="idZone" class="form-select mb-3" onchange="this.form.submit()">
                    <option value="">-- Choisir une zone --</option>
 
                    <?php foreach ($listZones as $uneZone): ?>
                        <option value="<?= $uneZone['idZone'] ?>"
                            <?= (isset($_POST['idZone']) && $_POST['idZone'] == $uneZone['idZone']) ? 'selected' : '' ?>>
                            <?= $uneZone['nomZone'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <h4>Capteurs</h4>
                <select name="idType" id="idType" class="form-select mb-3">
                    <option value="">-- Choisir un type --</option>
 
                    <?php foreach ($listTypes as $unType): ?>
                        <!-- data-default sert à pré-remplir la valeur max en JS -->
                        <option value="<?= $unType['idT'] ?>" data-default="<?= $unType['valeurT'] ?>">
                            <?= $unType['nomT'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
 
                <h4>Nom de la machine</h4>
                <input type="text" name="nomMachine" class="form-control mb-3" placeholder="Ex : Clim Salle 1">
 
                <h4>Valeur maximale (Watt)</h4>
                <input type="number" name="valeurMaxWatt" id="valeurMaxWatt" class="form-control" placeholder="Ex : 120">
 
                <h4>Valeur maximale (°C)</h4>
                <input type="number" name="valeurMaxTemp" id="valeurMaxTemp" class="form-control" placeholder="Ex : 120">
 
            </div>
 
            <!-- ======= Colonne droite : liste des machines ======= -->
           
<div class="col-md-8">

    <h4>Machines enregistrées</h4>

    <?php if (empty($tabMachines)) { ?>

        <em>Aucune machine enregistrée pour l'instant.</em>

    <?php } else { ?>

        <ul class="list-group">
            <?php foreach ($tabMachines as $uneMachine) { ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= $uneMachine['noM'] ?></span>
                    <strong><?= $uneMachine['valeurMaxTemp'] ?>°C / <?= $uneMachine['valeurMaxWatt'] ?>W</strong>
                </li>
            <?php } ?>
        </ul>

    <?php } ?>

</div>
        <input type="hidden" name="idZone" value="<?php echo isset($_POST['idZone']) ? $_POST['idZone'] : ''; ?>">
        <button type="submit" class="btn btn-primary mt-4">Ajouter la machine</button>
    </form>
</div>
 
<div style="text-align: right; margin-top: 20px;">
    <a href="salle.php" class="btn btn-secondary">Continuer</a>
</div>

<script src="java/machine.js"></script>
 
</body>
</html>