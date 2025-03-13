<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Vérifier si un ID de commande est passé
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = $_GET['id'];

// Récupérer les informations de la commande
$stmt = $conn->prepare("SELECT orders.id, users.username, orders.order_date, orders.status 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        WHERE orders.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les produits associés à la commande
$stmt = $conn->prepare("SELECT items.name, order_items.quantity, order_items.price 
                        FROM order_items 
                        JOIN items ON order_items.item_id = items.id 
                        WHERE order_items.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Détails de la Commande #<?= $order['id'] ?></h2>
    <p><strong>Client :</strong> <?= htmlspecialchars($order['username']) ?></p>
    <p><strong>Date :</strong> <?= $order['order_date'] ?></p>
    <p><strong>Statut :</strong> <?= $order['status'] ?></p>

    <h3>Produits commandés 🛍</h3>
    <table border="1">
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Total</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach ($order_items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $item['price'] ?> €</td>
            <td><?= $item['quantity'] * $item['price'] ?> €</td>
        </tr>
        <?php $total += $item['quantity'] * $item['price']; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong><?= $total ?> €</strong></td>
        </tr>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
