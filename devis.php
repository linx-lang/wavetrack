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
$sql = "SELECT * FROM devis WHERE idPro = $idPro ORDER BY dateP DESC";
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
    <link rel="stylesheet" href="css/salle.css">
    <link rel="stylesheet" href="css/header.css">

</head>
<body>

<nav class="navbar">
    <div class="logo">
        <img src="img/rasp.png" class="logo-img">
        <span class="nomSite">WaveTrack</span>
    </div>
</nav>

<main>
    <h1 style="text-align:center; margin-top:20px;">Historique des devis</h1>

    <table class="table-devis">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Prix total</th>
                <th>Prix client</th>
                <th>Prix par mois</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($d = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= $d['idDev'] ?></td>
                    <td><?= $d['dateP'] ?></td>
                    <td><?= $d['prixTot'] ?: "—" ?> €</td>
                    <td><?= $d['prixClient'] ?: "—" ?> €</td>
                    <td><?= $d['prixMois'] ?: "—" ?> €</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="profil.php" class="btn-retour">⬅ Retour au profil</a>

</main>

</body>
</html>
