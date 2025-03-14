<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Vérifier si un ID de produit est fourni
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = $_GET['id'];

// Récupérer les infos du produit
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}

// Mettre à jour le produit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $image = $product['image'];

    // Gérer le téléchargement d'une nouvelle image
    if (!empty($_FILES['image']['name'])) {
        $image = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $image);
    }

    // Mise à jour du produit
    $stmt = $conn->prepare("UPDATE items SET nom = ?, description = ?, prix = ?, stock = ?, image = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $prix, $stock, $image, $product_id]);

    header("Location: products.php");
    exit;
}

?>

<div class="container">
    <h2>Modifier le Produit 🛠</h2>

    <form method="POST" enctype="multipart/form-data">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($product['nom']) ?>" required><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>

        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" value="<?= htmlspecialchars($product['prix']) ?>" step="0.01" required><br>

        <label for="stock">Stock :</label>
        <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required><br>

        <label for="image">Image :</label>
        <input type="file" id="image" name="image"><br>
        <img src="../<?= htmlspecialchars($product['image']) ?>" width="100" alt="Image actuelle"><br>

        <button type="submit">Mettre à Jour</button>
    </form>

    <a href="products.php">⬅ Retour</a>
</div>

<?php include '../includes/footer.php'; ?>
