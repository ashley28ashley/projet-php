<?php
require_once '../config/db.php';
include '../includes/header.php';

// Récupérer tous les produits
$stmt = $conn->query("SELECT * FROM items ORDER BY publication_date DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Catalogue des produits</h2>
    <div class="product-list">
        <?php foreach ($products as $product) : ?>
            <div class="product">
                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                <p><strong><?= number_format($product['price'], 2) ?> €</strong></p>
                <a href="produit.php?id=<?= $product['id'] ?>" class="btn">Voir Détails</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
