<?php
require_once "connectdb.php";
header("Content-Type: application/json");

// Récupérer l'id de la machine (ou 1 par défaut)
$idM = isset($_GET["idM"]) ? intval($_GET["idM"]) : 1;

// Récupérer le nom de la machine
$sqlNom = "SELECT noM FROM machine WHERE idM = $idM";
$resNom = mysqli_query($connexion, $sqlNom);
$rowNom = mysqli_fetch_assoc($resNom);
$nomMachine = $rowNom["noM"];

// Compter les alertes PAR JOUR pour cette machine
$sql = "
    SELECT DATE(date) AS jour, COUNT(*) AS total
    FROM alerte
    WHERE idM = $idM
    GROUP BY DATE(date)
    ORDER BY jour ASC
";

$result = mysqli_query($connexion, $sql);

$labels = array();
$values = array();

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row["jour"];      // ex : 2026-05-01
    $values[] = intval($row["total"]);
}

// Retour JSON
echo json_encode(array(
    "machine" => $nomMachine,
    "labels" => $labels,
    "values" => $values
));
?>
