<?php

    function ajouterinscriptionPro($connexion){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
            session_start();
            }
            $nom = $_POST['nomPro'];
            $prenom = $_POST['prenomPro'];
            $email = $_POST['emailPro'];
            $pwd = $_POST['mdpPro'];
            $query = "INSERT INTO proprietaire (nomP, prenomP, mailP, mdpP) VALUES ('" . $nom . "', '" . $prenom . "', '" . $email . "', '" . $pwd . "')";
            mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
        }
    }
        
?>

