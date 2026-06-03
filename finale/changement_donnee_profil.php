<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur connexion : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPro = $_SESSION['idPro'];

    // Récupérer les nouvelles données
    $nom = mysqli_real_escape_string($conn, $_POST['nomP']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenomP']);
    $mail = mysqli_real_escape_string($conn, $_POST['mailP']);
    $mdp = mysqli_real_escape_string($conn, $_POST['mdpP']);

    // 🔥 UPDATE du propriétaire existant
    $sqlUpdate = "UPDATE proprietaire 
                  SET nomP='$nom', prenomP='$prenom', mailP='$mail', mdpP='$mdp'
                  WHERE idPro=$idPro";

    mysqli_query($conn, $sqlUpdate);

    // Retour au profil
    header("Location: profil.php");
    exit;
}

// Récupération des infos du propriétaire actuel
$idPro = $_SESSION['idPro'];
$sqlPro = "SELECT * FROM proprietaire WHERE idPro = $idPro";
$resPro = mysqli_query($conn, $sqlPro);
$pro = mysqli_fetch_assoc($resPro);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/changement_donnee_profile.css">
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
        <h1>Modifier mon profil</h1>

        <form method="POST">

            <label>Nom</label>
            <input type="text" name="nomP" value="<?= $pro['nomP'] ?>" required>

            <label>Prénom</label>
            <input type="text" name="prenomP" value="<?= $pro['prenomP'] ?>" required>

            <label>Email</label>
            <input type="email" name="mailP" value="<?= $pro['mailP'] ?>" required>

            <label>Mot de passe</label>
            <input type="text" name="mdpP" value="<?= $pro['mdpP'] ?>" required>

            

            <button type="submit" class="btn-valider">Enregistrer</button>
        </form>
    </div>
</main>

</body>
</html>
