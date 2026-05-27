<?php require_once 'php/connectdb.php'; ?>
<?php
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }
 function getZones($connexion) {
    //les produits sélectionnés pour chaque salle
    $idSalle = $_SESSION['idSalle'];
    $zone       = isset($_POST['zone']) ? $_POST['zone'] : [];
    $selections = isset($_POST['selections']) ? $_POST['selections'] : [];
    $compteur_bouton = 0;
    $compteur_multisensor = 0;
    $compteur_porte = 0;
    $compteur_prises = 0;

    foreach ($zone as $z) {
        if (empty($z)) continue;

        // Insérer la zone
        $query  = "INSERT INTO zone (nomZone, idSalle) VALUES ('$z', $idSalle)";
        mysqli_query($connexion, $query);
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
                $q = "INSERT INTO capteurs (lieuCAP, idT, idZone)
                      VALUES ('$z', $productId, $idZone)";
                mysqli_query($connexion, $q);
            }

            // Mettre à jour les compteurs
            if ($productId == 1) $compteur_bouton += $quantity;
            if ($productId == 2) $compteur_multisensor += $quantity;
            if ($productId == 3) $compteur_porte += $quantity;
            if ($productId == 4) $compteur_prises += $quantity;
        }
    }
    
        $query5 = "INSERT INTO acheter(quant, idT, idSalle) VALUES ('$compteur_bouton', 1, $idSalle)";
        mysqli_query($connexion, $query5);
        $query6 = "INSERT INTO acheter(quant, idT, idSalle) VALUES ('$compteur_multisensor', 2, $idSalle)";
        mysqli_query($connexion, $query6);
        $query7 = "INSERT INTO acheter(quant, idT, idSalle) VALUES ('$compteur_porte', 3, $idSalle)";
        mysqli_query($connexion, $query7);
        $query8 = "INSERT INTO acheter(quant, idT, idSalle) VALUES ('$compteur_prises', 4, $idSalle)";
        mysqli_query($connexion, $query8);
}
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        getZones($connexion);
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
            <button type="submit" class="btn btn-primary" href="InscriptionAbonnement.html">Suivant</button>
        </div>

    </form>
</div>
<script src="java/InscriptionZoneProduit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>