function ajouterHoraire(jour, button) {
    const container = document.getElementById('h' + jour);
    console.log('fonction appelée avec:', 'h' + jour);
    const newPair = document.createElement('div');
    console.log('container trouvé:', container); 
    newPair.classList.add('horaireP');
    console.log('classes sur newPair:', newPair.classList);
    
    newPair.innerHTML = `
        <input type="text" placeholder="Horaire deb" name="horaireD[${jour}][]">
        <input type="text" placeholder="Horaire fin" name="horaireF[${jour}][]">
    `;
    container.appendChild(newPair);
    button.disabled = true;
}
document.getElementById('nbsalle').addEventListener('change', function() {
    document.getElementById('summary-rooms').textContent = this.options[this.selectedIndex].text;
});

// Mise à jour du type d'offre
document.querySelectorAll('input[name="abonnement_type"]').forEach(input => {
    input.addEventListener('change', function() {
        // On récupère le texte du label associé
        const label = document.querySelector(`label[for="${this.id}"]`).textContent;
        console.log("label est:", label);
        document.getElementById('summary-plan').textContent = label;
    });
});