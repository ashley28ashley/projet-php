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
?>

<main class="container my-4">
    <div class="row">
        <!-- Image du produit -->
        <div class="col-md-6">
            <div class="card">
                <img src="../assets/images/<?= htmlspecialchars($produit['image']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($produit['nom']) ?>">
            </div>
        </div>
        
        <!-- Détails du produit -->
        <div class="col-md-6">
            <h1><?= htmlspecialchars($produit['nom']) ?></h1>
            
            <div class="my-3">
                <h2 class="text-primary"><?= number_format($produit['prix'], 2) ?> €</h2>
            </div>
            
            <div class="my-3">
                <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
            </div>
            
            <div class="my-4">
                <!-- Formulaire pour ajouter au panier -->
                <form action="../pages/ajouter_panier.php" method="POST">
                    <input type="hidden" name="id_item" value="<?= $produit['id'] ?>">
                    <input type="hidden" name="redirect_to_panier" value="true"> <!-- Indique qu'on veut rediriger -->
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="quantity">Quantité</label>
                        <select class="form-select" id="quantity" name="quantity">
                            <?php for ($i = 1; $i <= min(10, $produit['stock']); $i++) : ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-success add-to-cart">Ajouter au panier</button>
                    </div>
                </form>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Informations produit</h5>
                    <p class="card-text"><small class="text-muted">Publié le: <?= date('d/m/Y', strtotime($produit['date_publication'])) ?></small></p>
                    <p class="card-text"><small class="text-muted">Stock disponible: <?= $produit['stock'] ?></small></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="catalogue.php" class="btn btn-outline-secondary">Retour au catalogue</a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
