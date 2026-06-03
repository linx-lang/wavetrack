<?php require('php/connectdb.php'); ?>
<?php
 function validerRecap($connexion) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $idPro = $_SESSION['idPro']; // Retrieve the ID from session
    $idDev= $_SESSION['idDev'];
    $query0= "SELECT prixInstallation, prixTotal,prixAchS, prixMois, idSalle FROM v_prixsalle WHERE idDev='$idDev'";
    $result0 = mysqli_query($connexion, $query0);
    $html = '';
    $index = 1;
    $prixTotal = 0;
    $prixInstallation = 0;
    
  
    if (!$result0) {
        die("Erreur lors de la récupération des prix : " . mysqli_error($connexion));
    }
    if ($result0 && mysqli_num_rows($result0) > 0) {
        
        while($row = mysqli_fetch_assoc($result0)) {
            $rows[] = $row;
            $prixTotal += $row['prixTotal'];
            $prixInstallation += $row['prixInstallation'];
            $prixMois = $row['prixMois'];
            }
            

        $prixClient= $prixTotal*1.2;
    
        
        $ajouterDevis= "UPDATE devis SET prixTot='$prixTotal' WHERE idDev='$idDev'";
        $resultAjouterDevis = mysqli_query($connexion, $ajouterDevis);
        if (!$resultAjouterDevis) {
            die("Erreur lors de la mise à jour du devis : " . mysqli_error($connexion));
        }

        $ajouterPrixClient= "UPDATE devis SET prixClient='$prixClient', prixMois='$prixMois' WHERE idDev='$idDev'";
        $resultAjouterPrixClient = mysqli_query($connexion, $ajouterPrixClient);
        if (!$resultAjouterPrixClient) {
            die("Erreur lors de la mise à jour du prix client : " . mysqli_error($connexion));
        }

        foreach( $rows as $row) {    
            $prixInstallation = $row['prixInstallation'];
            $prixAchS = $row['prixAchS'];
            $idSalle = $row['idSalle'];
            $html.= '<p class="mb-1 fw-bold"> Salle: <span class="fw-bold">' . $index . '</span></p>' .'<br>' .
            '<p class="mb-1">Prix installation: <span class="fw-bold">' . htmlspecialchars($prixInstallation) . '</span> €</p>' .'<br>' .
            '<p class="mb-1">Prix achat: <span class="fw-bold">' . htmlspecialchars($prixAchS) . '</span> €</p>'.'<br>' .
            '<p class="mb-1">Prix total: <span class="fw-bold">' . htmlspecialchars($row['prixTotal']) . '</span> €</p>'.'<br>' .
            '<p class="mb-1 fw-bold">Prix après TVA: <span class="fw-bold">  ' . htmlspecialchars($prixClient) . '</span> €</p>' .
            $index++;

        }

    }
    else {
        $html.='<p class="mb-1">Salles: <span class="fw-bold">Aucun</span></p>';
    }
    return $html;
 }
  function validerAbonnement($connexion) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $nombre_salles   = isset($_POST['nombre_salles'])   ? $_POST['nombre_salles']   : null;
    $abonnement_type = isset($_POST['abonnement_type']) ? $_POST['abonnement_type'] : null;
    $prixAbon=$_POST['prix'];

    $idPro = $_SESSION['idPro']; // Retrieve the ID from session

    $query3 = "UPDATE proprietaire SET nomAb = '$abonnement_type', nb_salle = '$nombre_salles' , prixAbon = '$prixAbon' WHERE idPro = '$idPro'";
    $result3=mysqli_query($connexion, $query3);
    if (!$result3) {
        die("UPDATE Error: " . mysqli_error($connexion));
        }
 }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        validerAbonnement($connexion);
        validerRecap($connexion);
}
   
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveTrack</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Inscription.css">
    <link rel="stylesheet" href="css/header.css">
    </head>
