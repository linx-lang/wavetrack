document.addEventListener('DOMContentLoaded', () => {
    
    // --- Graphe des temp---
fetch("php/temperatures.php")
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById("monGraphe"), {
            type: "line",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Température (°C)",
                    data: data.values,
                    borderColor: "red",
                    fill: false
                }]
            }
        });
    });

// --- Graphe alertes par machine ---
fetch("php/alerte.php")
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById("alerte"), {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Alertes par machine",
                    data: data.values,
                    backgroundColor: "rgba(255, 99, 132, 0.5)",
                    borderColor: "red",
                    borderWidth: 1
                }]
            }
        });
    });

// --- Graphe alertes par jour ---
fetch("php/alerte_jour.php")
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById("alerteJour"), {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Alertes par jour - " + data.machine,
                    data: data.values,
                    backgroundColor: "rgba(54, 162, 235, 0.5)",
                    borderColor: "blue",
                    borderWidth: 1
                }]
            }
        });
    });

// --- Graphe Voltage ---
fetch("php/voltage.php")
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById("voltage"), {
            type: "line",
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Voltage (V)",
                    data: data.values,
                    borderColor: "purple",
                    borderWidth: 2,
                    fill: false
                }]
            }
        });
    });



    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;

            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        });
    });

    const btnAjouterCreneau = document.getElementById('btn-ajouter-creneau');
    if (btnAjouterCreneau) {
        btnAjouterCreneau.addEventListener('click', () => {
            alert('Ici tu pourras ouvrir un formulaire pour ajouter un créneau.');
        });
    }

    const boutonsResolu = document.querySelectorAll('.btn-resolu');
    boutonsResolu.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.alerte').style.opacity = '0.5';
        });
    });
});
