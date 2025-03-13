<?php
require_once '../config/db.php';
include '../includes/header.php';

// Initialiser le panier si ce n'est pas déjà fait
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter un produit au panier
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    if (!isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] = 1; // Ajouter avec une quantité de 1
    } else {
        $_SESSION['panier'][$id]++; // Incrémenter la quantité
    }
}

// Supprimer un produit du panier
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
}

// Modifier la quantité d’un produit
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update'])) {
    foreach ($_POST['quantities'] as $id => $quantity) {
        $_SESSION['panier'][$id] = max(1, intval($quantity)); // S'assurer que la quantité est au moins 1
    }
}

// Récupérer les détails des produits du panier
$produits = [];
$total = 0;
if (!empty($_SESSION['panier'])) {
    $ids = implode(",", array_keys($_SESSION['panier']));
    $stmt = $conn->query("SELECT * FROM items WHERE id IN ($ids)");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($produits as &$produit) {
        $id = $produit['id'];
        $produit['quantite'] = $_SESSION['panier'][$id];
        $produit['sous_total'] = $produit['quantite'] * $produit['price'];
        $total += $produit['sous_total'];
    }
}
?>

<div class="container">
    <h2>Votre Panier</h2>

    <?php if (empty($produits)) : ?>
        <p>Votre panier est vide.</p>
    <?php else : ?>
        <form method="post">
            <table>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($produits as $produit) : ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['name']) ?></td>
                        <td><?= number_format($produit['price'], 2) ?> €</td>
                        <td>
                            <input type="number" name="quantities[<?= $produit['id'] ?>]" value="<?= $produit['quantite'] ?>" min="1">
                        </td>
                        <td><?= number_format($produit['sous_total'], 2) ?> €</td>
                        <td>
                            <a href="panier.php?remove=<?= $produit['id'] ?>" class="btn-delete">❌</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Total : <?= number_format($total, 2) ?> €</strong></p>
            <button type="submit" name="update" class="btn">Mettre à jour le panier</button>
            <a href="checkout.php" class="btn">Passer à la commande</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
