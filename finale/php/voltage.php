<?php
header("Content-Type: application/json");


$conn = mysqli_connect("localhost", "root", "", "projetsql");

// Si la connexion échoue → message clair
if (!$conn) {
    echo json_encode(["error" => "Connexion impossible : " . mysqli_connect_error()]);
    exit;
}

// Requête pour récupérer le voltage
$sql = "SELECT date, valeur FROM donnee WHERE Nom = 'Voltage' ORDER BY date ASC";
$res = mysqli_query($conn, $sql);

// Si la requête SQL échoue → message clair
if (!$res) {
    echo json_encode(["error" => "Erreur SQL : " . mysqli_error($conn)]);
    exit;
}

$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($res)) {
    $labels[] = $row["date"];
    $values[] = floatval($row["valeur"]);
}

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
