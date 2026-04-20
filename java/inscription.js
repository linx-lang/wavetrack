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
async function validerInscription() {
    console.log("clicked");

    const formPro = document.getElementById("inscriptionForm");
    const formSalle = document.getElementById("salleForm");

    try {
        const rep1 = await fetch("php/inscriptionPro.php", {
            method: "POST",
            body: new FormData(formPro)
        });

        const rep2 = await fetch("php/inscriptionSalle.php", {
            method: "POST",
            body: new FormData(formSalle)
        });

        if (rep1.ok && rep2.ok) {
            window.location.href = "PaiementInscription.html";
        } else {
            alert("Erreur inscription");
        }

    } catch(error) {
        console.log(error);
    }
}