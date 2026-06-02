<?php
header("Content-Type: application/json");

// Connexion simple
$connexion = mysqli_connect("localhost", "root", "", "projetsql");

// Vérifier la connexion
if (!$connexion) {
    echo json_encode(array("error" => "Erreur connexion BDD"));
    exit;
}

// Requête simple
$sql = "SELECT valeur, date FROM donnee WHERE Nom = 'Temperature' ORDER BY date DESC LIMIT 20";
$result = mysqli_query($connexion, $sql);

// Préparer les tableaux
$labels = array();
$values = array();

// Lire les lignes une par une
while ($ligne = mysqli_fetch_assoc($result)) {
    $labels[] = date("H:i", strtotime($ligne["date"]));
    $values[] = $ligne["valeur"];
}

// Inverser pour avoir les données dans le bon sens
$labels = array_reverse($labels);
$values = array_reverse($values);

// Construire le tableau final SANS =>
$final = array();
$final["labels"] = $labels;
$final["values"] = $values;

// Retour JSON
echo json_encode($final);
?>
