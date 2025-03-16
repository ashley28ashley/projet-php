<?php
require_once '../config/db.php';

// Récupérer le terme de recherche
$recherche = isset($_GET['q']) ? $_GET['q'] : '';

// Préparer la requête SQL
$sql = "SELECT * FROM items WHERE nom LIKE :recherche";
$stmt = $conn->prepare($sql);
$stmt->execute(['recherche' => "%$recherche%"]);

// Récupérer les résultats
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inclure le header
include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Résultats de recherche pour "<?php echo htmlspecialchars($recherche); ?>"</h2>
    
    <?php if (count($resultats) > 0): ?>
        <div class="row">
            <?php foreach ($resultats as $produit): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                         <img src="../<?php echo htmlspecialchars($produit['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($produit['nom']); ?></h5>
                            <p class="card-text"><?php echo number_format($produit['prix'], 2); ?> €</p>
                            <a href="produit.php?id=<?php echo $produit['id']; ?>" class="btn btn-primary">Voir détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun produit trouvé.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>