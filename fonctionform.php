<?php
function ajouterinscription($connexion){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }
        $nom = $_POST['nomPro'];
        $prenom = $_POST['prenomPro'];
        $email = $_POST['emailPro'];
        $pwd = $_POST['pwdPro'];
        $query = "INSERT INTO proprietaire (nomP, prenomP, mailP, mdpP) VALUES ('" . $nom . "', '" . $prenom . "', '" . $email . "', '" . $pwd . "')";
        mysqli_query($connexion, $query);
        
        $joursSelectionnes = $_POST['jour'] ?? [];//si vide on rajoute un string vide pour éviter les erreurs de type "undefined index"
        $horairesDebut = $_POST['horaireD'] ?? [];
        $horairesFin = $_POST['horaireF'] ?? [];

        foreach ($joursSelectionnes as $jour) {

            if (empty($jour)) continue; // saute les string jours vides
            
            $query1 = "INSERT INTO jour(nom) VALUES ('" . $jour . "')";//les identifiants sont auto-incrémentés dans la base de données, pas besoin de les spécifier
            mysqli_query($connexion, $query1);
            if(mysqli_query($connexion, $query1)){
                echo " Jour '$jour' inséré.<br>";
            } else {
                echo "Erreur insertion jour '$jour' : " . mysqli_error($connexion) . "<br>";
            }

            $debs = $horairesDebut[$jour] ?? [];
            $fins = $horairesFin[$jour]   ?? [];
            $idJour = mysqli_insert_id($connexion);
            echo "ID jour '$jour' : $idJour<br>";
        
            for ($i = 0; $i < count($debs); $i++) {
                $deb = $debs[$i];
                $fin = $fins[$i] ?? '';

                    if (empty($deb) || empty($fin)) {
                        $_SESSION['message'] = 'Veuillez remplir tous les champs horaires pour le jour ' . $jour;
                        header("Location: Inscription.html");
                        exit();
                    }

               $query2 = "INSERT INTO ouverture (horaireDeb, horaireFin, idJ) VALUES ('" . $deb . "', '" . $fin . "', " . $idJour . ")";
                if(mysqli_query($connexion, $query2)){
                    echo "Horaire $deb-$fin inséré pour le jour '$jour' (idJ=$idJour).<br>";
                } else {
                    echo "Erreur insertion horaire : " . mysqli_error($connexion) . "<br>";
                }
                mysqli_query($connexion, $query2);
}
     }}
?>

}
