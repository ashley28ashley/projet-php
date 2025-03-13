<?php
require_once 'config/db.php';
include 'includes/header.php';
?>

<main class="container">
    <!-- Carrousel -->
    <section id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/promo1.jpg" class="d-block w-100" alt="Promo 1">
            </div>
            <div class="carousel-item">
                <img src="assets/images/promo2.jpg" class="d-block w-100" alt="Promo 2">
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

    <!-- Barre de recherche -->
    <section class="search-bar text-center my-4">
        <form action="pages/catalogue.php" method="GET">
            <input type="text" name="q" placeholder="Rechercher un produit..." class="form-control w-50 d-inline">
            <button type="submit" class="btn btn-outline-primary">Rechercher</button>
        </form>
    </section>

    <!-- Produits en vedette -->
    <section class="featured-products">
        <h2>Produits en Vedette</h2>
        <div class="row">
            <?php
            $featuredProducts = $conn->prepare("SELECT * FROM items ORDER BY publication_date DESC LIMIT 4");
            $featuredProducts->execute();

            if ($featuredProducts->rowCount() > 0) {
                while ($produit = $featuredProducts->fetch(PDO::FETCH_ASSOC)) :
                    $shortDescription = substr(htmlspecialchars($produit['description']), 0, 60) . '...';
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['name']) ?>">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($produit['name']) ?></h5>
                            <p class="card-text small text-muted"><?= $shortDescription ?></p>
                            <p class="card-text fw-bold"><?= number_format($produit['price'], 2) ?> €</p>
                            <a href="pages/produit.php?id=<?= $produit['id'] ?>" class="btn btn-primary">Détails</a>
                            <button class="btn btn-success mt-2 add-to-cart" data-id="<?= $produit['id'] ?>">🛒 Ajouter</button>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                echo '<div class="col-12 text-center"><p>Aucun produit disponible.</p></div>';
            }
            ?>
        </div>
    </section>

    <!-- Meilleures ventes -->
    <section class="best-sellers my-5">
        <h2>Meilleures Ventes</h2>
        <div class="row">
            <?php
            $bestSellers = $conn->prepare("SELECT * FROM items WHERE sales > 0 ORDER BY sales DESC LIMIT 4");
            $bestSellers->execute();
            
            if ($bestSellers->rowCount() > 0) {
                while ($produit = $bestSellers->fetch(PDO::FETCH_ASSOC)) :
                    $shortDescription = substr(htmlspecialchars($produit['description']), 0, 60) . '...';
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['name']) ?>">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($produit['name']) ?></h5>
                            <p class="card-text small text-muted"><?= $shortDescription ?></p>
                            <p class="card-text fw-bold"><?= number_format($produit['price'], 2) ?> €</p>
                            <a href="pages/produit.php?id=<?= $produit['id'] ?>" class="btn btn-success">Voir</a>
                            <button class="btn btn-warning mt-2 add-to-cart" data-id="<?= $produit['id'] ?>">🛒 Ajouter</button>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                echo '<div class="col-12 text-center"><p>Aucune vente enregistrée.</p></div>';
            }
            ?>
        </div>
    </section>
</main>

<script src="assets/js/panier.js"></script>

<?php include 'includes/footer.php'; ?>
