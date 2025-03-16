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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

<!-- Section FAQ -->
<!-- Section FAQ -->
<!-- Section FAQ -->
<section class="faq bg-white py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-4">Questions fréquentes</h2>
        <div class="accordion" id="faqAccordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fa fa-chevron-down"></i> Comment passer une commande ?
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#faqAccordion">
                    <div class="card-body">
                        Pour passer une commande, parcourez notre catalogue, ajoutez les produits souhaités à votre panier, puis suivez les étapes de validation de commande.
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                           <i class="fa fa-chevron-down"></i> Quels sont les délais de livraison ?
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                    <div class="card-body">
                        Les délais de livraison varient généralement entre 3 et 5 jours ouvrés, selon votre localisation et le mode de livraison choisi.
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                           <i class="fa fa-chevron-down"></i> Comment retourner un produit ?
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                    <div class="card-body">
                        Pour retourner un produit, contactez notre service client dans les 14 jours suivant la réception. Nous vous fournirons les instructions pour le retour.
                    </div>
                </div>
            </div>
             <div class="card">
                <div class="card-header" id="headingFour">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                           <i class="fa fa-chevron-down"></i> Comment puis-je savoir si ma commande a bien été validée ?
                        </button>
                    </h2>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                    <div class="card-body">
                       Un e-mail de confirmation vous sera envoyé pour toute commande validée.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>






</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<script src="/projet-php/assets/js/panier.js"></script>

<?php include 'includes/footer.php'; ?>

</body>
</html>
