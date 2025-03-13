<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Récupérer toutes les commandes
$stmt = $conn->prepare("SELECT orders.id, users.username, orders.order_date, orders.status 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        ORDER BY orders.order_date DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Commandes 📦</h2>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td><?= $order['order_date'] ?></td>
            <td>
                <form method="post" action="update_order.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>En cours</option>
                        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Expédiée</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Livrée</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                    <button type="submit">Modifier</button>
                </form>
            </td>
            <td>
                <a href="delete_order.php?id=<?= $order['id'] ?>" onclick="return confirm('Supprimer cette commande ?')">🗑 Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
