document.addEventListener("DOMContentLoaded", function () {
    let buttons = document.querySelectorAll(".add-to-cart");

    buttons.forEach(button => {
        button.addEventListener("click", function () {
            let productId = this.dataset.id;
            fetch("actions/ajouter_panier.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + productId
            })
            .then(response => response.text())
            .then(data => alert(data))
            .catch(error => console.error("Erreur:", error));
        });
    });
});
