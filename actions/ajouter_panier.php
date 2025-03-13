<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        $_SESSION['panier'][$id] = $_SESSION['panier'][$id] ?? 0;
        $_SESSION['panier'][$id]++;

        echo "Produit ajouté au panier !";
    } else {
        echo "Produit introuvable.";
    }
} else {
    echo "Requête invalide.";
}
?>
