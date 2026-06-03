<?php
require_once "connectdb.php";
header("Content-Type: application/json");

// Compter les alertes par machine + récupérer le nom de la machine
$sql = "
    SELECT machine.noM AS nom, alerte.idM, COUNT(*) AS total
    FROM alerte
    INNER JOIN machine ON alerte.idM = machine.idM
    GROUP BY alerte.idM
    ORDER BY alerte.idM ASC
";

$result = mysqli_query($connexion, $sql);

$labels = array();
$values = array();

while ($row = mysqli_fetch_assoc($result)) {
    // On met le vrai nom de la machine
    $labels[] = $row["nom"];
    $values[] = intval($row["total"]);
}

echo json_encode(array(
    "labels" => $labels,
    "values" => $values
));
?>
