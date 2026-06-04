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
            $idSalle= mysqli_insert_id($connexion);// php garde la cle primaire de la derniere insert de la bdd
            $_SESSION['idSalle'] = $idSalle; 

            $joursSelectionnes = isset($_POST['jour']) ? $_POST['jour'] : [];// isset ? = Récupère les jours sélectionnés ou un tableau vide
            $horairesDebut = isset($_POST['horaireD']) ? $_POST['horaireD'] : [];// isset c'est soit on prend un val soit post[nana] est vide 
            $horairesFin  = isset($_POST['horaireF']) ? $_POST['horaireF'] : [];
        
            foreach ($joursSelectionnes as $jour) {
                if (empty($jour)) continue;// Ignore les jours vides
                $idJour = (int)$jour; // Convertit en entier pour la base de données

                $debs = $horairesDebut[$jour]? $horairesDebut[$jour] : [];// Récupère les horaires de début pour ce jour ou un tableau vide
                $fins = $horairesFin[$jour]? $horairesFin[$jour] : [];// Récupère les horaires de fin pour ce jour ou un tableau vide
                
                for ($i = 0; $i < count($debs); $i++) {
                    $deb = $debs[$i] ? $debs[$i] : ''; // Récupartion de l'Horaire début selon la position i ou vide
                    $fin = $fins[$i] ? $fins[$i] : ''; // Récupartion de l'Horaire fin selon la position i ou vide
                    if (empty($deb) || empty($fin)) continue;

                    $query20= "INSERT INTO ouverture (idSalle, idJ, horaireDeb, horaireFin) VALUES ('$idSalle', '$idJour', '$deb', '$fin')";
        
                    $result20 = mysqli_query($connexion, $query20);
                    if (!$result20) {
                        die("INSERT Error ouverture (idSalle, idJ): " . mysqli_error($connexion));
                    }
                }
            }
            header('Location: InscriptionZoneProduit.php');
            
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
    <link href="bootstrap/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container mt-4">
    <div class="row text-center g-3">
        <div class="col-3"><div class="step-box">1</div></div>
        <div class="col-3"><div class="step-box step-active">2: Information Salle</div></div>
        <div class="col-3"><div class="step-box ">3</div></div>
        <div class="col-3"><div class="step-box">4</div></div>
    </div>
    </div>

    
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
                            <input type="checkbox" id="lundi" name="jour[]" value=1>
                            <label for="lundi">Lundi</label>
                            <div class = "info">
                                <div class="horaireP" id="hlundi">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[1][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[1][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('lundi',this)">+ Ajouter horaire</button>
                        </div>

                        <div class="jour-ligne">
                            <input type="checkbox" id="mardi" name="jour[]" value=2>
                            <label for="mardi">Mardi</label>
                            <div class = "info">
                                <div class="horaireP" id="hmardi">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[2][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[2][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('mardi',this)">+ Ajouter horaire</button>
                        </div>

                        <div class="jour-ligne">
                            <input type="checkbox" id="mercredi" name="jour[]" value=3>
                            <label for="mercredi">Mercredi</label>
                            <div class = "info" id="hmercredi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[3][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[3][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('mercredi',this)">+ Ajouter horaire</button>
                        </div>
                
                        <div class="jour-ligne">
                            <input type="checkbox" id="jeudi" name="jour[]" value=4>
                            <label for="jeudi">Jeudi</label>
                            <div class = "info" id="hjeudi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb " name="horaireD[4][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[4][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('jeudi',this)">+ Ajouter horaire</button>
                        </div>


                        <div class="jour-ligne">
                            <input type="checkbox" id="vendredi" name="jour[]" value=5>
                            <label for="vendredi">Vendredi</label>
                            <div class = "info" id="hvendredi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[5][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[5][]">
                                </div>
                            </div>

                            <button type="button" onclick="ajouterHoraire('vendredi',this)">+ Ajouter horaire</button>
                        </div>
                

                        <div class="jour-ligne">
                            <input type="checkbox" id="samedi" name="jour[]" value=6>
                            <label for="samedi">Samedi</label>
                            <div class = "info" id="hsamedi">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[6][]">
                                    <input type="text" placeholder="Horaire fin" name="horaireF[6][]">
                                </div>
                            </div>
                            <button type="button" onclick="ajouterHoraire('samedi',this)">+ Ajouter horaire</button>
                        </div>
                
                        <div class="jour-ligne">
                            <input type="checkbox" id="dimanche" name="jour[]" value=7>
                            <label for="dimanche">Dimanche</label>
                            <div class = "info" id="hdimanche">
                                <div class="horaireP">
                                    <input type="text" placeholder="Horaire deb" name="horaireD[7][]" >
                                    <input type="text" placeholder="Horaire fin" name="horaireF[7][]" >
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
<script src="bootstrap/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../wavetrack/java/inscription.js"></script>
