

// Make sure currentSalleId is defined
if (typeof currentSalleId === 'undefined') {
    console.error("ERROR: currentSalleId is not defined! Check your HTML.");
}
function checkAlerts() {
    const url = "Salle.php?id=" + currentSalleId + "&derniereAlerte=" + lastId;
    console.log("Checking alerts with URL:", url);
    console.log("Checking with lastId:", lastId);

    fetch(url)
        .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
                throw new Error("HTTP error, status: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);

            // show bootstrap modal only if ID changed
            if (data && data.idAlerte && data.idAlerte != lastId) {

                // update modal content
                document.getElementById("alerteMessage").innerText = data.message;

                // open Bootstrap popup
                const modal = new bootstrap.Modal(
                    document.getElementById("alerteModal")
                );

                modal.show();

                // update remembered ID
                lastId = data.idAlerte;

                console.log("Alert ID updated:", lastId);
            }
        })
}
function updateAlertes() {
    fetch('Salle.php?action=cartealerte&id=' + currentSalleId)
        .then(response => response.json())
        .then(alertes => {
            const list = document.querySelector('#alertes .list-group');
            if (!list) return;

            list.innerHTML = ''; // clear current cards

            if (alertes.length === 0) {
                list.innerHTML = '<p class="text-muted">Aucune alerte.</p>';
                return;
            }

            alertes.forEach(alerte => {
                list.innerHTML += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Numéro de l'alerte: ${alerte.idA}</h5>
                            <p><strong>ID machine:</strong> ${alerte.idM}</p>
                            <p><strong>Date:</strong> ${alerte.date}</p>
                            <p><strong>Valeur:</strong> ${alerte.ValeurA}</p>
                        </div>
                        <form method="POST" action="resoudre_alerte.php">
                            <input type="hidden" name="id" value="${alerte.idA}">
                            <button class="btn btn-danger btn-sm">Résoudre</button>
                        </form>
                    </div>`;
            });
        })
        .catch(error => console.error('Error refreshing alertes:', error));
}
function updateDonnees() {
    fetch('Salle.php?action=getTemperatureData&id=' + currentSalleId)
        .then(response => response.json())
        .then(donnees => {
            donnees.forEach(d => {
                // update value element by capteur id
                const valeurEl = document.getElementById('valeurt_' + d.idCap);
                const moyenneEl = document.getElementById('moyennet_' + d.idCap);
                if (valeurEl) valeurEl.innerText = d.valeur;
                if (moyenneEl) moyenneEl.innerText = d.moyenne;
            });
        })
        .catch(error => console.error('Error refreshing donnees:', error));
    fetch('Salle.php?action=getVoltageData&id=' + currentSalleId)
        .then(response => response.json())
        .then(donnees => {
            donnees.forEach(d => {
                // update value element by capteur id
                const valeurE2 = document.getElementById('valeurv_' + d.idCap);
                const moyenneE2 = document.getElementById('moyennev_' + d.idCap);
                if (valeurE2) valeurE2.innerText = d.valeur;
                if (moyenneE2) moyenneE2.innerText = d.moyenne;
            });
        })
        .catch(error => console.error('Error refreshing voltage data:', error));
}
function updateClim() {
    fetch('Salle.php?action=getclim&id=' + currentSalleId)
        .then(response => response.json())
        .then(data => {
            const climEl = document.getElementById('climState');
            if (climEl) {
                climEl.innerText = data.clim ? 'Allumée' : 'Éteinte';
                climEl.className = data.clim ? 'text-success' : 'text-danger';
            }
        })
        .catch(error => console.error('Error refreshing clim state:', error));
}
const temperatureCharts = {};
const voltageCharts = {};
let alerteChart = null;
function calculernbVestiaire() {
    fetch('Salle.php?action=calculernbVestiaire&id=' + currentSalleId)
        .then(response => response.json())
        .then(nbVestiaires => {
            const vestiaireEl = document.getElementById('nbVestiaires');
            if (vestiaireEl) {
                vestiaireEl.innerText = nbVestiaires.vestiaires;
            }
        })
        .catch(error => console.error('Error refreshing nbVestiaires:', error));
}

function updateTemperatureGraphs() {
    fetch('Salle.php?action=getTemperatureGraphData&id=' + currentSalleId)
        .then(response => response.json())
        .then(capteurs => {
            capteurs.forEach(cap => {
                const canvas = document.getElementById('chart_' + cap.idCap);
                if (!canvas) return; // canvas doesn't exist for this capteur, skip

                const ctx = canvas.getContext('2d');

                if (temperatureCharts[cap.idCap]) {
                    // Just update data, no flicker
                    temperatureCharts[cap.idCap].data.labels = cap.labels;
                    temperatureCharts[cap.idCap].data.datasets[0].data = cap.values;
                    temperatureCharts[cap.idCap].update();
                } else {
                    // Create chart once
                    temperatureCharts[cap.idCap] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: cap.labels,
                            datasets: [{
                                label: 'Température (°C)',
                                data: cap.values,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { title: { display: true, text: '°C' } }
                            }
                        }
                    });
                }
            });
        })
        .catch(error => console.error('Error refreshing temperature graphs:', error));
}

function updateVoltageGraphs() {
    fetch('Salle.php?action=getVoltageGraphData&id=' + currentSalleId)
        .then(response => response.json())
        .then(capteurs => {
            capteurs.forEach(cap => {
                const canvas = document.getElementById('chart_volt_' + cap.idCap);
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                if (voltageCharts[cap.idCap]) {
                    voltageCharts[cap.idCap].data.labels = cap.labels;
                    voltageCharts[cap.idCap].data.datasets[0].data = cap.values;
                    voltageCharts[cap.idCap].update();
                } else {
                    voltageCharts[cap.idCap] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: cap.labels,
                            datasets: [{
                                label: 'Voltage (V)',
                                data: cap.values,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { title: { display: true, text: 'V' } }
                            }
                        }
                    });
                }
            });
        })
        .catch(error => console.error('Error refreshing voltage graphs:', error));
}
function loadAlerteChart() {

    fetch('Salle.php?action=alerteparmachine&id=' + currentSalleId)

        .then(response => response.json())

        .then(data => {

            const canvas = document.getElementById('graph_alertes');

            if (!canvas) return;



            const ctx = canvas.getContext('2d');



            if (alerteChart) {

                // Update existing chart without recreating it

                alerteChart.data.labels = data.labels;

                alerteChart.data.datasets[0].data = data.values;

                alerteChart.update();

            } else {

                // Create chart on first call

                alerteChart = new Chart(ctx, {

                    type: 'bar',

                    data: {

                        labels: data.labels,

                        datasets: [{

                            label: "Nombre d'alertes",

                            data: data.values,

                            backgroundColor: 'rgba(255, 99, 132, 0.6)',

                            borderColor: 'rgba(255, 99, 132, 1)',

                            borderWidth: 1,

                            borderRadius: 4

                        }]

                    },

                    options: {

                        responsive: true,

                        plugins: {

                            legend: { display: false },

                            title: {

                                display: true,

                                text: "Alertes par machine"

                            }

                        },

                        scales: {

                            y: {

                                beginAtZero: true,

                                ticks: { stepSize: 1 },

                                title: { display: true, text: "Nombre d'alertes" }

                            },

                            x: {

                                title: { display: true, text: "Machine" }

                            }

                        }

                    }

                });

            }

        })

        .catch(error => console.error('Erreur chargement graphique alertes:', error));

}
const alerteParJourCharts = {};

function loadAlerteParJourChart(idM) {
    fetch('Salle.php?action=alerteparmachineParJour&id=' + currentSalleId + '&idM=' + idM)
        .then(response => response.json())
        .then(data => {
            const canvas = document.getElementById('graph_alertes_jour_' + idM);
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            if (alerteParJourCharts[idM]) {
                alerteParJourCharts[idM].data.labels = data.labels;
                alerteParJourCharts[idM].data.datasets[0].data = data.values;
                alerteParJourCharts[idM].update();
            } else {
                alerteParJourCharts[idM] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: data.machine,
                            data: data.values,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
                            title: { display: true, text: 'Alertes par jour — ' + data.machine }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                title: { display: true, text: "Nombre d'alertes" }
                            },
                            x: {
                                title: { display: true, text: 'Date' }
                            }
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Erreur graphique alertes par jour:', error));
}


// telechargement initiale
updateTemperatureGraphs();
updateVoltageGraphs();
// Charger le graphique alertes au clic sur l'onglet
document.addEventListener("DOMContentLoaded", function () {
    const alerteTab = document.querySelector('[data-bs-target="#alertes"]');
    if (alerteTab) {
        alerteTab.addEventListener('shown.bs.tab', function () {
            loadAlerteChart();
            alerteMachineIds.forEach(id => loadAlerteParJourChart(id));
        });
    }
});
setInterval(async function() {
    try {
        await checkAlerts();
        updateAlertes();
        updateDonnees();
        updateTemperatureGraphs();
        updateVoltageGraphs();
        calculernbVestiaire();
        updateClim();
        if (document.getElementById('alertes')?.classList.contains('active')) {
        loadAlerteChart();
        }
        
    } catch (error) {
        console.error('Error:', error); // all errors surface here
    }
}, 7000);

//creneaux
// Ajoute un zéro devant les heures (exemple : 2h -> 02h ou 0h ->00h)
function pad(n) {
    return (n < 10 ? "0" : "") + n;
}

// Ouvre le modal de modification
// Récupération le créneau correspondant à l'idC dans la liste globale PLANNING_SLOTS
function ouvreModif(idC) {
    let creneau = window.PLANNING_SLOTS.find(s => s.idC == idC);
    if (!creneau) {
        return; // Si le créneau n'est pas trouvé, on ne fait rien
    }

    // Préremplissage des champs du formulaire du modal
    document.getElementById("modifIdC").value = creneau.idC;
    document.getElementById("modifNomArt").value = creneau.artist;
    document.getElementById("modifDate").value = creneau.date;
    document.getElementById("modifHeure").value = creneau.heureDebutCreneau;

    // Afficher le modal/pop-up
    var modal = new bootstrap.Modal(document.getElementById("modifModal"));
    modal.show();
}

// Ouvre le modal de suppression 
function ouvreSupp(idC) {
    let creneau = window.PLANNING_SLOTS.find(s => s.idC == idC);
    if (!creneau) {
        return; // Si le créneau n'est pas trouvé, on ne fait rien
    }

    // On remplit les champs du modal de suppression avec les infos du créneau à supprimer
    document.getElementById("suppIdC").value = idC;

    // Afficher le modal/pop-up
    new bootstrap.Modal(document.getElementById("suppModal")).show();
}

/* ----------------------------------------------------
   CODE QUI S'EXÉCUTE AU CHARGEMENT DE LA PAGE 
---------------------------------------------------- */

document.addEventListener("DOMContentLoaded", function () {

    /* --- Construction du planning --- */

    // Date du lundi de la semaine affichée
    const lundi = new Date(window.PLANNING_MONDAY);
    
    // Liste des créneaux envoyés par le PHP
    const listeCreneaux = window.PLANNING_SLOTS;

    // Récupération du tableau de l'emploi du temps HTML
    const table = document.getElementById("timetable");
    const entete = table.querySelector("thead tr");
    const body = table.querySelector("tbody");

    // Calcul des 7 jours de la semaine à partir du lundi
    let semaine = [];
    for (let i = 0; i < 7; i++) {
        let jour = new Date(lundi); 
        jour.setDate(lundi.getDate() + i);
        semaine.push(jour);
    }
    
    // On supprime tous les th sauf le premier ("Heure")
    while (entete.children.length > 1) {
        entete.removeChild(entete.lastChild);
    }
    
    // Ajout des jours dans l’en-tête du tableau
    semaine.forEach(d => {
        let th = document.createElement("th");

        th.textContent = d.toLocaleDateString("fr-FR", {
            weekday: "short", // Pour n’avoir que les 3 premières lettres du jour de la semaine
            day: "2-digit", // Pour avoir le jour sur 2 chiffres
            month: "2-digit" // Pour avoir le mois sur 2 chiffres
        });
        th.className = "jour-col";
        entete.appendChild(th);
    });

    // Ajout des lignes horaires 
    for (let heure = 0; heure < 24; heure += 1) {
        let tr = document.createElement("tr");

        // Création de la colonne Heure
        let tdHeure = document.createElement("td");
        tdHeure.className = "time-col";
        tdHeure.textContent = pad(heure) + "h";
        tr.appendChild(tdHeure);
        
        // Date du jour actuel
        let aujourdhui = new Date();
        aujourdhui.setHours(0,0,0,0);
        
        // Création des colonnes pour chaque jour
        for (let jour = 0; jour < 7; jour++) {
            let td = document.createElement("td");
            td.dataset.day = jour;  // index de chaque jour (0 pour lundi, 1 pour mardi, etc.)
            td.dataset.hour = heure; // heure à chaque ligne

            // On crée une date propre
            let celluleDate = new Date(
                            semaine[jour].getFullYear(), 
                            semaine[jour].getMonth(), 
                            semaine[jour].getDate()
                            );

            // On grise la cellule des jours passés
            if (celluleDate < aujourdhui) {
                td.style.pointerEvents = "none";
                td.style.opacity = "0.4";   
                td.style.background = "#EDE6E6";
            }

            // Désactiver les heures en dehors des horaires d'ouverture

            // Jour de la semaine (1=lundi, 7=dimanche)
            let jourSemaine = semaine[jour].getDay(); // 0=dimanche
            jourSemaine = jourSemaine === 0 ? 7 : jourSemaine; // Si on a 0 alors on le convertit en 7

            // Heures d’ouverture récupérées depuis PHP
            let deb = ouvertureSalleparJourDebut[jourSemaine];
            let fin = ouvertureSalleparJourFin[jourSemaine];

            // Si l’heure est avant/après l’ouverture/fermeture ou que les horaires de la salle ne sont pas définis
            if ((heure < deb || heure >= fin) || ouvertureSalleparJourDebut[jourSemaine] === undefined || ouvertureSalleparJourFin[jourSemaine] === undefined) {
                // On grise la cellule et on empêche les clics
                td.style.pointerEvents = "none";
                td.style.opacity = "0.4";   
                td.style.background = "#EDE6E6";
            }
            tr.appendChild(td);
        }

        body.appendChild(tr);
    }

    // Placement des créneaux dans le tableau
    listeCreneaux.forEach(s => {

        // On convertit la date du créneau en objet Date pour trouver son index dans la semaine
        let date = new Date(s.date);
        let indiceJour = semaine.findIndex(jour => jour.toDateString() === date.toDateString()); // N'y a-t-il pas un moyen plus simple pour faire cette comparaison ?  
        if (indiceJour === -1){ // Si la date du créneau ne correspond à aucun jour de la semaine
            return; // On ignore ce créneau
        }

        let heureDebutCreneau = s.heureDebutCreneau; 

        // Récupèration de la cellule de départ du créneau
        let celluleDebut = document.querySelector(
            `td[data-day='${indiceJour}'][data-hour='${heureDebutCreneau}']`
        );
        
        // Cas général
        if (heureDebutCreneau <= 21) {
            let heureFinCreneau = heureDebutCreneau + 2;

            // Récupèration de la cellule suivante (à supprimer)
            let celluleSuivante = document.querySelector(
                `td[data-day='${indiceJour}'][data-hour='${heureDebutCreneau + 1}']`
            );

            if (!celluleDebut || !celluleSuivante) return;

            // On supprimer la cellule suivante pour éviter un trou
            celluleSuivante.remove();

            // Création du bloc du créneau 
            let div = document.createElement("div");
            div.className = "slot"; // Pas sûr du nom de la classe CSS à mettre
            div.innerHTML = `
                <p><b>${s.artist}</b><br>${pad(heureDebutCreneau)}h - ${pad(heureFinCreneau)}h</p>
                <button type="button" class="btn btn-light btn-sm boutonCreneau" onclick="ouvreModif(${s.idC})" data-bs-toggle="modal" data-bs-target="#modifModal">Modifier</button>
                <button type="button" class="btn btn-danger btn-sm boutonCreneau" onclick="ouvreSupp(${s.idC})" data-bs-toggle="modal" data-bs-target="#suppModal">Supprimer</button>
                `;
            
            // Faire occuper 2 lignes (2 heures)
            celluleDebut.rowSpan = 2;
            celluleDebut.appendChild(div);
        } 
        // Cas un peu particulier (Affichage de 22h - 00h au lieu de 22h - 24h)
        if (heureDebutCreneau === 22) {
            // Récupèration de la cellule suivante (à supprimer)
            let celluleSuivante = document.querySelector(
                `td[data-day='${indiceJour}'][data-hour='23']`
            );

            if (!celluleDebut || !celluleSuivante) {
                return; 
            }

            // Supprimer la cellule suivante pour éviter un trou
            celluleSuivante.remove();

            // Création du bloc du créneau
            let div = document.createElement("div");
            div.className = "slot"; // Pas sûr du nom de la classe CSS à mettre
            // Affichage de 22h - 00h au lieu de 22h - 24h
            div.innerHTML = `
                <p><b>${s.artist}</b><br>22h - 00h</p>
                <button type="button" class="btn btn-light btn-sm boutonCreneau" onclick="ouvreModif(${s.idC})" data-bs-toggle="modal" data-bs-target="#modifModal">Modifier</button>
                <button type="button" class="btn btn-danger btn-sm boutonCreneau" onclick="ouvreSupp(${s.idC})" data-bs-toggle="modal" data-bs-target="#suppModal">Supprimer</button>
                `;
                
            // Faire occuper 2 lignes (2 heures)
            celluleDebut.rowSpan = 2;
            celluleDebut.appendChild(div);
        }

    });

});