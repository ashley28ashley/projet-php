<?php
require_once '../config/db.php';
include '../includes/header.php';

// Récupérer tous les produits
$stmt = $conn->query("SELECT * FROM items ORDER BY date_publication DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Catalogue des Produits</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            background-color: #D7D1FF;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
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
    <h2 class="text-center my-4">Catalogue des Produits</h2>
    <div class="row justify-content-center">
        <?php foreach ($products as $product) : ?>
            <div class="col-md-3">
                <div class="card shadow h-100">
                    <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nom']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['nom']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <p class="card-text fw-bold"><?= number_format($product['prix'], 2) ?> €</p>
                        <a href="produit.php?id=<?= $product['id'] ?>" class="btn btn-primary mb-2">Voir plus</a>
                        <button class="btn btn-success add-to-cart" 
                            data-id="<?= $product['id'] ?>" 
                            data-name="<?= htmlspecialchars($product['nom']) ?>" 
                            data-price="<?= $product['prix'] ?>" 
                            data-image="<?= htmlspecialchars($product['image']) ?>">
                            🛒 Ajouter au panier
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../assets/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
