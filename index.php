<?php
require_once 'config/db.php';
include 'includes/header.php';
?>

<main class="container">
    <!-- Carrousel avec ajout au panier -->
    <section id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/promo1.jpg" class="d-block w-100" alt="Promo 1">
                <div class="carousel-caption">
                    <button class="btn btn-warning add-to-cart" data-id="1" data-name="Fraise" data-price="10" data-image="promo1.jpg">🛒 Ajouter au panier</button>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/images/promo2.jpg" class="d-block w-100" alt="Promo 2">
                <div class="carousel-caption">
                    <button class="btn btn-warning add-to-cart" data-id="32" data-name="Promo 2" data-price="15" data-image="promo2.jpg">🛒 Ajouter au panier</button>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </section>

    <!-- Section de bienvenue -->
    <section class="hero text-center my-4">
        <h1>Bienvenue sur Mon E-Commerce</h1>
        <p>Découvrez nos produits de qualité aux meilleurs prix.</p>
        <a href="pages/catalogue.php" class="btn btn-primary">Voir les Produits</a>
    </section>

    <!-- Produits en vedette -->
    <section class="featured-products">
        <h2>Produits en Vedette</h2>
        <div class="row">
            <?php
            // Sélectionner uniquement les produits avec ID 1 et 32
            $stmt = $conn->query("SELECT * FROM items WHERE id IN (1, 32)");
            while ($produit = $stmt->fetch(PDO::FETCH_ASSOC)) :
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['name']) ?>">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($produit['name']) ?></h5>
                            <p class="card-text fw-bold"><?= number_format($produit['price'], 2) ?> €</p>
                            <a href="pages/produit.php?id=<?= $produit['id'] ?>" class="btn btn-primary">Détails</a>
                            <button class="btn btn-success mt-2 add-to-cart" 
                                data-id="<?= $produit['id'] ?>" 
                                data-name="<?= htmlspecialchars($produit['name']) ?>" 
                                data-price="<?= $produit['price'] ?>" 
                                data-image="<?= htmlspecialchars($produit['image']) ?>">
                                🛒 Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/panier.js"></script>

<?php include 'includes/footer.php'; ?>
