<?php
require 'db.php';

function verifieProfil($connexion, $pseudo, $pwd){

    $pwd = $_POST['pwd'];
    $pseudo = $_POST['pseudo'];

    $query = "select count(*) from Utilisateur where pseudo ='".$pseudo."' and pwd = '".$pwd."'";

    $resultat =  mysqli_query($connexion,$query);

    $ligne = mysqli_fetch_row($resultat);

    if($ligne[0] == 1){

    session_start();
    $_SESSION['pseudo']=$pseudo;
    $_SESSION['message']='';
    header("Location:index.php");
    
    }

    else{
    session_start();
    $_SESSION['message']='Login ou mot de passe incorrect';
    header("Location:login.php");
    }
    exit();
             
}

?>
