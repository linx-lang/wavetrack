function calculaterPrix(basePrice, nbSalles) {
    if (nbSalles > 1) {
        return basePrice * nbSalles * 0.85;
    } else if (nbSalles == 1) {
        return basePrice;
    } else {
        return 0;
    }
}

function updatePrix() {
    const nbSalles = parseInt(document.getElementById("nbsalle").value);

    document.getElementById("prix-classique").value = calculaterPrix(361.76, nbSalles).toFixed(2);
    document.getElementById("prix-complet").value = calculaterPrix(723.51, nbSalles).toFixed(2);
    document.getElementById("prix-expert").value = calculaterPrix(3255.81, nbSalles).toFixed(2);

    //mise à jour de la section récapitulative
    const selected = document.querySelector('input[name="abonnement_type"]:checked');
    if (selected) {
        const label = document.querySelector(`label[for="${selected.id}"]`);
        const planName = label.childNodes[0].textContent.trim();
        const price = document.getElementById(`prix-${selected.id}`).value;
        document.getElementById('summary-plan').textContent = `${planName} — ${price}€/mois`;
    }
}

document.querySelectorAll('input[name="abonnement_type"]').forEach(input => {
    input.addEventListener('change', function() {
        updatePrix();
        const label = document.querySelector(`label[for="${this.id}"]`);
        const planName = label.childNodes[0].textContent.trim();
        const price = document.getElementById(`prix-${this.id}`).value;
        document.getElementById('summary-plan').textContent = `${planName} — ${price}€/mois`;
    });
});

document.getElementById('nbsalle').addEventListener('change', function() {
    document.getElementById('summary-rooms').textContent = this.value;
    updatePrix();
});

updatePrix(); // première mise à jour au chargement de la page