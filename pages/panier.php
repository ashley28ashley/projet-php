<?php
session_start();
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    $_SESSION['redirect_after_login'] = 'pages/panier.php';
    echo "<div class='container'>";
    echo "<h2>Accès au panier</h2>";
    echo "<p>Veuillez vous <a href='../user/login.php'>connecter</a> pour voir votre panier.</p>";
    echo "</div>";
    include '../includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .cart-container {
            margin: 30px auto;
            max-width: 800px;
        }
        .cart-item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cart-item img {
            max-width: 80px;
            border-radius: 5px;
        }
        .cart-item .item-details {
            flex-grow: 1;
            margin-left: 15px;
        }
        .cart-item .item-actions {
            text-align: right;
        }
        .btn-primary {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }
        .btn-primary:hover {
            background-color: #4d5bf7;
            border-color: #4d5bf7;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }
    </style>
</head>
<body>

<div class="container cart-container">
    <h2 class="text-center mb-4">Votre Panier</h2>
    <div id="panier-container">
        <!-- Le contenu du panier sera chargé ici dynamiquement par JavaScript -->
    </div>
    <div class="text-center mt-4">
        <a href="../index.php" class="btn btn-primary">Continuer vos achats</a>
        <a href="checkout.php" class="btn btn-success">Passer à la commande</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../assets/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
