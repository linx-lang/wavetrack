<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomSalle   = htmlspecialchars($_POST['nomSalle']);
    $capacite   = (int) $_POST['capaciteSalle'];
    $adresse    = htmlspecialchars($_POST['adresseSalle']);
    $userId     = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO salles (nom, capacite, adresse, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nomSalle, $capacite, $adresse, $userId]);

    header('Location: Salle.html');
}