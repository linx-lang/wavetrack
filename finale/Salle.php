<?php require("php/connectdb.php"); ?>
<?php
    session_start();
    if (!isset($_SESSION['idPro'])) {
        header("Location: Connexion.html");
        exit();
    }
    function getSallesProprietaire($connexion, $idPro) {
        $query = "SELECT * FROM salle WHERE idPro = '$idPro'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        $salles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $salles[] = $row;
        }
        return $salles;
    }
    
    function voirSalle($connexion, $id_salle) {
        $query = "SELECT nomS, lieus, capacite FROM salle WHERE idSalle = '$id_salle'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        return mysqli_fetch_assoc($result);
    }

    function getMachinesSalle($connexion, $id_salle) {
        $query= "SELECT DISTINCT machine.* FROM machine Join capteurs on machine.idM=capteurs.idM WHERE capteurs.idSalle = '$id_salle'";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) . " - query: " . $query);
        }
        $machines = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $machines[] = $row;
        }
        return $machines;
    }

    function getcapteur($connexion, $machines) {
        $capteurs = [];
        foreach ($machines as $machine) {
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM capteurs JOIN type ON capteurs.idT = type.idT WHERE idM = $idM";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            
            while ($row = mysqli_fetch_assoc($result)) {
                $capteurs[] = $row;
            }
        }
        return $capteurs;
    }
    function getTemperatureData($connexion, $capteurs) {

        $temperatures = [];

        foreach ($capteurs as $capteur) {

            $idCap = (int)$capteur['idCap'];

            // Dernière température
            $query = "SELECT valeur FROM donnee WHERE IdCap = $idCap AND Nom = 'Temperature' ORDER BY date DESC LIMIT 1";

            // Moyenne température du jour
            $queryAvg = "SELECT AVG(valeur) AS moyenne
            FROM donnee
            WHERE IdCap = $idCap
            AND Nom = 'Temperature'
            AND DATE(date) = CURDATE()
            ";

            $result = mysqli_query($connexion, $query);
            $resultAvg = mysqli_query($connexion, $queryAvg);

            if (!$result || !$resultAvg) {
                die("SQL Error: " . mysqli_error($connexion));
            }

            $donnee = mysqli_fetch_assoc($result);
            $moyenne = mysqli_fetch_assoc($resultAvg);

            $temperatures[$idCap] = [
                'valeurT' => $donnee ? $donnee['valeur'] : null,
                'moyenneT' => $moyenne['moyenne']
            ];
        }

        return $temperatures;
    }
    function getVoltageData($connexion, $capteurs) {

        $voltages = [];

        foreach ($capteurs as $capteur) {

            $idCap = (int)$capteur['idCap'];

            // Dernier voltage
            $query = "SELECT valeur
            FROM donnee
            WHERE IdCap = $idCap
            AND Nom = 'Voltage'
            ORDER BY date DESC
            LIMIT 1
            ";

            // Moyenne voltage du jour
            $queryAvg = "SELECT AVG(valeur) AS moyenne FROM donnee WHERE IdCap = $idCap
            AND Nom = 'Voltage'
            AND DATE(date) = CURDATE()
            ";

            $result = mysqli_query($connexion, $query);
            $resultAvg = mysqli_query($connexion, $queryAvg);

            if (!$result || !$resultAvg) {
                die("SQL Error: " . mysqli_error($connexion));
            }

            $donnee = mysqli_fetch_assoc($result);
            $moyenne = mysqli_fetch_assoc($resultAvg);

            $voltages[$idCap] = [
                'valeurV' => $donnee ? $donnee['valeur'] : null,
                'moyenneV' => $moyenne['moyenne']
            ];
        }

        return $voltages;
    }

    function cartealerte($connexion, $machines) {
        $alertes = [];// liste de sortie
        foreach ($machines as $machine) {// pour chaque machine de la salle, on récupère les alertes non résolues
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM alerte WHERE idM = '$idM' AND resolu = 0";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            while ($alerte = mysqli_fetch_assoc($result)) {
                $alertes[] = $alerte;
            }
        }
        return $alertes;
    }

    function popupAlerte($connexion, $machines, $derniereAlerte) {
        foreach ($machines as $machine) {
            $idM = (int)$machine['idM'];
            $query = "SELECT * FROM alerte WHERE idM = $idM AND resolu = 0 AND idA > $derniereAlerte ORDER BY idA DESC LIMIT 1";
            $result = mysqli_query($connexion, $query);
            if (!$result) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            $alerte = mysqli_fetch_assoc($result);
            if ($alerte) {
                $alertes[] = $alerte;
            } else {
                return null;
            }
        }
        return $alertes;
    }
    function calculernbVestiaire($connexion, $idSalle) {
        $query = "SELECT valeur AS nbVestiaires FROM donnee JOIN capteurs ON donnee.IdCap = capteurs.idCap WHERE capteurs.idSalle = $idSalle AND Nom = 'nbVestiaires' ORDER BY date DESC LIMIT 1";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion) );
        }
        $row = mysqli_fetch_assoc($result);
        return $row['nbVestiaires'];
    }
    function getclim($connexion, $idSalle) {
        $query = "SELECT etat FROM climatisation JOIN bouton ON bouton.idB = climatisation.idB WHERE bouton.idSalle = $idSalle";
        $result = mysqli_query($connexion, $query);
        if (!$result) {
            die("SQL Error: " . mysqli_error($connexion));
        }
        $row = mysqli_fetch_assoc($result);
        
        return $row['etat'];
    }
    function getLundi($date = null) { // $date = null permet de rendre le paramètre optionnel
        // Si aucune date n'est fournie, on prend la date actuelle
        if (!$date) {
            $date = date("Y-m-d"); 
        }
        $ts = strtotime($date); // Convertir la date en timestamp 
        $jourDeLaSemaine = date('N', $ts); // 1 = lundi, 2 = mardi, etc.
        return date("Y-m-d", strtotime("-" . ($jourDeLaSemaine - 1) . " days", $ts));
    }
    /* ========================= */
    /* SALLE SELECTIONNEE        */
    /* ========================= */

    $idPro = $_SESSION['idPro'];
    $salles_proprietaire = getSallesProprietaire($connexion, $_SESSION['idPro']);
    



    if (isset($_GET['id'])) {
        $id_salle = (int)$_GET['id'];
        $salle    = voirSalle($connexion, $id_salle);
        $machines = getMachinesSalle($connexion, $id_salle);
        
        $capteurs = getcapteur($connexion, $machines);
        $temperatureData = getTemperatureData($connexion, $capteurs);
        $voltageData = getVoltageData($connexion, $capteurs);
        $alertes  = cartealerte($connexion, $machines);
        $nbVestiaires = calculernbVestiaire($connexion, $id_salle);
        $clim = getclim($connexion, $id_salle);
        $maxAlertId = 0;
        $lundi = isset($_GET['lundi']) ? $_GET['lundi'] : getLundi();
        $dimanche = date("Y-m-d", strtotime("$lundi +6 days"));
        $lundiAvant = date("Y-m-d", strtotime("$lundi -7 days"));
        $lundiApres = date("Y-m-d", strtotime("$lundi +7 days"));
        $sqlOuverture = "SELECT idj, horaireDeb, horaireFin FROM ouverture WHERE idSalle = $id_salle";
        $resOuverture = mysqli_query($connexion, $sqlOuverture);
        if (!$resOuverture) {
            die("SQL Error: " . mysqli_error($connexion));
         }
        $ouvertureParJourDebut = [];
        $ouvertureParJourFin   = [];
        while ($row = mysqli_fetch_assoc($resOuverture)) {
            $jour = (int)$row['idj']; // 1=lundi, 7=dimanche
            $ouvertureParJourDebut[$jour] = (int)substr($row['horaireDeb'], 0, 2);
            $ouvertureParJourFin[$jour]   = (int)substr($row['horaireFin'], 0, 2) ?: 24;
        }

        foreach ($alertes as $a) {
            if ($a['idA'] > $maxAlertId) $maxAlertId = $a['idA'];
        }

        if (isset($_GET['derniereAlerte'])) {
            $lastAlertId = (int)$_GET['derniereAlerte'];
            $newAlerte = null;
            if (is_array($alertes) && count($alertes) > 0) {
                foreach ($alertes as $alerte) {
                    if (is_array($alerte) && isset($alerte['idA']) && $alerte['idA'] > $lastAlertId) {
                        $newAlerte = $alerte;
                        break;
                    }
                }
            }
            header('Content-Type: application/json');
            if ($newAlerte && is_array($newAlerte)) {
                echo json_encode([
                    "idAlerte" => (int)$newAlerte["idA"],
                    "date"     => $newAlerte["date"],
                    "machine"  => (int)$newAlerte["idM"],
                    "message"  => "Une nouvelle alerte a été déclenchée pour la machine " . (int)$newAlerte["idM"] . " (valeur: " . $newAlerte["ValeurA"] . ")"
                ]);
            } else {
                echo json_encode([]);
            }
            exit();
        }        //creneaux de reservation
        // Récupérer le lundi de la semaine à afficher (soit depuis l'URL, soit par défaut le lundi de la semaine actuelle)
       

        // Si on est lundi et qu'aucune date n'est passé en paramètre, 
        // on force l'affichage de la semaine actuelle
        if (date("N") == 1 && !isset($_GET["lundi"])) {
            $lundi = getLundi();
        }



        // Gestion des créneaux (ajout, modification, suppression)

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
            $action = $_POST["action"];

            if ($action === "ajouter") {
                // Récupération des données du formulaire
                $nomArtiste = (string)$_POST["nomArtiste"]; 
                $date = $_POST["date"];
                $heureDebutCreneau = (int)$_POST["heureDebutCreneau"]; // 

                $erreurCreneau = "";
                $idArt = 0;

                $aujourdhui = date("Y-m-d");
                // Si la date choisie est antérieure (inférieure) à la date d'aujourd'hui
                if ($date < $aujourdhui) {
                    $erreurCreneau = "Impossible de rajouter ou modifier un créneau sur une date déjà passée.";
                }

                if ($erreurCreneau === "") {

                    // Récupérer les horaires d'ouverture du jour

                    // Convertit la date choisie en un numéro de jour de la semaine (1=lundi, 2=mardi, etc.)
                    $jourSemaine = date("N", strtotime($date)); 
                    $sqlOuverture = "
                            SELECT horaireDeb, horaireFin 
                            FROM ouverture 
                            WHERE idSalle = $id_salle AND idj = $jourSemaine
                        ";
                    $resOuverture = mysqli_query($connexion, $sqlOuverture); 
                    $ouvertureSalle = mysqli_fetch_assoc($resOuverture);
                    
                    // Si la salle n'a pas d'horaire d'ouverture/ est fermée pour le jour choisi
                    if (!$ouvertureSalle) {
                        $erreurCreneau = "La salle {$salle['nomS']} est fermé ce jour-là. Il est donc impossible de rajouter un créneau.";
                    } else {

                        // Convertit les heures d'ouverture et de fermeture en nombre entier
                        $heureOuvSalle = (int)substr($ouvertureSalle["horaireDeb"], 0, 2);
                        $heureFermSalle = (int)substr($ouvertureSalle["horaireFin"], 0, 2);
                        if ($heureFermSalle === 0) {
                            $heureFermSalle = 24;
                        }
                        
                        if ($heureDebutCreneau < $heureOuvSalle || ($heureDebutCreneau + 2) > $heureFermSalle) {
                            $erreurCreneau = "Il est impossible de rajouter un créneau en dehors des heures d'ouverture.";
                        }

                        // Vérifier si l'artiste choisit existe déjà
                        $res = mysqli_query($connexion, "
                            SELECT idArt
                            FROM artiste
                            WHERE nom='$nomArtiste'
                        ");
                        if (mysqli_num_rows($res) > 0) {
                            // Si l'artiste existe, on reprend son idArt
                            $row = mysqli_fetch_assoc($res);
                            $idArt = (int)$row["idArt"];
                        } else {
                            // Sinon, on crée l'artiste dans la base de donnée
                            mysqli_query($connexion, "
                                INSERT INTO artiste (nom) VALUES ('$nomArtiste')
                            ");
                            $idArt = mysqli_insert_id($connexion);
                        }
                    }
                    if ($erreurCreneau !== "") {
                        // Si il y a une erreur, elle sera affiché avec l'alerte php'
                    } else {

                        // Calcul des heures (Formatage pour la base de donnée)
                        $heureDeb = sprintf("%02d:00:00", $heureDebutCreneau);
                        $heureFin = sprintf("%02d:00:00", $heureDebutCreneau + 2);
                        
                        // Eviter le chevauchement de créneaux

                        // On vérifie s'il existe déjà un créneau pour : 
                                //la même salle, 
                                //le même jour 
                                // et qui chevauche les heures de début et fin du nouveau créneau
                        $sqlChevauchement = "
                            SELECT *
                            FROM creneaux
                            WHERE idSalle = $id_salle
                            AND date = '$date'
                            AND (heureDeb < '$heureFin')
                            AND (heureFin > '$heureDeb')
                        ";
                        $resChevauchement = mysqli_query($connexion, $sqlChevauchement);
                        
                        // Si le créneau en chevauche un autre
                        if (mysqli_num_rows($resChevauchement) > 0) {
                            $erreurCreneau = "Impossible de choisir ce créneau car il chevauche un autre créneau déjà existant.";
                            } else { 

                            // Sinon, on ajoute le créneau dans la base de donnée
                            mysqli_query($connexion, "
                                INSERT INTO creneaux (heureDeb, heureFin, date, idArt, idSalle)
                                VALUES ('$heureDeb', '$heureFin', '$date', '$idArt', '$id_salle')
                            ");

                            header("Location: Salle.php?id=" .$id_salle. "&lundi=" . urlencode($lundi));
                            exit;
                        }
                    }
                }    
            };
        
            if ($action === "modifier") {
                // Récupération des données du créneau à modifier
                $idC = (int)$_POST["idC"];
                $nomArtiste = (string)$_POST["nomArtiste"];
                $date = $_POST["date"];
                $heureDebutCreneau = (int)$_POST["heureDebutCreneau"];

                $erreurCreneau = "";
                $idArt = 0;
                $heureDeb = 0;
                $heureFin = 0;

                $aujourdhui = date("Y-m-d");
                if ($date < $aujourdhui) {            
                    $erreurCreneau = "Impossible de rajouter ou modifier un créneau dans une date déjà passée.";
                }

                // Récupération des horaires d'ouverture du jour
                if ($erreurCreneau === "") {
                    $jourSemaine = date("N", strtotime($date));
                    $sqlOuverture = "
                        SELECT horaireDeb, horaireFin 
                        FROM ouverture 
                        WHERE idSalle = $id_salle AND idj = $jourSemaine
                    ";
                    $resOuverture = mysqli_query($connexion, $sqlOuverture);
                    $ouvertureSalle = mysqli_fetch_assoc($resOuverture);
                    
                    if (!$ouvertureSalle) {
                        $erreurCreneau = "Aucun horaire d'ouverture défini pour ce jour.";
                    } else {
                        $heureOuv = (int)substr($ouvertureSalle["horaireDeb"], 0, 2);
                        $heureFerm = (int)substr($ouvertureSalle["horaireFin"], 0, 2);

                        if ($heureFerm === 0) {
                            $heureFerm = 24;
                        }
                        
                        if ($heureDebutCreneau < $heureOuv || ($heureDebutCreneau + 2) > $heureFerm) {
                            $erreurCreneau = "Impossible de rajouter un créneau en dehors des heures d'ouverture.";
                            }

                        // Mettre à jour les heures (Formatage pour la base de donnée)
                        $heureDeb = sprintf("%02d:00:00", $heureDebutCreneau);
                        $heureFin = sprintf("%02d:00:00", $heureDebutCreneau + 2);
                        
                        // Chercher si un artiste a déjà ce nom
                        $res = mysqli_query($connexion, "
                            SELECT idArt 
                            FROM artiste
                            WHERE artiste.nom='$nomArtiste'
                        ");

                        if (mysqli_num_rows($res) > 0) {
                            // Si l'artiste existe alors on prend l'idArt
                            $row = mysqli_fetch_assoc($res);
                            $idArt = (int)$row["idArt"];
                        } else {
                            // Sinon, on crée l'artiste et on prend le nouvel id
                            mysqli_query($connexion, "
                                INSERT INTO artiste (nom) 
                                VALUES ('$nomArtiste')
                            ");
                            $idArt = mysqli_insert_id($connexion);
                        }
                    }
                    if ($erreurCreneau !== "") {
                        // Si il y a une erreur, elle sera affiché avec l'alerte php
                    } else {
                        // Eviter le chevauchement de créneaux
                        
                        // On vérifie s'il existe déjà un créneau pour : 
                                // la même salle, 
                                // le même jour 
                                // et qui chevauche les heures de début et fin du nouveau créneau
                                // On exclut le créneau actuel pour ne pas le comparer à lui-même
                        $sqlChevauchement = "
                            SELECT *
                            FROM creneaux
                            WHERE idSalle = $id_salle
                            AND date = '$date'
                            AND (heureDeb < '$heureFin')
                            AND (heureFin > '$heureDeb')
                            AND idC != $idC
                        ";
                        $resChevauchement = mysqli_query($connexion, $sqlChevauchement);

                        if (mysqli_num_rows($resChevauchement) > 0) {
                            $erreurCreneau = "Impossible de choisir ce créneau car il chevauche un autre créneau déjà existant.";
                        } else { 

                            mysqli_query($connexion, "
                                UPDATE creneaux
                                SET idArt='$idArt', Date='$date', heureDeb='$heureDeb', heureFin='$heureFin'
                                WHERE idC=$idC AND idSalle=$id_salle
                            ");

                            header("Location: Salle.php?id=" .$id_salle. "&lundi=" . urlencode($lundi));
                            exit;
                        }
                    }
                }
            };

            if ($action === "supprimer") {
                $idC = (int)$_POST["idC"];

                if ($idC > 0) {
                    mysqli_query($connexion, "
                    DELETE FROM creneaux WHERE idC=$idC AND idSalle=$id_salle
                    ");
                }

                header("Location: Salle.php?id=" .$id_salle. "&lundi=" . urlencode($lundi));
                exit;
            };
        };

        // Chargement des créneaux de la semaine affichée
        
        // Récupération de tous les créneaux de la salle affichée pour la semaine affichée
        $sqlCreneaux = "
            SELECT c.idC, c.date, c.heureDeb, c.heureFin, c.idArt, a.nom
            FROM creneaux c
            JOIN artiste a ON a.idArt = c.idArt
            WHERE c.date BETWEEN '$lundi' AND '$dimanche' AND c.idSalle = $id_salle
            ORDER BY c.date, c.heureDeb
        ";
        $resCreneaux = mysqli_query($connexion, $sqlCreneaux);

        // Formatage des créneaux dans un tableau pour le javascript
        $listeCreneaux = [];
        while ($row = mysqli_fetch_assoc($resCreneaux)) {
            $listeCreneaux[] = [
                "idC"       => (int)$row["idC"],
                "artist"    => (string)$row["nom"],
                "date"      => $row["date"],
                "heureDebutCreneau" => (int)substr($row["heureDeb"], 0, 2),
                "idArt"     => (int)$row["idArt"]
            ];
        }


    } else {
        $salle = $salles_proprietaire[0];
    }


    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'cartealerte') {
            $alerte = cartealerte($connexion, $machines);
            header('Content-Type: application/json');
            echo json_encode($alerte);
            exit();
        }
        if ($_GET['action'] == 'getTemperatureData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $temperatureData = getTemperatureData($connexion, $capteurs);
            $result   = [];
            foreach ($capteurs as $capteur) {
                $idCap = (int)$capteur['idCap'];
                if (isset($temperatureData[$idCap])) {
                    $result[] = [
                        'idCap'   => $idCap,
                        'valeur'  => $temperatureData[$idCap]['valeurT'],
                        'moyenne' => $temperatureData[$idCap]['moyenneT']
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        if ($_GET['action'] == 'getVoltageData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $voltageData = getVoltageData($connexion, $capteurs);
            $result   = [];
            foreach ($capteurs as $capteur) {
                $idCap = (int)$capteur['idCap'];
                if (isset($voltageData[$idCap])) {
                    $result[] = [
                        'idCap'   => $idCap,
                        'valeur'  => $voltageData[$idCap]['valeurV'],
                        'moyenne' => $voltageData[$idCap]['moyenneV']
                    ];
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        if($_GET['action']=='calculernbVestiaire'){
            $id_salle = (int)$_GET['id'];
            $nbVestiaires = calculernbVestiaire($connexion, $id_salle);
            header('Content-Type: application/json');
            echo json_encode(['vestiaires' => $nbVestiaires]);
            exit();
        }
        if($_GET['action']=='getClim'){
            $id_salle = (int)$_GET['id'];
            $climState = getclim($connexion, $id_salle);
            header('Content-Type: application/json');
            echo json_encode(['etat' => $climState]);
            exit();
        }
        //les graphiques
        if ($_GET['action'] == 'getTemperatureGraphData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $result = [];
                foreach ($capteurs as $capteur) {
                    if ($capteur['nomT'] !== 'Multi-Sensor') continue; 
                    $idCap = (int)$capteur['idCap'];
                    $query = "SELECT valeur, date FROM donnee WHERE IdCap = $idCap AND Nom = 'Temperature' ORDER BY date DESC LIMIT 20";
                    $res = mysqli_query($connexion, $query);
                    $labels = []; $values = [];
                    while ($row = mysqli_fetch_assoc($res)) {
                        $labels[] = date("H:i", strtotime($row['date']));
                        $values[] = floatval($row['valeur']);
                    }
                    $result[] = [
                    'idCap'  => $idCap,
                    'labels' => array_reverse($labels),
                    'values' => array_reverse($values)
                    ];
             }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        if ($_GET['action'] == 'getVoltageGraphData') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $capteurs = getcapteur($connexion, $machines);
            $result = [];
                foreach ($capteurs as $capteur) {
                    if ($capteur['nomT'] !== 'Prise Intelligente') continue; 
                    $idCap = (int)$capteur['idCap'];
                    $query = "SELECT valeur, date FROM donnee WHERE IdCap = $idCap AND Nom = 'Voltage' ORDER BY date DESC LIMIT 20";
                    $res = mysqli_query($connexion, $query);
                    $labels = []; $values = [];
                    while ($row = mysqli_fetch_assoc($res)) {
                        $labels[] = date("H:i", strtotime($row['date']));
                        $values[] = floatval($row['valeur']);
                    }
                    $result[] = [
                    'idCap'  => $idCap,
                    'labels' => array_reverse($labels),
                    'values' => array_reverse($values)
                    ];
             }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        if ($_GET['action'] == 'alerteparmachine') {
            $id_salle = (int)$_GET['id'];
            $machines = getMachinesSalle($connexion, $id_salle);
            $labels = [];
            $values = [];
            foreach ($machines as $machine) {
                $idM = (int)$machine['idM'];
                $query = "SELECT COUNT(*) AS total FROM alerte WHERE idM = $idM AND resolu = 0";
                $res = mysqli_query($connexion, $query);
                if (!$res) die("SQL Error: " . mysqli_error($connexion));
                $row = mysqli_fetch_assoc($res);
                $labels[] = $machine['noM'];
                $values[] = (int)$row['total'];
            }
            header('Content-Type: application/json');
            echo json_encode(['labels' => $labels, 'values' => $values]);
            exit();
        }
        if ($_GET['action'] == 'alerteparmachineParJour') {
            $id_salle = (int)$_GET['id'];
            $idM = (int)$_GET['idM'];

            // Vérifier que la machine appartient bien à cette salle
            $machines = getMachinesSalle($connexion, $id_salle);
            $nomMachine = '';
            foreach ($machines as $machine) {
                if ((int)$machine['idM'] === $idM) {
                    $nomMachine = $machine['noM'];
                    break;
                }
            }

            $query = "
                SELECT DATE(date) AS jour, COUNT(*) AS total
                FROM alerte
                WHERE idM = $idM
                GROUP BY DATE(date)
                ORDER BY jour ASC
            ";
            $res = mysqli_query($connexion, $query);
            if (!$res) die("SQL Error: " . mysqli_error($connexion));

            $labels = [];
            $values = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $labels[] = $row['jour'];
                $values[] = (int)$row['total'];
            }

            header('Content-Type: application/json');
            echo json_encode(['machine' => $nomMachine, 'labels' => $labels, 'values' => $values]);
            exit();
        }

        
    }

    
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Salle</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/salle.css">
    
</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <img src="img/logo.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        <ul class="nav-links">
            <li><a href="Accueil.html">Accueil</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>

    <!-- ============================================================ -->
    <!-- MAIN LAYOUT                                                   -->
    <!-- ============================================================ -->
    <div class="container-fluid mt-4">
        <div class="row min-vh-100">

            <!-- ===================================================== -->
            <!-- SIDEBAR — liste des salles                             -->
            <!-- ===================================================== -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-4 h-100">
                    <div class="card-body p-0">
                        <h3 class="px-3 pt-3 mb-2">Salles du propriétaire</h3>
                        <div class="list-group list-group-flush">
                            <?php foreach ($salles_proprietaire as $s): ?>
                                <a href="salle.php?id=<?= $s['idSalle']; ?>"
                                   class="list-group-item list-group-item-action px-3 py-2">
                                    <div class="fw-semibold small"><?= $s['nomS']; ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= $s['lieus']; ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin col-md-3 (sidebar) -->

            <!-- ===================================================== -->
            <!-- CONTENU PRINCIPAL                                      -->
            <!-- ===================================================== -->
            <main class="col-md-9">

                <!-- ================================================= -->
                <!-- DÉTAILS DE LA SALLE                                -->
                <!-- ================================================= -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3>Détails de la salle</h3>
                        <p><strong>Nom :</strong> <?= $salle['nomS']; ?></p>
                        <p><strong>Emplacement :</strong> <?= $salle['lieus']; ?></p>
                        <p><strong>Capacité :</strong> <?= $salle['capacite']; ?></p>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- CRÉNEAUX DE RÉSERVATION                            -->
                <!-- ================================================= -->
                <div class="conteneur">
                            <div class="planning-header">
                                <h2>Planning</h2>
                                <div class="planning-nav">
                                    <a href="Salle.php?id=<?= $id_salle ?>&lundi=<?= urlencode($lundiAvant) ?>">« Semaine précédente</a>
                                    <span>
                                        Semaine du <?= date("d/m/Y", strtotime($lundi)) ?>
                                        au <?= date("d/m/Y", strtotime($dimanche)) ?>
                                    </span>
                                    <a href="Salle.php?id=<?= $id_salle ?>&lundi=<?= urlencode($lundiApres) ?>" class="boutton">Semaine suivante »</a>
                                </div>
                                <button class="boutton" data-bs-toggle="modal" data-bs-target="#ajoutModal">Ajouter un créneau</button>
                            </div>

                            <?php if (!empty($erreurCreneau)) : ?>
                                <div class="alert alert-danger">
                                    <?= htmlspecialchars($erreurCreneau) ?>
                                </div>
                            <?php endif; ?>
                            <div class="timetable-container mt-3">
                                <table id="timetable">
                                    <thead>
                                        <tr>
                                            <th class="time-col">Heure</th>
                                            <!-- Les jours seront ajoutés en JS -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Les lignes horaires & créneaux seront ajoutés en JS -->
                                    </tbody>
                                </table>
                            </div>
                    </div>
                

                <!-- Modal/Pop-up pour ajouter un créneau -->
                <div class="modal fade" id="ajoutModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="post" action="Salle.php?id=<?= $id_salle ?>&lundi=<?= urlencode($lundi) ?>" class="modal-content">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="date" id="ajoutDate">
                            <input type="hidden" name="heureDebutCreneau" id="ajoutheureDebutCreneau">

                            <div class="modal-header">
                                <h5 class="modal-title">Ajouter un créneau</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-2">
                                    <label class="form-label">Artiste</label>
                                    <input type="text" name="nomArtiste" id="ajoutNomArt" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" id="ajoutDate" class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Heure de début</label>
                                    <select name="heureDebutCreneau" id="ajoutheureDebutCreneau" class="form-select" required>
                                        <?php for ($h = 0; $h <= 22; $h++): ?>
                                        <option value="<?= $h ?>"><?= sprintf("%02d", $h) ?>h</option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="form-text">Chaque créneau dure 2h automatiquement.</div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal/Pop-up pour modifier un créneau -->
                <div class="modal fade" id="modifModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" action="Salle.php?id=<?= $id_salle ?>&lundi=<?= urlencode($lundi) ?>" class="modal-content">
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="idC" id="modifIdC">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le créneau</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label">Artiste</label>
                                <input type="text" name="nomArtiste" id="modifNomArt" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" id="modifDate" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Heure de début</label>
                                <select name="heureDebutCreneau" id="modifHeure" class="form-select" required>
                                    <?php for ($h = 0; $h <= 22; $h++): ?>
                                    <option value="<?= $h ?>"><?= sprintf("%02d", $h) ?>h</option>
                                    <?php endfor; ?>
                                </select>
                                <div class="form-text">Chaque créneau dure 2h automatiquement.</div>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                    </form>
                </div>
                </div>

                <!-- Modal/Pop-up pour supprimer un créneau -->
                <div class="modal fade" id="suppModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" action="Salle.php?id=<?= $id_salle ?>&lundi=<?= urlencode($lundi) ?>" class="modal-content">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="idC" id="suppIdC">

                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le créneau</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Es-tu sûre de vouloir supprimer ce créneau ?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                    </form>
                </div>
                </div>

                <!-- ================================================= -->
                <!-- ONGLETS                                             -->
                <!-- ================================================= -->
                <ul class="nav nav-tabs" id="salleTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#analyse">Analyse</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alertes">Alertes</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#Vuedensemble">Vue d'ensemble</button>
                    </li>
                </ul>

                <div class="tab-content mt-4">

                    <!-- ============================================= -->
                    <!-- ONGLET ANALYSE                                 -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade show active" id="analyse">

                        <h4 class="mb-3">Machines</h4>

                        <div class="row g-3">
                            <?php foreach ($machines as $machine): ?>
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">

                                        <div class="card-title fw-bold fs-5 border-bottom pb-2 mb-3">
                                             <?= $machine['noM']; ?>
                                        </div>
                                        <p><strong>Température max :</strong> <?= $machine['valeurMaxTemp']; ?>°C</p>
                                        <p><strong>Voltage max :</strong> <?= $machine['valeurMaxWatt']; ?>V</p>

                                        <strong>Capteurs associés</strong>

                                        <?php foreach ($capteurs as $capteur): ?>
                                            
                                            <?php if ($capteur['idM'] == $machine['idM']): ?>
                                                
                                                <div class="mb-3 p-2 border rounded bg-light">
                                                    <?php if ($capteur ['nomT'] == 'Multi-Sensor'): ?>
                                                        <p><strong>Type :</strong> <?= $capteur['nomT']; ?></p>
                                                        <?php $d = isset($temperatureData[$capteur['idCap']]) ? $temperatureData[$capteur['idCap']] : null; ?>
                                                        <?php if($d['valeurT'] !== null): ?>
                                                            
                                                            <p><strong>Température :</strong> <span id="valeurt_<?= $capteur['idCap'] ?>"><?= $d ? $d['valeurT'] : 'N/A'; ?></span> °C</p>
                                                            <p><strong>Moyenne du jour :</strong> <span id="moyennet_<?= $capteur['idCap'] ?>"><?= $d ? $d['moyenneT'] : 'N/A'; ?></span> °C</p>
                                                        <?php endif; ?>
                                                        <p><strong>graphique:</strong></p>
                                                        <canvas id="chart_<?= $capteur['idCap'] ?>" height="140"></canvas>
                                                    <?php endif; ?>
                                                    <?php if ($capteur ['nomT'] == 'Prise Intelligente'): ?>
                                                        <p><strong>Type :</strong> <?= $capteur['nomT']; ?></p>
                                                        <?php $d = isset($voltageData[$capteur['idCap']]) ? $voltageData[$capteur['idCap']] : null; ?>
                                                        <?php if ($d['valeurV'] != null): ?>
                                                            <p><strong>Voltage :</strong> <span id="valeurv_<?= $capteur['idCap'] ?>"><?= $d ? $d['valeurV'] : 'N/A'; ?></span> V</p>
                                                            <p><strong>Moyenne du jour :</strong> <span id="moyennev_<?= $capteur['idCap'] ?>"><?= $d ? $d['moyenneV'] : 'N/A'; ?></span> V</p>
                                                    
                                                        <?php endif; ?>
                                                        <p><strong>graphique:</strong></p>
                                                        <canvas id="chart_volt_<?= $capteur['idCap'] ?>" height="140"></canvas>
                                                    <?php endif; ?>
                                                   
                                                
                                                    
                                                </div>
                                
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                    <!-- fin onglet analyse -->

                    <!-- ============================================= -->
                    <!-- ONGLET ALERTES                                 -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade" id="alertes">

                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="card-title">Alertes par machine</h6>
                                <canvas id="graph_alertes" height="120"></canvas>
                            </div>
                        </div>

                        <h4 class="mb-3 mt-4">Alertes à résoudre</h4>
                        <div class="list-group">
                            <?php foreach ($alertes as $alerte): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Numéro de l'alerte : <?= $alerte['idA']; ?></h5>
                                    <p><strong>ID machine :</strong> <?= $alerte['idM']; ?></p>
                                    <p><strong>Date :</strong> <?= $alerte['date']; ?></p>
                                    <p><strong>Valeur :</strong> <?= $alerte['ValeurA']; ?></p>
                                </div>
                                <form method="POST" action="resoudre_alerte.php">
                                    <input type="hidden" name="id" value="<?= $alerte['idA']; ?>">
                                    <button class="btn btn-danger btn-sm">Résoudre</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <h4 class="mt-4 mb-3">Analyse par machine</h4>
                        <?php foreach ($machines as $machine): ?>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title"><?= $machine['noM'] ?></h6>
                                    <canvas id="graph_alertes_jour_<?= $machine['idM'] ?>" height="120"></canvas>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                    <!-- fin onglet alertes -->

                    <!-- ============================================= -->
                    <!-- ONGLET DIAGNOSTIC                              -->
                    <!-- ============================================= -->
                    <div class="tab-pane fade" id="Vuedensemble">
                        <h4 class="mb-3">État de la salle</h4>
                        <div class="row g-3">

                            <!-- Porte -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Porte</h5>
                                        <?php foreach ($capteurs as $capteur): ?>
                                            <?php if ($capteur['nomT'] == 'Porte'): ?>
                                                <p><strong>État : </strong>
                                                <?php if ($capteur['etatT'] == 0): ?>
                                                    <span id="etat-porte" class="text-danger">Fermée</span>
                                                <?php else: ?>
                                                    <span id="etat-porte" class="text-success">Ouverte</span>
                                                <?php endif; ?>
                                                </p>

                                    
                                            <?php endif; ?>
                                        
                                        <?php endforeach; ?>
                                       
                                    </div>
                                </div>
                            </div>

                            <!-- Climatisation -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Climatisation</h5>
                                        <p><strong>État :</strong>
                                            <?php if($clim == 1): ?>
                                                <?php echo '<span id="etat-clim" class="text-success">Allumée</span>'; ?>
                                            <?php else: ?>
                                                <?php echo '<span id="etat-clim" class="text-danger">Éteinte</span>'; ?>
                                            <?php endif; ?>
                                        </p>
                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Vestiaires -->
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Vestiaires</h5>
                                        <p><strong>Compteur :</strong>
                                            <span id="nbVestiaires"><?= $nbVestiaires['vestiaires']; ?> personnes</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- fin onglet diagnostic -->

                </div>
                <!-- fin tab-content -->

            </main>
            <!-- fin col-md-9 (main) -->

        </div>
        <!-- fin row -->

    </div>
    <!-- fin container -->

    <!-- ============================================================ -->
    <!-- POPUP ALERTES                                                 -->
    <!-- ============================================================ -->
    <div class="modal fade" id="alerteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Alerte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="alerteMessage"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Passer des données PHP au JavaScript pour la gestion des créneaux et des alertes
        const currentSalleId = <?= isset($_GET['id']) ? (int)$_GET['id'] : (int)$salle['idSalle']; ?>;
        let lastId = <?= $maxAlertId; ?>;
        window.PLANNING_SLOTS = <?= json_encode($listeCreneaux) ?>;
        window.PLANNING_MONDAY = "<?= $lundi ?>";
        var ouvertureSalleparJourDebut = <?= json_encode($ouvertureParJourDebut) ?>;
        var ouvertureSalleparJourFin   = <?= json_encode($ouvertureParJourFin) ?>;
        const alerteMachineIds = <?= json_encode(array_column($machines, 'idM')) ?>;
    </script>
    <script src="java/Salle.js"></script>

</body>
</html>
