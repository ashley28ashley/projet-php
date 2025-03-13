<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($stmt->rowCount() == 0) {
        $error_message = "Aucun utilisateur trouvé avec cet email.";
    } elseif (!password_verify($password, $user["password"])) {
        $error_message = "Mot de passe incorrect.";
    } else {
        // Connexion réussie
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["id_utilisateur"] = $user["id"]; // Pour correspondre au système de panier
        $_SESSION["username"] = $user["username"];
        $_SESSION["user_role"] = $user["role"];
        
        // Vérifier s'il y a une redirection en attente
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // Nettoyer la session
            
            // Si un produit était en attente d'ajout au panier
            if (isset($_SESSION['produit_a_ajouter'])) {
                $id_produit = $_SESSION['produit_a_ajouter'];
                unset($_SESSION['produit_a_ajouter']);
                header("Location: ajouter_panier.php?id_produit=$id_produit");
                exit();
            }
            
            header("Location: $redirect");
            exit();
        } else {
            // Redirection par défaut vers la page d'accueil
            header("Location: ../index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .message {
            color: blue;
            margin-bottom: 15px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form method="post">
        <h2>Connexion</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; ?></div>
            <?php unset($_SESSION['message']); // Nettoyer le message après affichage ?>
        <?php endif; ?>
        
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>
        
        <div>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>