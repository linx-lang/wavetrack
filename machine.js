document.getElementById("idType").addEventListener("change", function () {
 
    // On récupère l'option actuellement sélectionnée
    const optionChoisie = document.querySelector("#idType option:checked");
 
    // On lit sa valeur par défaut (stockée dans data-default côté PHP)
    // Si elle n'existe pas, on met une chaîne vide ""
    const valeurParDefaut = optionChoisie.dataset.default || "";
 
    // On remplit automatiquement le champ "valeurMax" avec cette valeur
    document.getElementById("valeurMax").value = valeurParDefaut;
 
});
 