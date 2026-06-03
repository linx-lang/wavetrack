<?php require_once 'php/connectdb.php'; ?>
<?php
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }
 
 function createDevis($connexion) {
    $idPro = $_SESSION['idPro'];
    $query0 = "SELECT idDev FROM devis WHERE idPro='$idPro' AND dateP >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $result0 = mysqli_query($connexion, $query0);
    if ($result0 && mysqli_num_rows($result0) > 0) {
        $row = mysqli_fetch_assoc($result0);
        $_SESSION['idDev'] = $row['idDev'];
        return $row['idDev'];
    } else {
        $query = "INSERT INTO DEVIS (idPro, dateP) VALUES ('$idPro', NOW())";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("Erreur lors de la création du devis : " . mysqli_error($connexion));
        }
        $idDev = mysqli_insert_id($connexion);
        $_SESSION['idDev'] = $idDev;
        return $idDev;
    }
 }
 
 function savePorteInfo($connexion, $idSalle, $idZone, $zoneName) {
    // Récupérer la réponse pour cette zone spécifique : "oui" ou "non"
    $porteZone = isset($_POST['porte_zone'][$zoneName]) ? $_POST['porte_zone'][$zoneName] : 'non';
    $aUnePorte = ($porteZone === 'oui') ? 1 : 0;
 
    if ($aUnePorte) {
        $query = "INSERT INTO porte (idSalle, idZone) VALUES ('$idSalle', '$idZone')";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("Erreur lors de l'insertion de la porte : " . mysqli_error($connexion));
        }
    }
 }
 
 function getZones($connexion, $idDev) {
    $idSalle     = $_SESSION['idSalle'];
    $selections  = isset($_POST['selections']) ? $_POST['selections'] : [];
    $zone        = array_keys($selections);
 
    $compteur_bouton      = 0;
    $compteur_multisensor = 0;
    $compteur_porte       = 0;
    $compteur_prises      = 0;
 
    foreach ($zone as $z) {
        if (empty($z)) continue;
 
        // 1. Insérer la zone
        $query1    = "INSERT INTO zone (nomZone, idSalle) VALUES ('$z', '$idSalle')";
        $resultat1 = mysqli_query($connexion, $query1);
        if (!$resultat1) {
            die("Erreur lors de l'insertion de la zone : " . mysqli_error($connexion));
        }
        $idZone = mysqli_insert_id($connexion);
 
        // 2. Sauvegarder l'info porte pour cette zone (maintenant qu'on a $idZone)
        savePorteInfo($connexion, $idSalle, $idZone, $z);
 
        if (!isset($selections[$z])) continue;
 
        foreach ($selections[$z] as $productId => $quantity) {
            if (empty($quantity)) continue;
 
            $quantity  = (int)$quantity;
            $productId = (int)$productId;
 
            for ($i = 0; $i < $quantity; $i++) {
                if ($productId == 1) {
                    $qBouton = "INSERT INTO bouton (etatb, prix, idSalle) VALUES (0, 0, '$idSalle')";
                    if (!mysqli_query($connexion, $qBouton)) {
                        die("Erreur lors de l'insertion du bouton : " . mysqli_error($connexion));
                    }
                } else {
                    $qCapteur = "INSERT INTO capteurs (lieuCAP, idT, idZone, idSalle)
                                 VALUES ('$z', '$productId', '$idZone', '$idSalle')";
                    if (!mysqli_query($connexion, $qCapteur)) {
                        die("Erreur lors de l'insertion du capteur : " . mysqli_error($connexion));
                    }
                }
            }
 
            if ($productId == 1) $compteur_bouton      += $quantity;
            if ($productId == 2) $compteur_multisensor += $quantity;
            if ($productId == 3) $compteur_porte       += $quantity;
            if ($productId == 4) $compteur_prises      += $quantity;
        }
    }
 
    if ($compteur_bouton > 0) {
        $q5 = "INSERT INTO acheter (qant, idT, idSalle, idDev) VALUES ('$compteur_bouton', 1, '$idSalle', '$idDev')";
        if (!mysqli_query($connexion, $q5)) die("Erreur acheter bouton : " . mysqli_error($connexion));
    }
    if ($compteur_multisensor > 0) {
        $q6 = "INSERT INTO acheter (qant, idT, idSalle, idDev) VALUES ('$compteur_multisensor', 2, '$idSalle', '$idDev')";
        if (!mysqli_query($connexion, $q6)) die("Erreur acheter multisensor : " . mysqli_error($connexion));
    }
    if ($compteur_porte > 0) {
        $q7 = "INSERT INTO acheter (qant, idT, idSalle, idDev) VALUES ('$compteur_porte', 3, '$idSalle', '$idDev')";
        if (!mysqli_query($connexion, $q7)) die("Erreur acheter porte : " . mysqli_error($connexion));
    }
    if ($compteur_prises > 0) {
        $q8 = "INSERT INTO acheter (qant, idT, idSalle, idDev) VALUES ('$compteur_prises', 4, '$idSalle', '$idDev')";
        if (!mysqli_query($connexion, $q8)) die("Erreur acheter prises : " . mysqli_error($connexion));
    }
 
    header('Location: Abonnement.php');
 }
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDev = createDevis($connexion);
    getZones($connexion, $idDev);
 }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveTrack - Zones & Produits</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/inscriptionZoneProduit.css">
    
    </head>
<body>
 
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <a class="navbar-brand" href="#">WaveTrack</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="nav-links">
        <li><a href="Accueil.html">Accueil</a></li>
        <li>
        <a href="profil.php" class="profil-link">
            <img src="img/user.png" class="user-img">
        </a>
</li>

    </ul>
    </div>
</nav>
 
<div class="container mt-4">
    <div class="row text-center g-3">
        <div class="col-3"><div class="step-box">1</div></div>
        <div class="col-3"><div class="step-box">2</div></div>
        <div class="col-3"><div class="step-box step-active">3 : Zones & Produits</div></div>
        <div class="col-3"><div class="step-box">4</div></div>
    </div>
</div>
 

 
    <form method="POST">
        <div class="row">
            <div class="col-md-3">
                <h3>Zones</h3>
                <p>Sélectionnez une zone :</p>
                <div id="zones-list"></div>
            </div>
            <div class="col-md-6">
                <h3>Produits</h3>
                <p>Configurez les quantités :</p>
                <div id="products-container"></div>
            </div>
            <div class="col-md-3">
                <h3>Récapitulatif</h3>
                <p>Votre sélection :</p>
                <div id="summary-container" class="summary-box">
                    <em>Aucune zone sélectionnée.</em>
                </div>
            </div>
        </div>
 
        <hr class="my-4">
 
        <div class="d-flex justify-content-between">
            
            <a href="InscriptionSalle.php" class="btn btn-secondary">Ajouter une autre salle</a>

            <button type="submit" class="btn btn-secondary">Valider</button>
        </div>
    </form>
</div>
 
<footer>
    <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia -
        <a href="Contactapres.html" class="contactfooter">Nos Contacts</a>
    </p>
</footer>
 <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
 <script src="java/InscriptionZoneProduit.js"></script>
</body>
</html>