<body>


    <nav class="navbar">
        <div class="logo">
            <img src="img/logo.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        
        <ul class="nav-links">
            <li><a href="#">Accueil</a></li>
            
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>
    <div class="container mt-4">
    <div class="row text-center g-3">
        
    <div class="col-3"><div class="step-box">1</div></div>
    <div class="col-3"><div class="step-box">2</div></div>
    <div class="col-3"><div class="step-box">3</div></div>
    <div class="col-3"><div class="step-box step-active">4 : Récapitulatif</div></div>

    </div>

    <main>

            <form class= "container" method="POST" action="Abonnementtest.php">
                <div class="row g-4"> 
                    <div class="col-md-6 col-sm-12">
                        <div class="selection-card p-4 shadow-sm border rounded bg-white">
                        <p class="text-uppercase text-muted small fw-bold mb-1">Étape 01</p>
                        <h4 class="mb-3">Nombre de salles</h4>
                        <input type="number" class="form-control form-control-sm mb-4" id="nbsalle" name="nombre_salles" required min="1" placeholder="Entrez le nombre de salles">

                        <p class="text-uppercase text-muted small fw-bold mb-1">Étape 02</p>
                        <h4 class="mb-3">Choisissez votre abonnement</h4>

                        <div class="plan-option p-3 mb-2 border rounded">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abonnement_type" id="classique" value="classique" checked>
                                <label class="form-check-label fw-bold" for="classique">Classique <input  id="prix-classique" value="" name="prix" readonly class="text-primary border-0 bg-transparent p-0 fw-bold">€ par mois</label>
                            </div>
                            <ul class="small text-muted mt-2 mb-0">
                                <li>Maintenance tous les 6 mois</li>
                                <li>Assistance par email</li>
                            </ul>
                        </div>

                        <div class="plan-option p-3 mb-2 border rounded">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abonnement_type" id="complet" value="complet">
                                <label class="form-check-label fw-bold" for="complet">Complet <input  id="prix-complet" value="" name="prix" readonly class="text-primary border-0 bg-transparent p-0 fw-bold">€ par mois</label>
                            </div>
                            <ul class="small text-muted mt-2 mb-0">
                                <li>Maintenance trimestrielle</li>
                                <li>Rapport d'analyse de données</li>
                            </ul>
                        </div>
            
            

                        <div class="plan-option p-3 mb-2 border rounded">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="abonnement_type" id="expert" value="expert">
                                <label class="form-check-label fw-bold" for="expert">Expert <input  id="prix-expert" value="" name="prix" readonly class="text-primary border-0 bg-transparent p-0 fw-bold">€ par mois</label>
                            </div>
                            <ul class="small text-muted mt-2 mb-0">
                                <li>Maintenance tous les 2 mois</li>
                                <li>Rapport d'analyse de données</li>
                                <li>Étude & recommandations</li>
                                <li>Assurance incluse</li>
                            </ul>
                        </div>
                        </div>
                    </div>
            
            
                    <div class="col-md-5 col-sm-12">
                        <div class="summary-card p-4 shadow-sm border rounded bg-light sticky-top" style="top: 20px;">
                            <h5 class="border-bottom pb-2 mb-3">Récapitulatif</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Salles:</span>
                                <span id="summary-rooms" class="fw-bold">—</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Offre choisie:</span>
                                <span id="summary-plan" class="fw-bold text-primary">Classique</span>
                                
                            </div>
                            <div class="info mb-4" role="">
                            <h6 class="heading">Résumé des achats</h6>
                            <div class="content" id="info-achat">
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    echo validerRecap($connexion);
                                }
                                ?>
                            </div>
                    </div>
                </div>


                <hr>
                <button type="submit" class="btn-facture">
    Voir la facture détaillée
</button>

                <p class="text-center small text-muted mt-3">
                    <i class="bi bi-shield-check"></i> Paiement sécurisé
                </p>
                </div>
                </div>
                </div>

            </form>
                    <div class="container mt-5 d-flex justify-content-end">
                <a href="machine.php" class="btn-suivant">Suivant</a>
            </div>

    </main>

</body>
</html>

<footer>
        <p>Nos Contacts</p>
        <p>Téléphone: 06.05.04.03.02 - Mail: Contact@wavetrack.fr</p>
        <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia</p>
            <img src="img/miage.jpg" alt="fac" class="fac-img" style="width: 10%;   object-position: right;">
            <img src="img/universite.png" alt="fac" class="fac-img" style="width: 10%; object-position: left;">
    </footer>
<script src="java/abonnement.js"></script>
</body>
</html>
