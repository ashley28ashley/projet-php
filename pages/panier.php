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

<div class="container">
    <h2>Votre Panier</h2>
    <div id="panier-container">
        <!-- Le contenu du panier sera chargé ici dynamiquement par JavaScript -->
    </div>
    <a href="../index.php" class="btn btn-primary">Continuer vos achats</a>
    <a href="checkout.php" class="btn btn-success">Passer à la commande</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/panier.js"></script>

<?php include '../includes/footer.php'; ?>
