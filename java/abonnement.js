// Mise à jour du type d'offre
document.querySelectorAll('input[name="abonnement_type"]').forEach(input => {
    input.addEventListener('change', function() {
        const label = document.querySelector(`label[for="${this.id}"]`);
        const planName = label.childNodes[0].textContent.trim();
        const priceInput = document.getElementById(`prix-${this.id}`); // prix-classique, prix-complet, etc.
        const price = priceInput ? priceInput.value : '—';

        document.getElementById('summary-plan').textContent = `${planName} — ${price}€/mois`;
    });
});
document.getElementById('nbsalle').addEventListener('change', function() {
    const val = this.value;
    document.getElementById('summary-rooms').textContent = val;
});
function calculaterPrix(basePrice, nbSalles) {
    if (nbSalles > 1) {
        return basePrice * nbSalles * 0.85;
    } 
    else if (nbSalles == 1) {
        return basePrice;
    } 
    else {
        return 0;
    }
}
const nbSalles = parseInt(document.getElementById("nbsalle").value);

const classiquePrice = calculaterPrix(361.76 , nbSalles);
const completPrice = calculaterPrix(723.51 , nbSalles);
const expertPrice = calculaterPrix(3255.81, nbSalles);

function updatePrix() {
    const nbSalles = parseInt(document.getElementById("nbsalle").value);

    document.getElementById("prix-classique").value =
        calculaterPrix(361.76, nbSalles).toFixed(2)  ;

    document.getElementById("prix-complet").value =
        calculaterPrix(723.51, nbSalles).toFixed(2) ;

    document.getElementById("prix-expert").value =
        calculaterPrix(3255.81, nbSalles).toFixed(2) ;
}

document.getElementById("nbsalle")
    .addEventListener("change", updatePrix);

updatePrix(); // Initial call to set prices on page load