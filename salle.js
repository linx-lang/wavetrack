/* ----------------------------------------------------
   FONCTIONS GLOBALES (accessibles depuis le HTML)
---------------------------------------------------- */

// Ajoute un zéro devant les heures (ex : 2 → 02)
function pad(n) {
    return (n < 10 ? "0" : "") + n;
}

// Ouvre le modal de modification
function ouvreModif(idC) {
    let slot = window.PLANNING_SLOTS.find(s => s.idC == idC);
    if (!slot) return;

    document.getElementById("editIdC").value = slot.idC;
    document.getElementById("editIdArt").value = slot.idArt;

    // Calcul des jours de la semaine
    let monday = new Date(window.PLANNING_MONDAY);
    let days = [];
    for (let i = 0; i < 7; i++) {
        let d = new Date(monday);
        d.setDate(monday.getDate() + i);
        days.push(d);
    }

    // Trouver le jour correspondant
    let date = new Date(slot.date);
    let dayIndex = days.findIndex(d => d.toDateString() === date.toDateString());

    document.getElementById("editDay").value = dayIndex;
    document.getElementById("editHour").value = slot.startHour;

    // Préparer les champs cachés
    document.querySelector("#editModal form").onsubmit = function () {
        let d = days[document.getElementById("editDay").value];
        document.getElementById("editDate").value = d.toISOString().split("T")[0];
        document.getElementById("editStartHour").value = document.getElementById("editHour").value;
    };

    new bootstrap.Modal(document.getElementById("editModal")).show();
}

// Ouvre le modal de suppression
function ouvreSupp(idC) {
    let slot = window.PLANNING_SLOTS.find(s => s.idC == idC);
    if (!slot) return;

    document.getElementById("deleteIdC").value = idC;
    document.getElementById("deleteText").textContent =
        slot.artist + " – " + slot.date + " – " + slot.startHour + "h";

    new bootstrap.Modal(document.getElementById("deleteModal")).show();
}


/* ----------------------------------------------------
   CODE QUI S'EXÉCUTE AU CHARGEMENT DE LA PAGE
---------------------------------------------------- */

document.addEventListener("DOMContentLoaded", function () {

    console.log("JS simple chargé ✔️");

    /* --- Gestion des onglets --- */
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const tab = btn.dataset.tab;

            tabButtons.forEach(b => b.classList.remove("active"));
            tabContents.forEach(c => c.classList.remove("active"));

            btn.classList.add("active");
            document.getElementById("tab-" + tab).classList.add("active");
        });
    });

    /* --- Construction du planning --- */

    const monday = new Date(window.PLANNING_MONDAY);
    const slots = window.PLANNING_SLOTS;

    const table = document.getElementById("timetable");
    const headRow = table.querySelector("thead tr");
    const body = table.querySelector("tbody");

    // Calcul des 7 jours de la semaine
    let days = [];
    for (let i = 0; i < 7; i++) {
        let d = new Date(monday);
        d.setDate(monday.getDate() + i);
        days.push(d);
    }

    // Ajout des jours dans le header
    days.forEach(d => {
        let th = document.createElement("th");
        th.textContent = d.toLocaleDateString("fr-FR", {
            weekday: "short",
            day: "2-digit",
            month: "2-digit"
        });
        headRow.appendChild(th);
    });

    // Ajout des lignes horaires
    for (let h = 0; h < 24; h += 2) {
        let tr = document.createElement("tr");

        let tdHour = document.createElement("td");
        tdHour.className = "time-col";
        tdHour.textContent = pad(h) + "h - " + pad(h + 2) + "h";
        tr.appendChild(tdHour);

        for (let d = 0; d < 7; d++) {
            let td = document.createElement("td");
            td.dataset.day = d;
            td.dataset.hour = h;
            tr.appendChild(td);
        }

        body.appendChild(tr);
    }

    // Ajout des créneaux
    slots.forEach(s => {
        let date = new Date(s.date);
        let dayIndex = days.findIndex(d => d.toDateString() === date.toDateString());
        if (dayIndex === -1) return;

        let block = Math.floor(s.startHour / 2) * 2;
        let cell = document.querySelector(
            "td[data-day='" + dayIndex + "'][data-hour='" + block + "']"
        );

        if (!cell) return;

        let div = document.createElement("div");
        div.className = "slot";
        div.innerHTML = `
            <p><b>${s.artist}</b>
            <br>
            ${pad(s.startHour)}h - ${pad(s.startHour + 2)}h </p>
            <button type="button" class="btn btn-light btn-sm text-center boutonCreneau" onclick="ouvreModif(${s.idC})" data-bs-toggle="modal" data-bs-target="#ModifModal">Modifier</button>
            <button type="button" class="btn btn-danger btn-sm text-center boutonCreneau" onclick="ouvreSupp(${s.idC})" data-bs-toggle="modal" data-bs-target="#SuppModal">Supprimer</button>
        `;
        cell.appendChild(div);
    });

});

