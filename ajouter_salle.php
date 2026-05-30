<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['idPro'])) {
    die("Aucun propriétaire connecté");
}

$idPro = (int) $_SESSION['idPro'];

// --- Si le formulaire est envoyé ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = mysqli_real_escape_string($conn, $_POST['nomS']);
    $lieu = mysqli_real_escape_string($conn, $_POST['lieus']);
    $capacite = (int) $_POST['capacite'];
    $codePoS = mysqli_real_escape_string($conn, $_POST['codePoS']);

    // INSERT de la salle
    $sqlInsert = "INSERT INTO salle (nomS, lieus, capacite, codePoS, idPro)
                  VALUES ('$nom', '$lieu', $capacite, '$codePoS', $idPro)";
    mysqli_query($conn, $sqlInsert);

    // Récupérer l'id de la nouvelle salle
    $newSalleId = mysqli_insert_id($conn);

    // Redirection vers la page salle
    header("Location: salle.php?salle=$newSalleId");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une salle</title>
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

<main class="conteneur">
    <div class="formulaire-profil">
        <h1>Ajouter une salle</h1>

        <form method="POST">

            <label>Nom de la salle</label>
            <input type="text" name="nomS" required>

            <label>Adresse / Lieu</label>
            <input type="text" name="lieus" required>

            <label>Capacité</label>
            <input type="number" name="capacite" required>

            <label>Code postal</label>
            <input type="text" name="codePoS" required>

            <button type="submit" class="btn-valider">Ajouter</button>
        </form>
    </div>
</main>

</body>
</html>
