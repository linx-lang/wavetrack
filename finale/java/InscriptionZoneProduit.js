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
 
/* --- PANIER GLOBAL --- */
let cart = {};
zones.forEach(z => cart[z] = {});
 
/* --- ÉTAT PORTE PAR ZONE --- */
let zoneAPorte = {};
zones.forEach(z => zoneAPorte[z] = null);
 
/* --- Nettoyer le nom de zone pour les IDs HTML (enlever espaces et caractères spéciaux) --- */
function zoneId(zoneName) {
    return zoneName.replace(/[^a-zA-Z0-9]/g, "_");
}
 
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
        document.querySelectorAll(".zone-checkbox").forEach(cb => {
            if (cb !== this) cb.checked = false;
        });
 
        productsContainer.innerHTML = "";
 
        if (this.checked) {
            afficherProduits(this.value);
        }
 
        updateSummary();
    });
});
 
/* --- AFFICHAGE DES PRODUITS avec question porte --- */
function afficherProduits(zoneName) {
    const aPorte = zoneAPorte[zoneName];
    const zid = zoneId(zoneName);
 
    const ouiChecked = aPorte === true  ? "checked" : "";
    const nonChecked = aPorte === false ? "checked" : "";
 
    // Filtrer le Détecteur Porte (id=3) si la zone n'a pas de porte
    const produitsFiltres = products.filter(prod => {
        if (prod.id === 3 && aPorte !== true) return false;
        return true;
    });
 
    let html = `
        <div class="mb-4 p-3 border rounded bg-light">
            <p class="fw-semibold mb-2">🚪 La zone <strong>${zoneName}</strong> possède-t-elle une porte ?</p>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="porte_zone[${zoneName}]"
                           id="porte_oui_${zid}" 
                           value="oui" ${ouiChecked}
                           onchange="setPorte('${zoneName}', true)">
                    <label class="form-check-label" for="porte_oui_${zid}">Oui</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="porte_zone[${zoneName}]"
                           id="porte_non_${zid}" 
                           value="non" ${nonChecked}
                           onchange="setPorte('${zoneName}', false)">
                    <label class="form-check-label" for="porte_non_${zid}">Non</label>
                </div>
            </div>
        </div>
        <div class="row">
    `;
 
    if (aPorte === null) {
        html += `
            <div class="col-12 mb-3">
                <div class="alert alert-info py-2">
                    ℹ️ Indiquez si cette zone a une porte pour voir tous les produits disponibles.
                </div>
            </div>
        `;
    }
 
    produitsFiltres.forEach(prod => {
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
                            <span id="qty-${zid}-${prod.id}" class="mx-3 fs-5">${qty}</span>
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
 
/* --- CHANGER L'ÉTAT PORTE D'UNE ZONE --- */
function setPorte(zoneName, value) {
    zoneAPorte[zoneName] = value;
 
    // Vider le panier du détecteur porte si on passe à Non
    if (!value) {
        cart[zoneName][3] = 0;
    }
 
    afficherProduits(zoneName);
    updateSummary();
}
 
function changeQty(zone, productId, delta) {
    if (!cart[zone][productId]) cart[zone][productId] = 0;
 
    cart[zone][productId] += delta;
    if (cart[zone][productId] < 0) cart[zone][productId] = 0;
 
    const zid = zoneId(zone);
    const qtySpan = document.getElementById(`qty-${zid}-${productId}`);
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
            <input type="text" name="total" value="${prixTotal.toFixed(2)} €" readonly class="form-control-plaintext w-auto text-end p-0 fw-bold">
        </div>
    `;
 
    summaryContainer.innerHTML = html;
}