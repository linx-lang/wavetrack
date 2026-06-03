document.getElementById("idType").addEventListener("change", function () {
 
    // On récupère l'option actuellement sélectionnée
    const optionChoisie = document.querySelector("#idType option:checked");
 
    // On lit sa valeur par défaut (stockée dans data-default côté PHP)
    // Si elle n'existe pas, on met une chaîne vide ""
    const valeurParDefaut = optionChoisie.dataset.default || "";
 
    // On remplit automatiquement le champ "valeurMax" avec cette valeur
    document.getElementById("valeurMax").value = valeurParDefaut;
 
});
 
function changerZone() {
    // On vide l'affichage immédiatement (optionnel, juste visuel)
    const bloc = document.getElementById("listeMachines");
    if (bloc) {
        bloc.innerHTML = "<em>Chargement...</em>";
    }

    // On soumet le formulaire de sélection de zone
    document.getElementById("formZone").submit();
}

// Remplit le formulaire de modification et l'affiche
function remplirFormModif(idM, nomComplet, temp, watt) {
 
    // On affiche le formulaire
    document.getElementById('formModifContainer').style.display = 'block';
 
    // On remplit l'idM caché
    document.getElementById('modifIdM').value = idM;
 
    // Le nomComplet est de la forme "Type - Nom", on sépare les deux
    var parties = nomComplet.split(' - ');
    var nomType = parties[0];           // ex : "Climatisation"
    var nomMachine = parties.slice(1).join(' - '); // ex : "Clim Salle 1"
 
    // On sélectionne le bon type dans le select
    var select = document.getElementById('modifIdType');
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].text === nomType) {
            select.selectedIndex = i;
            break;
        }
    }
 
    // On remplit les autres champs
    document.getElementById('modifNom').value  = nomMachine;
    document.getElementById('modifTemp').value = temp;
    document.getElementById('modifWatt').value = watt;
 
    // On fait défiler jusqu'au formulaire
    document.getElementById('formModifContainer').scrollIntoView();
}