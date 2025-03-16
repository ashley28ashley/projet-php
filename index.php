<?php
require_once 'config/db.php';
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mon E-Commerce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            background-color: #D7D1FF;
            margin-bottom: 20px;
        }
        .card-img-top {
            max-width: 100%;
            height: auto;
        }
        .btn-primary {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }
        .btn-primary:hover {
            background-color: #4d5bf7;
            border-color: #4d5bf7;
        }
    </style>
</head>
<body>

<main class="container">
    <!-- Carrousel avec ajout au panier -->
    <section id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <!-- ... (le code du carrousel reste inchangé) ... -->
    </section>

    <!-- Section de bienvenue -->
    <section class="hero text-center my-4">
        <h1>Bienvenue sur Mon E-Commerce</h1>
        <p>Découvrez nos produits de qualité aux meilleurs prix.</p>
        <a href="pages/catalogue.php" class="btn btn-primary">Voir les Produits</a>
    </section>

    <!-- Produits en vedette -->
    <section class="featured-products">
        <h2 class="text-center mb-4">Produits en Vedette</h2>
        <div class="row justify-content-center">
            <?php
            $stmt = $conn->query("SELECT * FROM items ");
            $count = 0;
            while ($produit = $stmt->fetch(PDO::FETCH_ASSOC)) :
                if ($count % 4 == 0 && $count != 0) {
                    echo '</div><div class="row justify-content-center">';
                }
            ?>
                <div class="col-md-3">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($produit['nom']) ?></h5>
                            <?php if (!empty($produit['image'])): ?>
                                <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top mb-3" alt="<?= htmlspecialchars($produit['nom']) ?>">
                            <?php endif; ?>
                            <p class="card-text fw-bold"><?= number_format($produit['prix'], 2) ?> €</p>
                            <a href="pages/produit.php?id=<?= $produit['id'] ?>" class="btn btn-primary mb-2">Voir plus</a>
                            <button class="btn btn-success add-to-cart" 
                                data-id="<?= $produit['id'] ?>" 
                                data-name="<?= htmlspecialchars($produit['nom']) ?>" 
                                data-price="<?= $produit['prix'] ?>" 
                                data-image="<?= htmlspecialchars($produit['image']) ?>">
                                🛒 Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            <?php 
                $count++;
                endwhile; 
            ?>
        </div>
    </section>





</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/projet-php/assets/js/panier.js"></script>

<?php include 'includes/footer.php'; ?>

</body>
</html>
