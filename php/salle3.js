

// Make sure currentSalleId is defined
if (typeof currentSalleId === 'undefined') {
    console.error("ERROR: currentSalleId is not defined! Check your HTML.");
}
function checkAlerts() {
    const url = "salle3.php?id=" + currentSalleId + "&derniereAlerte=" + lastId;
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
    fetch('salle3.php?action=cartealerte&id=' + currentSalleId)
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
    fetch('salle3.php?action=getTemperatureData&id=' + currentSalleId)
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
    fetch('salle3.php?action=getVoltageData&id=' + currentSalleId)
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
const temperatureCharts = {};

function updateTemperatureGraphs() {
    fetch('salle3.php?action=getTemperatureGraphData&id=' + currentSalleId)
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

// Call once on page load
updateTemperatureGraphs();

setInterval(async function() {
    try {
        await checkAlerts();
        updateAlertes();
        updateDonnees();
        updateTemperatureGraphs();
    } catch (error) {
        console.error('Error:', error); // all errors surface here
    }
}, 7000);

