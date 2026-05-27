<?php require("php/connectdb.php"); ?>
 <?php
    
    /* 1. Define function first */
    function validerInscription($connexion) {
        error_log("=== START validerInscription ===");
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            echo"bonjour";
            error_log("Session started");}
            //var_dump($_POST);
            //die("Debug stop");
            $nom = $_POST['nomPro'];
            $prenom = $_POST['prenomPro'];
            $email = $_POST['emailPro'];
            $pwd = $_POST['mdpPro'];

            if (empty($nom) || empty($prenom) || empty($email) || empty($pwd)) {
            error_log("STEP 1 FAILED - Missing personal info");
            echo "<p style='color:red'>Erreur : infos personnelles manquantes</p>";
            return;
            }
            

            $query = "INSERT INTO proprietaire (nomP, prenomP, mailP, mdpP) VALUES ('" . $nom . "', '" . $prenom . "', '" . $email . "', '" . $pwd . "')";
            $result=mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            $_SESSION['idPro'] = mysqli_insert_id($connexion); // Store the new ID in session for later use
            header('Location: InscriptionSalle.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validerInscription($connexion);
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveTrack - Inscription</title>
    <link rel="stylesheet" href="css/Inscription.css">
    <link rel="stylesheet" href="css/InscriptionZoneProduit.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
    <

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="InscriptionPro.php" class="p-4 border rounded shadow-sm">
                    <div class="mb-3">
                        <label for="nomPro" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nomPro" name="nomPro" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenomPro" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenomPro" name="prenomPro" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailPro" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailPro" name="emailPro" required>
                    </div>
                    <div class="mb-3">
                        <label for="mdpPro" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mdpPro" name="mdpPro" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Continuer</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia - 
                <a href="Contactavant.html" class="contactfooter">Nos Contacts</a></p>
    </footer>
</body>
</html>