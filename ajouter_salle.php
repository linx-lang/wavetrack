<?php require('php/connectdb.php'); ?>
<?php

    /* 1. Define function first */
    function validerInscription($connexion) {
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            echo"bonjour";
            error_log("Session started");}
            //var_dump($_POST);
            //die("Debug stop");
            $nomSalle = $_POST['nomSalle'];
            $capacite = $_POST['capaciteSalle'];
            $codePos  = $_POST['codePosSalle'];
            $adresse  = $_POST['adresseSalle'];
            $idPro = $_SESSION['idPro']; // Retrieve the ID from session
    
            if (empty($nomSalle) || empty($capacite) || empty($codePos) || empty($adresse)) {
                echo "<p style='color:red'>Erreur : infos de la salle manquantes</p>";
                    return;
            }

            $query = "INSERT INTO salle (nomS, capacite, codePoS, lieus, idPro) 
                VALUES ('$nomSalle', '$capacite', '$codePos', '$adresse', '$idPro')";
            mysqli_query($connexion, $query);
            if (mysqli_error($connexion)) {
                die("SQL Error: " . mysqli_error($connexion));
            }
            $idSalle= mysqli_insert_id($connexion);
            $_SESSION['idSalle'] = $idSalle; // Store the new ID in session for later use

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
                    $deb = $debs[$i] ? $debs[$i] : '';
                    $fin = $fins[$i] ? $fins[$i] : '';
                    if (empty($deb) || empty($fin)) continue;

                    $query20= "INSERT INTO ouverture (idSalle, idJ, horaireDeb, horaireFin) VALUES ('$idSalle', '$idJour', '$deb', '$fin')";
                    $result20 = mysqli_query($connexion, $query20);
                    if (!$result20) {
                        die("INSERT Error ouverture (idSalle, idJ): " . mysqli_error($connexion));
                    }
                }
            }
            header('Location: InscriptionZoneProduit1.php');
            
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validerInscription($connexion);
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveTrack- Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/Inscription.css">
    <link rel="stylesheet" href="css/InscriptionZoneProduit.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <nav class="navbar bg-dark text-white p-3 mb-4">
        <div class="logo">
            <img src="img/rasp.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        
        <ul class="nav-links">
            <li><a href="#">Accueil</a></li>
            <li><a href="#">Produits</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">À propos</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>
    

    
    <main class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="POST" action="InscriptionSalle.php" class="p-4 border">
                    
                        <div class="col-md-12 mb-3">
                            <input type="text" placeholder="Nom de la salle" class="form-control mb-2" name="nomSalle">
                            <input type="text" placeholder="Capacité" class="form-control mb-2" name="capaciteSalle">
                            <input type="text" placeholder="Code Postal" class="form-control mb-2" name="codePosSalle">
                            <input type="text" placeholder="Adresse" class="form-control mb-2" name="adresseSalle">
                            <textarea class="form-control mb-2" placeholder="Autre" name="autreSalle"></textarea>
                            <textarea class="form-control mb-2" placeholder="Périodes de fermetures" name="periodeFermetureSalle"></textarea>
                            
                        </div>

                        <p>Horaires de la semaine:</p>
                
                        <div class="jour-ligne">
                            <input type="checkbox" id="lundi" name="jour[]" value="Lundi">
                            <label for="lundi">Lundi</label>
                            <div class = "info">
                                <div class="horaireP" id="hlundi">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Lundi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Lundi][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('lundi',this)">+ Ajouter horaire</button>
                        </div>

                        <div class="jour-ligne">
                            <input type="checkbox" id="mardi" name="jour[]" value="Mardi">
                            <label for="mardi">Mardi</label>
                            <div class = "info">
                                <div class="horaireP" id="hmardi">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Mardi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Mardi][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('mardi',this)">+ Ajouter horaire</button>
                        </div>

                        <div class="jour-ligne">
                            <input type="checkbox" id="mercredi" name="jour[]" value="Mercredi">
                            <label for="mercredi">Mercredi</label>
                            <div class = "info" id="hmercredi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Mercredi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Mercredi][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('mercredi',this)">+ Ajouter horaire</button>
                        </div>
                
                        <div class="jour-ligne">
                            <input type="checkbox" id="jeudi" name="jour[]" value="Jeudi">
                            <label for="jeudi">Jeudi</label>
                            <div class = "info" id="hjeudi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb " name="horaireD[Jeudi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Jeudi][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('jeudi',this)">+ Ajouter horaire</button>
                        </div>


                        <div class="jour-ligne">
                            <input type="checkbox" id="vendredi" name="jour[]" value="Vendredi">
                            <label for="vendredi">Vendredi</label>
                            <div class = "info" id="hvendredi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Vendredi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Vendredi][]">
                                </div>
                            </div>

                            <button type="button" onclick="ajouterHoraire('vendredi',this)">+ Ajouter horaire</button>
                        </div>
                

                        <div class="jour-ligne">
                            <input type="checkbox" id="samedi" name="jour[]" value="Samedi">
                            <label for="samedi">Samedi</label>
                            <div class = "info" id="hsamedi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Samedi][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Samedi][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('samedi',this)">+ Ajouter horaire</button>
                        </div>
                
                        <div class="jour-ligne">
                            <input type="checkbox" id="dimanche" name="jour[]" value="Dimanche">
                            <label for="dimanche">Dimanche</label>
                            <div class = "info" id="hdimanche">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[Dimanche][]" >
                                    <input type="text" placeholder="Horaire fin" name="horaireF[Dimanche][]" >
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('dimanche',this)">+ Ajouter horaire</button>
                        </div>
                        <div class = "bouttondroite">
                            <button type="submit"  class="btn btn-primary">Continuer</button>
                        </div>
                    
                
                    </form>
                        

    </main>
     <footer class="text-center py-4 mt-5 border-top">
        <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia - 
        <a href="Contactavant.html">Nos Contacts</a></p>
    </footer>
</body>
</html>
<script src="../wavetrack/java/inscription.js"></script>