<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Récupérer le nombre de produits
$stmt = $conn->query("SELECT COUNT(*) AS total_products FROM items");
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Récupérer le nombre de commandes
$stmt = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

// Récupérer le nombre d'utilisateurs
$stmt = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

?>

<div class="container">
    <h2>Tableau de Bord Admin 📊</h2>

    <div class="stats">
        <div class="stat">
            <h3>📦 Produits</h3>
            <p><?= $total_products ?> Produits enregistrés</p>
            <a href="products.php" class="btn">Gérer les Produits</a>
        </div>
        
        <div class="stat">
            <h3>🛒 Commandes</h3>
            <p><?= $total_orders ?> Commandes passées</p>
            <a href="orders.php" class="btn">Gérer les Commandes</a>
        </div>

        <div class="stat">
            <h3>👥 Utilisateurs</h3>
            <p><?= $total_users ?> Utilisateurs inscrits</p>
            <a href="users.php" class="btn">Gérer les Utilisateurs</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
