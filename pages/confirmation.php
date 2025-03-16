<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si une commande a été passée
if (!isset($_GET['order_id'])) {
    echo "<p>Aucune commande trouvée. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

$order_id = intval($_GET['order_id']);

// Récupérer les détails de la commande
$stmt = $conn->prepare("
    SELECT o.id, o.order_date, o.status, i.amount, i.payment_status 
    FROM orders o
    JOIN invoice i ON o.id = i.order_id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>Commande introuvable. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

// Récupérer les produits de la commande
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name 
    FROM order_items oi
    JOIN items p ON oi.item_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Commande Confirmée ✅</h2>
    <p>Merci pour votre achat ! Votre commande a été enregistrée avec succès.</p>
    
    <h3>Détails de la commande</h3>
    <p><strong>Numéro de commande :</strong> #<?= $order['id'] ?></p>
    <p><strong>Date :</strong> <?= $order['order_date'] ?></p>
    <p><strong>Statut :</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Total payé :</strong> <?= number_format($order['amount'], 2) ?> €</p>
    <p><strong>Statut du paiement :</strong> <?= ucfirst($order['payment_status']) ?></p>

    <h3>Produits commandés</h3>
    <table>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Total</th>
        </tr>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price'], 2) ?> €</td>
                <td><?= number_format($item['quantity'] * $item['price'], 2) ?> €</td>
            </tr>
        <?php endforeach; ?>
    </table>
à
    <br>
    <a href="formulaire_paiement.php" class="btn btn-primary">Passer au paiement</a>
    <a href="catalogue.php" class="btn btn-secondary">Retourner à la boutique</a>
</div>

<?php include '../includes/footer.php'; ?>
