<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomGroupe  = htmlspecialchars($_POST['nomGroupeAjoutCreneaux']);
    $date       = $_POST['date'];
    $heure      = $_POST['heure'];
    $duree      = $_POST['duree'];
    $salleId    = (int) $_POST['salleId'];

    $stmt = $pdo->prepare(
        "INSERT INTO creneaux (nom_groupe, date, heure, duree, salle_id) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$nomGroupe, $date, $heure, $duree, $salleId]);

    header('Location: Salle.html');
}

?>