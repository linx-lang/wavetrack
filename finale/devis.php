<?php
session_start();

if (!isset($_SESSION['idPro'])) {
    die("Aucun propriétaire connecté");
}

$idPro = (int) $_SESSION['idPro'];

// Connexion BDD
$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur connexion : " . mysqli_connect_error());
}

// Récupération des devis du propriétaire
$sql = "SELECT * FROM devis WHERE idPro = $idPro ORDER BY dateP ASC";
$res = mysqli_query($conn, $sql);

if (!$res) {
    die("Erreur SQL devis : " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>WaveTrack - Mes devis</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/devis.css">

</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <img src="img/1000038818.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        <ul class="nav-links">
            <li><a href="Accueil.html">Accueil</a></li>
            <a href="profil.php"> <img src="img/user.png" class="user-img"> </a>
        </ul>
    </nav>

<main>
    
    <a href="profil.php" class="btn-retour">Retour au profil</a>
    
    <div class="container">
        <h2 style="text-align:center; margin-top:20px;">Historique des devis</h2>
        
        

        <table class="table table-bordered table-striped table-devis">
            <thead>
                <tr>
                    <th scope="col">Numéro Devis</th>
                    <th scope="col">Date</th>
                    <th scope="col">Prix (par mois)</th>
                    <th scope="col">Prix Total (par an)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td scope="row"><?= $d['idDev'] ?></td>
                        <td><?= date("d/m/Y", strtotime($d['dateP'])) ?></td>
                        <td><?= $d['prixMois'] ?: "—" ?> €</td>
                        <td><?= $d['prixClient'] ?: "—" ?> €</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
