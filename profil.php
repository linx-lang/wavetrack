<?php
session_start();

if (!isset($_SESSION['idPro'])) {
    die("Aucun propriétaire connecté");
}

$idPro = (int) $_SESSION['idPro'];

// 🔥 Connexion BDD — tu l'avais supprimée !
$conn = mysqli_connect("localhost", "root", "", "projetsql");
if (!$conn) {
    die("Erreur connexion : " . mysqli_connect_error());
}

// --- Récupération des infos du propriétaire ---
$sqlPro = "SELECT * FROM proprietaire WHERE idPro = $idPro";
$resPro = mysqli_query($conn, $sqlPro);

if (!$resPro) {
    die("Erreur SQL propriétaire : " . mysqli_error($conn));
}

$pro = mysqli_fetch_assoc($resPro);

// --- Récupération des salles du propriétaire ---
$sqlSalles = "SELECT * FROM salle WHERE idPro = $idPro";
$resSalles = mysqli_query($conn, $sqlSalles);

if (!$resSalles) {
    die("Erreur SQL salles : " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>WaveTrack - Profil</title>
    <link rel="stylesheet" href="css/salle.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <img src="img/rasp.png" class="logo-img">
        <span class="nomSite">WaveTrack</span>
    </div>
    <ul class="nav-links">
        <li><a href="Accueil.html">Accueil</a></li>
        <li><a href="#">Produits</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">À propos</a></li>
        <li>
        <a href="profil.php" class="profil-link">
            <img src="img/user.png" class="user-img">
        </a>
</li>

    </ul>
</nav>

<main>
    <div class="conteneur">

        <!-- COLONNE GAUCHE -->
        <aside class="colonne-gauche">
            <div class="menu">
                <h1 class="onglet">Mes salles</h1>
                
            </div>

            <div class="sousmenu">
    
    <h2 class="titre-sousmenu">Mes salles</h2>

    <div class="liste-salles">
        <?php while ($s = mysqli_fetch_assoc($resSalles)): ?>
            <div class="carte-salle">
                <h3><?= htmlspecialchars($s['nomS']) ?></h3>
                <p><?= htmlspecialchars($s['lieus']) ?></p>

                <a href="salle.php?salle=<?= $s['idSalle'] ?>" class="btn-voir">
                    <a href="salle.php?salle=<?= $s['idSalle'] ?>" class="btn-voir">Voir</a>

                </a>
            </div>
        <?php endwhile; ?>
   

        </aside>

        <!-- CONTENU PRINCIPAL -->
        <section class="infos">
            <div class="header-salle">
                <div>
                    <h1><?= htmlspecialchars($pro['prenomP'] . " " . $pro['nomP']) ?></h1>
                    <p class="adresse"><?= htmlspecialchars($pro['mailP']) ?></p>
                    <p class="maj">Dernière mise à jour : il y a 2 min</p>
                </div>

                <a href="changement_donnee_profil.php" class="btn-ajouter-salle">
                    <span class="plus">+</span> changer les données du profil</a>
            </div>

            <div class="cards">
                <div class="card card-profil">
                <div class="infos-profil">
                    <h3>Informations personnelles</h3>
                    <p><strong>Nom :</strong> <?= $pro['nomP'] ?></p>
                    <p><strong>Prénom :</strong> <?= $pro['prenomP'] ?></p>
                    <p><strong>Email :</strong> <?= $pro['mailP'] ?></p>
                    <p><strong>Téléphone :</strong> <?= $pro['numP'] ?: "Non renseigné" ?></p>
                </div>

                <div class="photo-profil">
                    <img src="img/user.png" alt="Photo de profil">
                </div>
</div>

                <div class="card">
                    <h3>Abonnement</h3>
                    <p><strong>Nom :</strong> <?= $pro['nomAb'] ?: "Aucun" ?></p>
                    <p><strong>Date :</strong> <?= $pro['dateAb'] ?: "Non renseignée" ?></p>
                    <p><strong>Nombre de salles :</strong> <?= $pro['nb_salle'] ?: 0 ?></p>
                    <p><strong>Prix :</strong> <?= $pro['prixAbon'] ? $pro['prixAbon']." €" : "Non renseigné" ?></p>
                </div>
            </div>

        </section>
    </div>
</main>

<footer>
    <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia -
        <a href="Contactapres.html" class="contactfooter">Nos Contacts</a>
    </p>
</footer>

</body>
</html>
