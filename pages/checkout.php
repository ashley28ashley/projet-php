<?php 
require_once '../config/db.php';
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Page de checkout pour votre boutique en ligne.">

	<!-- Title -->
	<title>Check Out</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/png" href="../assets/img/favicon.png">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">

	<!-- FontAwesome -->
	<link rel="stylesheet" href="../assets/css/all.min.css">

	<!-- Bootstrap -->
	<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">

	<!-- Owl Carousel -->
	<link rel="stylesheet" href="../assets/css/owl.carousel.css">

	<!-- Magnific Popup -->
	<link rel="stylesheet" href="../assets/css/magnific-popup.css">

	<!-- Animate CSS -->
	<link rel="stylesheet" href="../assets/css/animate.css">

	<!-- Mean Menu CSS -->
	<link rel="stylesheet" href="../assets/css/meanmenu.min.css">

	<!-- Main Style -->
	<link rel="stylesheet" href="../assets/css/main.css">

	<!-- Responsive -->
	<link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<?php 
// Vérifier si le panier est vide
if (empty($_SESSION['panier'])) {
    echo "<p>Votre panier est vide. <a href='catalogue.php'>Retourner à la boutique</a></p>";
    include '../includes/footer.php';
    exit;
}

// Récupérer les produits du panier
$produits = [];
$total = 0;
$ids = implode(",", array_keys($_SESSION['panier']));
$stmt = $conn->query("SELECT * FROM items WHERE id IN ($ids)");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($produits as &$produit) {
    $id = $produit['id'];
    $produit['quantite'] = $_SESSION['panier'][$id];
    $produit['sous_total'] = $produit['quantite'] * $produit['price'];
    $total += $produit['sous_total'];
}

// Traitement du formulaire de commande
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? 0; // Si l'utilisateur est connecté
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $code_postal = trim($_POST['code_postal']);
    
    // Insérer la commande
    $stmt = $conn->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'pending')");
    $stmt->execute([$user_id]);
    $order_id = $conn->lastInsertId();

    // Insérer les produits de la commande
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($produits as $produit) {
        $stmt->execute([$order_id, $produit['id'], $produit['quantite'], $produit['price']]);
    }
// Mettre à jour le nombre de ventes pour chaque produit
    $stmt = $conn->prepare("UPDATE items SET sales = sales + ? WHERE id = ?");
    foreach ($produits as $produit) {
        $stmt->execute([$produit['quantite'], $produit['id']]);
    }

    // Insérer la facture
    $stmt = $conn->prepare("INSERT INTO invoice (order_id, user_id, transaction_date, amount, billing_address, city, postal_code, payment_status) 
                            VALUES (?, ?, NOW(), ?, ?, ?, ?, 'pending')");
    $stmt->execute([$order_id, $user_id, $total, $adresse, $ville, $code_postal]);

    // Vider le panier après la commande
    unset($_SESSION['panier']);

    // Rediriger vers une page de confirmation
    header("Location: confirmation.php?order_id=" . $order_id);
    exit;
}
?>

<div class="container mt-5">
    <h2 class="text-center">Validation de la commande</h2>
    <h3 class="mt-4">Récapitulatif</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit) : ?>
                <tr>
                    <td><?= htmlspecialchars($produit['name']) ?></td>
                    <td><?= $produit['quantite'] ?></td>
                    <td><?= number_format($produit['sous_total'], 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Total à payer : <?= number_format($total, 2) ?> €</strong></p>

    <h3 class="mt-4">Informations de livraison</h3>
    <form method="post">
        <div class="mb-3">
            <input type="text" class="form-control" name="adresse" placeholder="Adresse de livraison" required>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" name="ville" placeholder="Ville" required>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" name="code_postal" placeholder="Code Postal" required>
        </div>
        <button type="submit" class="btn btn-primary">Confirmer la commande</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

<!-- JQuery -->
<script src="../assets/js/jquery-1.11.3.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<!-- Owl Carousel -->
<script src="../assets/js/owl.carousel.min.js"></script>
<!-- Magnific Popup -->
<script src="../assets/js/jquery.magnific-popup.min.js"></script>
<!-- Mean Menu -->
<script src="../assets/js/jquery.meanmenu.min.js"></script>
<!-- Main Script -->
<script src="../assets/js/main.js"></script>

</body>
</html>
