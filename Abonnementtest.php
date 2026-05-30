<?php require('php/connectdb.php'); ?>
<?php
 function validerRecap($connexion) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $idPro = $_SESSION['idPro']; // Retrieve the ID from session
    $idDev= $_SESSION['idDev'];
    $query0= "SELECT prixInstallation, prixTotal,prixAchS, idSalle FROM v_prixsalle WHERE idDev='$idDev'";
    $result0 = mysqli_query($connexion, $query0);
    $html = '';
    $index = 1;
    $prixTotal = 0;
  
    if (!$result0) {
        die("Erreur lors de la récupération des prix : " . mysqli_error($connexion));
    }
    if ($result0 && mysqli_num_rows($result0) > 0) {
        
        while($row = mysqli_fetch_assoc($result0)) {
            $rows[] = $row;
            $prixTotal += $row['prixTotal'];
            $prixInstallation = $row['prixInstallation'];}

        $prixClient= $prixTotal*1.2;
        $addDevis= "UPDATE devis SET prixTot='$prixTotal' WHERE idDev='$idDev'";
        $resultAddDevis = mysqli_query($connexion, $addDevis);
        if (!$resultAddDevis) {
            die("Erreur lors de la mise à jour du devis : " . mysqli_error($connexion));
        }

        $addPrixClient= "UPDATE devis SET prixClient='$prixClient' WHERE idDev='$idDev'";
        $resultAddPrixClient = mysqli_query($connexion, $addPrixClient);
        if (!$resultAddPrixClient) {
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
            '<p class="mb-1 fw-bold">Prix après TVA: <span class="fw-bold">  ' . htmlspecialchars($prixClient) . '</span> €</p>';

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
 validerRecap($connexion);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validerAbonnement($connexion);}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveTrack</title>
    <link rel="stylesheet" href="css/Inscription.css">
    <link rel="stylesheet" href="css/InscriptionZoneProduit.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>


    <nav class="navbar">
        <div class="logo">
            <img src="img/rasp.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        
        <ul class="nav-links">
            <li><a href="#">Accueil</a></li>
            <li><a href="#">Produits</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">À propos??</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>
    <div class="container mt-4">
    <div class="row text-center g-3">
        <div class="col-3"><div class="step-box">1</div></div>
        <div class="col-3"><div class="step-box">2</div></div>
        <div class="col-3"><div class="step-box step-active">3</div></div>
        <div class="col-3"><div class="step-box">4: Récapitulatif</div></div>
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
                                echo validerRecap($connexion);
                                ?>
                            </div>
                    </div>
                </div>


                <hr>
                <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
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
                <button class="btn btn-secondary" onclick="window.location.href='machine.php'">suivant</button>
            </div>
    </main>
    <script src="java/abonnement.js"></script>
</body>
    <footer class="text-center py-4 mt-5 border-top">
        <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia - 
        <a href="Contactavant.html">Nos Contacts</a></p>
    </footer>
</html>