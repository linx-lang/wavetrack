<?php 
require('connectdb.php'); 
require('db.php');
function verifieProfil($connexion, $email, $mdp){

    $query = "SELECT * FROM proprietaire WHERE mailP='".$email."' AND mdpP='".$mdp."'";
    $resultat = mysqli_query($connexion, $query);
    $pro = mysqli_fetch_assoc($resultat);

    if($pro){
        session_start();
        $_SESSION['idPro'] = $pro['idPro'];   
        $_SESSION['email'] = $pro['mailP'];   
        header("Location: ../salle.php");    
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

