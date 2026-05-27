<?php 
require('connectdb.php'); 
require('db.php');
function verifieProfil($connexion, $email, $mdp){

    $query = "SELECT COUNT(*) FROM proprietaire WHERE mailP='".$email."' AND mdpP='".$mdp."'";
    $resultat = mysqli_query($connexion, $query);
    $ligne = mysqli_fetch_row($resultat);

    if($ligne[0] == 1){
        session_start();
        $_SESSION['email'] = $email;
        header("Location: ../Salle.html");
    } else {
        session_start();
        $_SESSION['message'] = 'Email ou mot de passe incorrect';
        header("Location: ../Connexion.html");
    }
    exit();
}

$connexion = mysqli_connect(SERVEUR, NOM, PASSE, BD);
if(!$connexion){
    echo "<p>Erreur de connexion : ".mysqli_connect_error()."</p>";
    exit();
}

$email = $_POST['emailPro'];
$mdp = $_POST['mdpPro'];

verifieProfil($connexion, $email, $mdp);
?>

