document.addEventListener("DOMContentLoaded", function () {
    function ajouterAuPanier(id, name, price, image) {
        console.log("Product ID being sent:", id);
        $.ajax({
            url: '/projet-php/pages/ajouter_panier.php',
            type: 'POST',
            data: { id_item: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(`${name} ajouté au panier !`);
                    console.log("Contenu du panier:", response.data);
                    setTimeout(() => afficherPanier(), 300);
                } else {
                    alert('Erreur: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
                alert('Erreur lors de l\'ajout au panier.');
            }
        });
    }

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function () {
            let id = this.dataset.id;
            let name = this.dataset.name;
            let price = parseFloat(this.dataset.price);
            let image = this.dataset.image;

            ajouterAuPanier(id, name, price, image);
        });
    });

    function afficherPanier() {
        $.ajax({
            url: '/projet-php/pages/get_cart.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let panierContainer = document.getElementById("panier-container");
                if (!panierContainer) return;

                panierContainer.innerHTML = "";
                let total = 0;

                if (response.status === 'success' && Array.isArray(response.data)) {
                    const panier = response.data;

                    if (panier.length === 0) {
                        panierContainer.innerHTML = "<p>Votre panier est vide.</p>";
                        return;
                    }

                    panier.forEach((produit) => {
                        total += produit.prix * produit.quantité;
                        panierContainer.innerHTML += `
                            <div class="panier-item d-flex justify-content-between align-items-center border p-2 mb-2">
                                <img src="../assets/images/${produit.image}" alt="${produit.nom}" width="50">
                                <span>${produit.nom}</span>
                                <span>${produit.prix} €</span>
                                <div>
                                    <button class="btn btn-sm btn-danger retirer" data-id="${produit.id}">➖</button>
                                    <span>${produit.quantité}</span>
                                    <button class="btn btn-sm btn-success ajouter" data-id="${produit.id}">➕</button>
                                </div>
                            </div>
                        `;
                    });

                    panierContainer.innerHTML += `<h4>Total: ${total.toFixed(2)} €</h4>`;
                } else {
                    panierContainer.innerHTML = "<p>Erreur lors du chargement du panier.</p>";
                    console.error('Erreur de réponse:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
                alert('Erreur lors de la récupération du panier.');
            }
        });
    }

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("ajouter") || event.target.classList.contains("retirer")) {
            let id = event.target.dataset.id;
            let action = event.target.classList.contains("ajouter") ? 'add' : 'remove';

            $.ajax({
                url: '/projet-php/pages/update_cart.php',
                type: 'POST',
                data: { id_item: id, action: action },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        setTimeout(() => afficherPanier(), 300);
                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' - ' + error);
                    alert('Erreur lors de la mise à jour du panier.');
                }
            });
        }
    });

    if (window.location.pathname.includes("pages/panier.php")) {
        afficherPanier();
    }
});
