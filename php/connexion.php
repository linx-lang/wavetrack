<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['emailPro'];
    $mdp   = $_POST['mdpPro'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user['mdp'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom']     = $user['nom'];
        header('Location: Salle.html');
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}

?>