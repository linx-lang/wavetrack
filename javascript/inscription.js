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