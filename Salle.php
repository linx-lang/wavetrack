<?php
// --- Simulation d'un propriétaire connecté ---
session_start();
// Normalement : $_SESSION['user_id'] etc.
$userId = 1;

// Simulation des salles du propriétaire
$salles = [
    ['id' => 1, 'nom' => 'Salle 1', 'adresse' => '--- rue -- --------------, Toulouse, 31---'],
    ['id' => 2, 'nom' => 'Salle 2', 'adresse' => '--- avenue -- --------------, Toulouse, 31---'],
];

$salleId = isset($_GET['salle']) ? (int)$_GET['salle'] : $salles[0]['id'];
$salleCourante = null;
foreach ($salles as $salle) {
    if ($salle['id'] === $salleId) {
        $salleCourante = $salle;
        break;
    }
}
if (!$salleCourante) {
    $salleCourante = $salles[0];
}

// --- Simulation de données capteurs ---
$temperatureActuelle = 22;
$temperatureMoyenneJour = 20;
$portesOuverturesJour = 14;
$porteEtat = "Fermée";
$humidite = 45;
$son = 32;

// --- Simulation de planning ---
$creneaux = [
    ['date' => '2026-04-18', 'heure' => '20:00', 'evenement' => 'Concert Rock'],
    ['date' => '2026-04-19', 'heure' => '18:00', 'evenement' => 'Répétition'],
];

// --- Simulation d’alertes ---
$alertes = [
    ['type' => 'Surchauffe', 'message' => 'Température supérieure à 28°C', 'niveau' => 'Élevé', 'date' => '2026-04-16 21:32'],
    ['type' => 'Porte ouverte', 'message' => 'Porte principale ouverte après fermeture', 'niveau' => 'Moyen', 'date' => '2026-04-15 02:10'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>WaveTrack - Salle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/salle.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <img src="img/rasp.png" alt="Logo" class="logo-img">
            <span class="nomSite">WaveTrack</span>
        </div>
        <ul class="nav-links">
            <li><a href="Accueil.html">Accueil</a></li>
            <li><a href="#">Produits</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">À propos</a></li>
            <li><img src="img/user.png" alt="Profile" class="user-img"></li>
        </ul>
    </nav>

    <main>
        <div class="conteneur">
            <aside class="colonne-gauche">
                <div class="menu">
                    <h1 class="onglet">Salles</h1>
                </div>
                <div class="sousmenu">
                    <?php foreach ($salles as $salle): ?>
                        <a href="salle.php?salle=<?= $salle['id'] ?>" 
                           class="sous-onglet <?= $salle['id'] === $salleCourante['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($salle['nom']) ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="ajouter_salle.php" class="sous-onglet ajouter">+ Ajouter une salle</a>
                </div>
            </aside>

            <section class="infos">
                <div class="header-salle">
                    <div>
                        <h1><?= htmlspecialchars($salleCourante['nom']) ?></h1>
                        <p class="adresse"><?= htmlspecialchars($salleCourante['adresse']) ?></p>
                        <p class="maj">Dernière mise à jour : il y a 5 min</p>
                    </div>
                    <button class="edit-btn">Modifier la salle</button>
                </div>
                <div class="slider">
                    <input type="radio" name="slider" id="s1" checked>
                    <input type="radio" name="slider" id="s2">
                    <input type="radio" name="slider" id="s3">
                    <input type="radio" name="slider" id="s4">
                    <input type="radio" name="slider" id="s5">

                    <div class="slides">
                        <div class="slide" style="background-image:url('img/imgSalle-1.png');"></div>
                        <div class="slide" style="background-image:url('img/imgSalle-2.png');"></div>
                        <div class="slide" style="background-image:url('img/imgSalle-3.png');"></div>
                        <div class="slide" style="background-image:url('img/imgSalle-4.png');"></div>
                        <div class="slide" style="background-image:url('img/imgSalle-5.png');"></div>
                    </div>

                    <div class="thumbnails">
                        <label for="s1" style="background-image:url('img/imgSalle-1.png');"></label>
                        <label for="s2" style="background-image:url('img/imgSalle-2.png');"></label>
                        <label for="s3" style="background-image:url('img/imgSalle-3.png');"></label>
                        <label for="s4" style="background-image:url('img/imgSalle-4.png');"></label>
                        <label for="s5" style="background-image:url('img/imgSalle-5.png');"></label>
                    </div>
                </div>

                <div class="tabs">
                    <button class="tab-btn active" data-tab="analyse">Analyse</button>
                    <button class="tab-btn" data-tab="planning">Planning</button>
                    <button class="tab-btn" data-tab="alertes">Alertes</button>
                </div>

                <!-- Contenu Analyse -->
                <div class="tab-content active" id="tab-analyse">
                    <div class="cards">
                        <div class="card">
                            <h3>Température</h3>
                            <p><strong>Actuelle :</strong> <?= $temperatureActuelle ?> °C</p>
                            <p><strong>Moyenne du jour :</strong> <?= $temperatureMoyenneJour ?> °C</p>
                            <div class="mini-graph">Graphique à venir</div>
                        </div>

                        <div class="card">
                            <h3>Portes</h3>
                            <p><strong>Ouvertures aujourd’hui :</strong> <?= $portesOuverturesJour ?></p>
                            <p><strong>État actuel :</strong> <?= $porteEtat ?></p>
                        </div>

                        <div class="card">
                            <h3>Autres capteurs</h3>
                            <p><strong>Humidité :</strong> <?= $humidite ?> %</p>
                            <p><strong>Niveau sonore :</strong> <?= $son ?> dB</p>
                        </div>
                    </div>
                </div>

                <!-- Contenu Planning -->
                <div class="tab-content" id="tab-planning">
                    <div class="planning-header">
                        <h2>Planning</h2>
                        <button class="boutton" id="btn-ajouter-creneau">Ajouter un créneau</button>
                    </div>
                    <table class="planning-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Événement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($creneaux as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['date']) ?></td>
                                    <td><?= htmlspecialchars($c['heure']) ?></td>
                                    <td><?= htmlspecialchars($c['evenement']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Contenu Alertes -->
                <div class="tab-content" id="tab-alertes">
                    <h2>Alertes</h2>
                    <?php if (empty($alertes)): ?>
                        <p>Aucune alerte pour le moment.</p>
                    <?php else: ?>
                        <ul class="liste-alertes">
                            <?php foreach ($alertes as $a): ?>
                                <li class="alerte alerte-<?= strtolower($a['niveau']) ?>">
                                    <div class="alerte-header">
                                        <span class="type"><?= htmlspecialchars($a['type']) ?></span>
                                        <span class="niveau"><?= htmlspecialchars($a['niveau']) ?></span>
                                    </div>
                                    <p><?= htmlspecialchars($a['message']) ?></p>
                                    <span class="date"><?= htmlspecialchars($a['date']) ?></span>
                                    <button class="btn-resolu">Marquer comme résolue</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </section>
        </div>
    </main>

    <footer>
        <p>Kozlov-mercier Nina - Issa Lina - Akbaba Julia - Doumergue Louise - Girard Alexia - 
            <a href="Contactapres.html" class="contactfooter">Nos Contacts</a>
        </p>
    </footer>

    <script src="js/salle.js"></script>
</body>
</html>
