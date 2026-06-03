<?php
header("Content-Type: application/json");


$connexion = mysqli_connect("localhost", "root", "", "projetsql");


if (!$connexion) {
    echo json_encode(array("error" => "Erreur connexion BDD"));
    exit;
}


$sql = "SELECT valeur, date FROM donnee WHERE Nom = 'Temperature' ORDER BY date DESC LIMIT 20";
$result = mysqli_query($connexion, $sql);

// Préparer les tableaux
$labels = array();
$values = array();

while ($ligne = mysqli_fetch_assoc($result)) {
    $labels[] = date("H:i", strtotime($ligne["date"]));
    $values[] = $ligne["valeur"];
}

// Inverser pour avoir les données dans le bon sens
$labels = array_reverse($labels);
$values = array_reverse($values);


$final = array();
$final["labels"] = $labels;
$final["values"] = $values;


echo json_encode($final);
?>
