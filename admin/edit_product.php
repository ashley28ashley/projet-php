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
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $product['image'];

    // Gérer le téléchargement d'une nouvelle image
    if (!empty($_FILES['image']['name'])) {
        $image = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $image);
    }

    // Mise à jour du produit
    $stmt = $conn->prepare("UPDATE items SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $image, $product_id]);

    header("Location: products.php");
    exit;
}

?>

<div class="container">
    <h2>Modifier le Produit 🛠</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= $product['name'] ?>" required><br>
        <textarea name="description" required><?= $product['description'] ?></textarea><br>
        <input type="number" name="price" value="<?= $product['price'] ?>" required><br>
        <input type="file" name="image"><br>
        <img src="../<?= $product['image'] ?>" width="100"><br>
        <button type="submit">Mettre à Jour</button>
    </form>

    <a href="products.php">⬅ Retour</a>
</div>

<?php include '../includes/footer.php'; ?>
