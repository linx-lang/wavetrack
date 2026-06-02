<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur connexion : " . mysqli_connect_error());
}

if (!isset($_GET['salle'])) {
    die("Aucune salle sélectionnée");
}

$idSalle = (int) $_GET['salle'];

// --- Récupération des infos de la salle ---
$sqlSalle = "SELECT * FROM salle WHERE idSalle = $idSalle";
$resSalle = mysqli_query($conn, $sqlSalle);
$salle = mysqli_fetch_assoc($resSalle);

if (!$salle) {
    die("Salle introuvable");
}

// --- Si le formulaire est envoyé ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = mysqli_real_escape_string($conn, $_POST['nomSalle']);
    $adresse = mysqli_real_escape_string($conn, $_POST['adresseSalle']);

    $sqlUpdate = "UPDATE salle 
                  SET nomSalle='$nom', adresseSalle='$adresse'
                  WHERE idSalle=$idSalle";

    mysqli_query($conn, $sqlUpdate);

    header("Location: salle.php?salle=$idSalle");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Salle</title>
    <link rel="stylesheet" href="css/salle.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <img src="img/1000038818.png" alt="Logo" class="logo-img">
        <span class="nomSite">WaveTrack</span>
    </div>
</nav>

<main class="conteneur">
    <div class="formulaire-profil">
        <h1>Modifier la salle</h1>

        <form method="POST">

            <label>Nom de la salle</label>
            <input type="text" name="nomSalle" value="<?= $salle['nomSalle'] ?>" required>

            <label>Adresse</label>
            <input type="text" name="adresseSalle" value="<?= $salle['adresseSalle'] ?>" required>

            <button type="submit" class="btn-valider">Enregistrer</button>
        </form>
    </div>
</main>

</body>
</html>
