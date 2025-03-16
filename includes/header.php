<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_path = '/projet-php/'; 

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: rgb(67, 11, 196);
            color: white;
            padding: 10px 20px;
        }

        .banniere nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            display: inline-block;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        .efruitshop {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .cart,
        .user {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }

        .cart:hover,
        .user:hover {
            color: #ddd;
        }
    </style>
</head>

<body>

    <header>
        <div class="banniere">
            <nav>
                <a href="<?php echo $base_path; ?>index.php" class="navbar-brand text-white fw-bold"> <span class="efruitshop">E-FruitShop</span></a>
                <ul>
                    <li><a href="<?php echo $base_path; ?>index.php">Accueil</a></li>
                    <li><a href="<?php echo $base_path; ?>pages/catalogue.php">Produits</a></li>
                    <li><a href="<?php echo $base_path; ?>pages/panier.php">Panier</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_path; ?>user/compte.php">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                        <li><a href="<?php echo $base_path; ?>user/logout.php">🚪 Déconnexion</a></li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="<?php echo $base_path; ?>admin/dashboard.php">⚙ Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>user/login.php">🔑 Se connecter</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base_path; ?>pages/about.php">Qui sommes nous ?</a></li>
                    <li><a href="<?php echo $base_path; ?>index.php#faq">❓ FAQ</a></li>

                </ul>

                <!-- Icônes utilisateur et panier -->
                <div class="icons">
                    <a href="<?php echo $base_path; ?>pages/panier.php" class="cart"><i class="bi bi-cart"></i></a>
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <a href="<?php echo $base_path; ?>user/compte.php" class="user"><i class="bi bi-person"></i></a>
                    <?php else : ?>
                        <a href="<?php echo $base_path; ?>user/login.php" class="user"><i class="bi bi-person"></i></a>
                    <?php endif; ?>
                </div>
            </nav>

            <!-- Boîte de recherche -->
            <form action="pages/search.php" method="GET">
                <div class="container mt-5 custom-search-container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="input-group custom-search-input">
                                <input type="text" name="q" class="form-control"  placeholder="Rechercher un produit..." aria-label="Rechercher"  aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success custom-search-button" type="submit" id="button-addon2">Rechercher</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </header>

    <main>