<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Récupérer la liste des produits
$stmt = $conn->query("SELECT * FROM items");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // Gestion de l'image
    $image = '';
    if ($_FILES['image']['name']) {
        $image = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../" . $image);
    }

    $stmt = $conn->prepare("INSERT INTO items (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image]);

    header("Location: products.php");
    exit;
}

// Supprimer un produit
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: products.php");
    exit;
}

?>

<div class="container">
    <h2>Gestion des Produits 🛒</h2>

    <!-- Formulaire d'ajout de produit -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nom du produit" required><br>
        <textarea name="description" placeholder="Description" required></textarea><br>
        <input type="number" name="price" placeholder="Prix" required><br>
        <input type="file" name="image"><br>
        <button type="submit" name="add_product">Ajouter Produit</button>
    </form>

    <h3>Liste des Produits</h3>
    <table border="1">
        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><img src="../<?= $product['image'] ?>" width="50"></td>
            <td><?= $product['name'] ?></td>
            <td><?= $product['description'] ?></td>
            <td><?= $product['price'] ?> €</td>
            <td>
                <a href="edit_product.php?id=<?= $product['id'] ?>">✏ Modifier</a>
                <a href="products.php?delete=<?= $product['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">❌ Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
