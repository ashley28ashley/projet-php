<?php
session_start();
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['id_utilisateur'])) {
    echo "<p>Veuillez vous connecter pour accéder à votre panier. <a href='login.php'>Se connecter</a></p>";
    include '../includes/footer.php';
    exit;
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupération des produits du panier
$stmt = $conn->prepare("
    SELECT p.id, p.nom, p.prix, p.image, SUM(pa.quantité) AS quantité
    FROM panier pa
    JOIN items p ON pa.id_item = p.id
    WHERE pa.id_utilisateur = ?
    GROUP BY p.id, p.nom, p.prix, p.image
");
$stmt->execute([$id_utilisateur]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérification du panier vide
if (empty($produits)) {
    echo "<p>Votre panier est vide. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

// Calcul du total (sans référence &)
$total = 0;
foreach ($produits as $index => $produit) {
    $produits[$index]['sous_total'] = $produit['quantité'] * $produit['prix'];
    $total += $produits[$index]['sous_total'];
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécuriser un minimum
    $adresse = htmlspecialchars($_POST['adresse']);
    $ville = htmlspecialchars($_POST['ville']);
    $code_postal = htmlspecialchars($_POST['code_postal']);

    try {
        $conn->beginTransaction();

        // Insertion dans la table invoice
        $stmt = $conn->prepare("
            INSERT INTO invoice (id_user, date_transaction, montant, adresse_facturation, ville, code_postal)
            VALUES (?, NOW(), ?, ?, ?, ?)
        ");
        $stmt->execute([$id_utilisateur, $total, $adresse, $ville, $code_postal]);
        $id_invoice = $conn->lastInsertId();

        // Insertion dans la table orders pour chaque produit
        foreach ($produits as $produit) {
            $stmt = $conn->prepare("
                INSERT INTO orders (id_user, id_item, quantite, date_commande, status)
                VALUES (?, ?, ?, NOW(), 'pending')
            ");
            $stmt->execute([$id_utilisateur, $produit['id'], $produit['quantité']]);

            // Supprimer du panier
            $stmt = $conn->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_item = ?");
            $stmt->execute([$id_utilisateur, $produit['id']]);
        }

        $conn->commit();
        $_SESSION['commande_confirmee'] = true;
        $_SESSION['montant_total'] = $total;
        header("Location: formulaire_paiement.php");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<p class='alert alert-danger'>Erreur lors de la validation de la commande : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Validation de la commande</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .checkout-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .checkout-container h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #5e72e4;
            padding-bottom: 10px;
        }
        .table {
            background-color: #fff;
        }
        .table th {
            background-color: #5e72e4;
            color: #fff;
        }
        .btn-primary {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }
        .btn-primary:hover {
            background-color: #4d5bf7;
            border-color: #4d5bf7;
        }
        .form-control {
            border-radius: 25px;
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <h2>Validation de la commande</h2>
    <h3 class="mt-4">Récapitulatif</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit) : ?>
                <tr>
                    <td><?= htmlspecialchars($produit['nom']) ?></td>
                    <td><?= $produit['quantité'] ?></td>
                    <td><?= number_format($produit['prix'], 2) ?> €</td>
                    <td><?= number_format($produit['sous_total'], 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="text-right"><strong>Total à payer : <?= number_format($total, 2) ?> €</strong></p>

    <h3 class="mt-5">Informations de livraison</h3>
    <form method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="adresse" placeholder="Adresse de livraison" required>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="ville" placeholder="Ville" required>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="code_postal" placeholder="Code Postal" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Confirmer la commande</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
