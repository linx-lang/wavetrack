const zones = [
    "Scene", "Backstage", "Regie", "Bar", "Vestiaires",
    "Toilettes", "Entree / Hall", "Zone Technique", 
];

const products = [
    { id: 1, name: "Bouton", image : "img/bouton.webp", price: 67.44, desc: "Ce produit est conçu pour détecter l'ouverture d'une porte et envoyer une alerte si une intrusion est détectée." },
    { id: 2, name: "Multisensor", image : "img/multisensor.jpg", price: 94.59, desc: "Ce produit est conçu pour détecter la température, ---, --- dans votre salle de concert." },
    { id: 3, name: "Détecteur Ouverture Porte", image : "img/porte.png", price: 28.00, desc: "Ce produit sert à activer votre climatiseur ou bien compter le nombre de fois où vos vestiaires ont servis." },
    { id: 4, name: "Prise Murale Connectée", image : "img/wallplug.jpg", price: 12.86, desc: "Ce produit est utilisé pour analyser le nombre de watt utilisés par chacune de vos machines." }
];

const zonesList = document.getElementById("zones-list");
const productsContainer = document.getElementById("products-container");
const summaryContainer = document.getElementById("summary-container");

/* --- PANIER GLOBAL (comme la 2e page) --- */
let cart = {};  
zones.forEach(z => cart[z] = {}); // chaque zone a son panier

/* --- AFFICHAGE DES ZONES --- */
zones.forEach((zone, index) => {
    zonesList.innerHTML += `
        <div class="form-check mb-2">
            <input class="form-check-input zone-checkbox" 
                    type="checkbox" 
                    name="zone[]" 
                    value="${zone}" 
                    id="zone${index}">
            <label class="form-check-label" for="zone${index}">
                ${zone}
            </label>
        </div>`;
});

/* --- GESTION DES ZONES --- */
document.querySelectorAll(".zone-checkbox").forEach(checkbox => {
    checkbox.addEventListener("change", function() {

        // Décocher les autres zones
        document.querySelectorAll(".zone-checkbox").forEach(cb => {
            if (cb !== this) cb.checked = false;
        });

        productsContainer.innerHTML = "";

        if (this.checked) {
            const zoneName = this.value;

            let html = `<div class="row">`;

            products.forEach(prod => {
                const qty = cart[zoneName][prod.id] || 0;

                html += `
                    <div class="col-md-5 mb-4">
                        <div class="card shadow-sm">
                            <img src="${prod.image}" class="card-img-top" alt="${prod.name}">
                            <div class="card-body">
                                <h3 class="card-title">${prod.name}</h3>
                                <p class="card-text text-muted">${prod.desc}</p>
                                <p><strong>${prod.price} €</strong></p>

                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="changeQty('${zoneName}', ${prod.id}, -1)">-</button>

                                    <span id="qty-${zoneName}-${prod.id}" 
                                        class="mx-3 fs-5">${qty}</span>

                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="changeQty('${zoneName}', ${prod.id}, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            productsContainer.innerHTML = html;
        }

        updateSummary();
    });
});

function changeQty(zone, productId, delta) {
    if (!cart[zone][productId]) cart[zone][productId] = 0;

    cart[zone][productId] += delta;
    if (cart[zone][productId] < 0) cart[zone][productId] = 0;

    const qtySpan = document.getElementById(`qty-${zone}-${productId}`);
    if (qtySpan) qtySpan.innerText = cart[zone][productId];

    updateSummary();
}

function updateSummary() {
let html = "";
let hasContent = false;
let prixTotal = 0;

zones.forEach(zone => {
    const items = cart[zone];

    const productLines = Object.keys(items)
        .filter(pid => items[pid] > 0)
        .map(pid => {
            const prod = products.find(p => p.id == pid);
            const qty = items[pid];
            const subtotal = prod.price * qty;

            prixTotal += subtotal;

            return `
                <div class="d-flex justify-content-between">
                    <label>${prod.name}</label>
                    <input name="selections[${zone}][${prod.id}]" value="${qty}" readonly class="form-control-plaintext w-auto text-end p-0 fw-bold">
                </div>
            `;
        });

    if (productLines.length > 0) {
        hasContent = true;

        html += `
            <h6 class="mt-3">— ${zone} —</h6>
            ${productLines.join("")}
            <hr>
        `;
    }
});

if (!hasContent) {
        summaryContainer.innerHTML = "<em>Aucune sélection pour le moment.</em>";
        return;
    }

    html += `
        <div class="d-flex justify-content-between mt-3">
            <label>Prix total :</label>
            <input type="text" name="total" value="${prixTotal} €" readonly class="form-control-plaintext w-auto text-end p-0 fw-bold">
        </div>
    `;

    summaryContainer.innerHTML = html;
}