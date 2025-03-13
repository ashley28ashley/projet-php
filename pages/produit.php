<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

// Récupérer l'ID du produit depuis l'URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si l'ID n'est pas valide, rediriger vers la page catalogue
if ($product_id <= 0) {
    header('Location: catalogue.php');
    exit;
}

// Récupérer les informations du produit
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$product_id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le produit n'existe pas, rediriger vers la page catalogue
if (!$produit) {
    header('Location: catalogue.php');
    exit;
}

// Incrémenter le compteur de vues (optionnel)
$stmt = $conn->prepare("UPDATE items SET views = views + 1 WHERE id = ?");
$stmt->execute([$product_id]);
?>

<main class="container my-4">
    <div class="row">
        <!-- Image du produit -->
        <div class="col-md-6">
            <div class="card">
                <img src="<?= getImagePath($produit['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($produit['name']) ?>">
            </div>
        </div>
        
        <!-- Détails du produit -->
        <div class="col-md-6">
            <h1><?= htmlspecialchars($produit['name']) ?></h1>
            
            <div class="my-3">
                <h2 class="text-primary"><?= number_format($produit['price'], 2) ?> €</h2>
            </div>
            
            <div class="my-3">
                <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
            </div>
            
            <div class="my-4">
                <form action="pages/panier.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $produit['id'] ?>">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="quantity">Quantité</label>
                        <select class="form-select" id="quantity" name="quantity">
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </div>
                </form>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Informations produit</h5>
                    <p class="card-text"><small class="text-muted">Publié le: <?= date('d/m/Y', strtotime($produit['publication_date'])) ?></small></p>
                    <p class="card-text"><small class="text-muted">Dernière mise à jour: <?= date('d/m/Y', strtotime($produit['updated_at'])) ?></small></p>
                    <?php if (isset($produit['sales']) && $produit['sales'] > 0) : ?>
                        <p class="card-text"><small class="text-success"><?= $produit['sales'] ?> vente(s)</small></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="catalogue.php" class="btn btn-outline-secondary">Retour au catalogue</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>