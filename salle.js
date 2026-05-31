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
   CODE QUI S'EXÉCUTE AU CHARGEMENT DE LA PAGE ( VOIR AVEC LINA )
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