<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon E-Commerce</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo">
            <a href="index.php">Mon E-Commerce</a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="pages/catalogue.php">Produits</a></li>
                <li><a href="pages/panier.php">🛒 Panier</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="user/compte.php">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                    <li><a href="user/logout.php">🚪 Déconnexion</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">⚙ Admin</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="user/login.php">🔑 Se connecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>