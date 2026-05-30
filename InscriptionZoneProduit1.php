<?php require_once 'php/connectdb.php'; ?>
<?php
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }
 function createDevis($connexion) {
    $idPro = $_SESSION['idPro']; // Retrieve the ID from session
    $query0= "SELECT idDev FROM devis WHERE idPro='$idPro' and dateP >= DATE_SUB(NOW(), INTERVAL 1 DAY)";// regarder s'il existe déjà un devis pour ce propriétaire datant de moins de 24h
    $result0 = mysqli_query($connexion, $query0);
    if ($result0 && mysqli_num_rows($result0) > 0) {
        $row = mysqli_fetch_assoc($result0);
        $idDev = $row['idDev'];
        $_SESSION['idDev'] = $idDev; // Store the existing ID in session
        return $row['idDev']; // Return existing idDev
    }
    

    else {
        $query= "INSERT INTO DEVIS (idPro, dateP) VALUES ('$idPro', NOW())";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("Erreur lors de la création du devis : " . mysqli_error($connexion));
        }
       
        $idDev= mysqli_insert_id($connexion);
        $_SESSION['idDev'] = $idDev; // Store the new ID in session for later use
        return mysqli_insert_id($connexion);
    }
 }
 function getZones($connexion, $idDev) {
    //les produits sélectionnés pour chaque salle
    $idPro = $_SESSION['idPro']; // Retrieve the ID from session
    $idSalle = $_SESSION['idSalle'];
    $selections = isset($_POST['selections']) ? $_POST['selections'] : [];
    $zone = array_keys($selections);

    $compteur_bouton = 0;
    $compteur_multisensor = 0;
    $compteur_porte = 0;
    $compteur_prises = 0;

    foreach ($zone as $z) {
        if (empty($z)) continue;

        // Insérer la zone
        $query1  = "INSERT INTO zone (nomZone, idSalle) VALUES ('$z', '$idSalle')";
        $resultat1 = mysqli_query($connexion, $query1);
        if (!$resultat1) {
            die("Erreur lors de l'insertion de la zone : " . mysqli_error($connexion));
        }
        $idZone = mysqli_insert_id($connexion);

        // Vérifier s'il y a des produits sélectionnés pour cette zone
        if (!isset($selections[$z])) continue;

        // Insérer les produits sélectionnés pour cette zone
        foreach ($selections[$z] as $productId => $quantity) {
            if (empty($quantity)) continue;

            $quantity    = (int)$quantity;
            $productId   = (int)$productId;

            // Insérer chaque capteur individuellement
            for ($i = 0; $i < $quantity; $i++) {
                if ($productId == 1){
                    $bouton= "INSERT INTO bouton (etatb, prix, idSalle)
                        VALUES (0, 0, '$idSalle')";
                    $resultatBouton = mysqli_query($connexion, $bouton);
                    if (!$resultatBouton) {
                        die("Erreur lors de l'insertion du bouton : " . mysqli_error($connexion));
                    }
                } else {
                    $q = "INSERT INTO capteurs (lieuCAP, idT, idZone, idSalle)
                          VALUES ('$z', '$productId', '$idZone', '$idSalle')";

                    $resultat2 = mysqli_query($connexion, $q);
                    if (!$resultat2) {
                        die("Erreur lors de l'insertion du capteur : " . mysqli_error($connexion));
                    }
                }
            }


            // Mettre à jour les compteurs
            if ($productId == 1) $compteur_bouton += $quantity;
            if ($productId == 2) $compteur_multisensor += $quantity;
            if ($productId == 3) $compteur_porte += $quantity;
            if ($productId == 4) $compteur_prises += $quantity;
            
        }
    }
        if ($compteur_bouton > 0) {

            $query5 = "INSERT INTO acheter(qant, idT, idSalle,idDev) VALUES ('$compteur_bouton', 1, '$idSalle', '$idDev')";
            $resultat5=mysqli_query($connexion, $query5);
            if (!$resultat5) {
                die("Erreur lors de l'insertion du bouton : " . mysqli_error($connexion));
                }
        }
        if ($compteur_multisensor > 0) {
            $query6 = "INSERT INTO acheter(qant, idT, idSalle,idDev) VALUES ('$compteur_multisensor', 2, '$idSalle', '$idDev')";
            $resultat6=mysqli_query($connexion, $query6);
            if (!$resultat6) {
                die("Erreur lors de l'insertion du multisensor : " . mysqli_error($connexion));
            }
        }
        if ($compteur_porte > 0) {
            $query7 = "INSERT INTO acheter(qant, idT, idSalle,idDev) VALUES ('$compteur_porte', 3, '$idSalle', '$idDev')";
            $resultat7= mysqli_query($connexion, $query7);
            if (!$resultat7) {
                die("Erreur lors de l'insertion de la porte : " . mysqli_error($connexion));
            }
        }
        if ($compteur_prises > 0) {
            $query8 = "INSERT INTO acheter(qant, idT, idSalle, idDev) VALUES ('$compteur_prises', 4, '$idSalle', '$idDev')";
            $resultat8= mysqli_query($connexion, $query8);
            if (!$resultat8) {
                die("Erreur lors de l'insertion des prises : " . mysqli_error($connexion));
            }
        }

    header('Location: Abonnementtest.php');

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
    <link rel="stylesheet" href="css/InscriptionZoneProduit.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
    <a class="navbar-brand" href="#">WaveTrack</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Produits</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            <li class="nav-item"><a class="nav-link" href="#">O</a></li>
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

<div class="container mt-5">
    <h4 class="mb-3">Comment choisir une zone ? :</h4>
    <div class="gif-placeholder mb-4">
        Y'aura bientôt un Tuto qui va arriver
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
            <button type="submit" class="btn btn-secondary" href="InscriptionSalle.html">Ajouter une autre salle</button>
            <button type="submit" class="btn btn-secondary">Valider</button>
        
        </div>

    </form>
</div>
<script src="java/InscriptionZoneProduit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>