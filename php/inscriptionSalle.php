<?php
function ajouterSalle($connexion) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nomSalle = $_POST['nomSalle'];
        $capacite = $_POST['capaciteSalle'];
        $codePos  = $_POST['codePosSalle'];
        $adresse  = $_POST['adresseSalle'];

        $query = "INSERT INTO salle (nomS, capacite, codePoS, lieus) 
                  VALUES ('$nomSalle', '$capacite', '$codePos', '$adresse')";
        mysqli_query($connexion, $query);

        $joursSelectionnes = isset($_POST['jour']) ? $_POST['jour'] : [];// Récupère les jours sélectionnés ou un tableau vide
        $horairesDebut = isset($_POST['horaireD']) ? $_POST['horaireD'] : [];
        $horairesFin  = isset($_POST['horaireF']) ? $_POST['horaireF'] : [];

        foreach ($joursSelectionnes as $jour) {
            if (empty($jour)) continue;// Ignore les jours vides

            $query1 = "INSERT INTO jour(nom) VALUES ('$jour')";
            mysqli_query($connexion, $query1);
            $idJour = mysqli_insert_id($connexion);

            $debs = $horairesDebut[$jour]? $horairesDebut[$jour] : [];
            $fins = $horairesFin[$jour]? $horairesFin[$jour] : [];

            for ($i = 0; $i < count($debs); $i++) {
                $deb = $debs[$i];
                $fin = $fins[$i] ? $fins[$i] : '';
                if (empty($deb) || empty($fin)) continue;

                $query2 = "INSERT INTO ouverture (horaireDeb, horaireFin, idJ) 
                           VALUES ('$deb', '$fin', $idJour)";
                mysqli_query($connexion, $query2);
            }
        }
    }
}
?>