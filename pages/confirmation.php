<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si une facture a été générée
if (!isset($_GET['id_invoice'])) {
    echo "<p>Aucune facture trouvée. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

$id_invoice = intval($_GET['id_invoice']);

// Récupérer les détails de la facture
$stmt = $conn->prepare("
    SELECT i.id, i.date_transaction, i.montant, i.payment_status 
    FROM invoice i
    WHERE i.id = ?
");
$stmt->execute([$id_invoice]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    echo "<p>Facture introuvable. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

// Récupérer les produits de la commande
$stmt = $conn->prepare("
    SELECT o.id_item, o.quantite, i.nom, i.prix
    FROM orders o
    JOIN items i ON o.id_item = i.id
    WHERE o.id_user = ? AND o.date_commande = ?
");
$stmt->execute([$_SESSION['id_utilisateur'], $invoice['date_transaction']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Commande Confirmée ✅</h2>
    <p>Merci pour votre achat ! Votre commande a été enregistrée avec succès.</p>
    
    <h3>Détails de la facture</h3>
    <p><strong>Numéro de facture :</strong> #<?= $invoice['id'] ?></p>
    <p><strong>Date :</strong> <?= $invoice['date_transaction'] ?></p>
    <p><strong>Total à payer :</strong> <?= number_format($invoice['montant'], 2) ?> €</p>
    <p><strong>Statut du paiement :</strong> <?= ucfirst($invoice['payment_status']) ?></p>

    <h3>Produits commandés</h3>
    <table class="table">
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Total</th>
        </tr>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?= htmlspecialchars($item['nom']) ?></td>
                <td><?= $item['quantite'] ?></td>
                <td><?= number_format($item['prix'], 2) ?> €</td>
                <td><?= number_format($item['quantite'] * $item['prix'], 2) ?> €</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="formulaire_paiement.php" class="btn btn-primary">Passer au paiement</a>
    <a href="catalogue.php" class="btn btn-secondary">Retourner à la boutique</a>
</div>

<?php include '../includes/footer.php'; ?>
