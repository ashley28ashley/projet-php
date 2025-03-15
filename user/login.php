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
        // ... (le reste du code PHP reste inchangé)
        
            } else {
                // Connexion réussie
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["id_utilisateur"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["user_role"] = $user["role"];
                
                // Vérifier s'il y a une redirection en attente
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    
                    if (isset($_SESSION['produit_a_ajouter'])) {
                        $id_produit = $_SESSION['produit_a_ajouter'];
                        unset($_SESSION['produit_a_ajouter']);
                        header("Location: ajouter_panier.php?id_produit=$id_produit");
                        exit();
                    }
                    
                    header("Location: $redirect");
                    exit();
                } else {
                    // Redirection en fonction du rôle
                    if ($user["role"] === 'admin') {
                        header("Location: ../admin/dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                }
            }
          }  
        
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .login-form {
            width: 400px;
            margin: 0 auto;
            padding: 30px 0;
        }
        .login-form h2 {
            color: #333;
            margin: 0 0 30px;
            display: inline-block;
            border-bottom: 2px solid #5e72e4;
            padding-bottom: 10px;
        }
        .login-form form {
            color: #999;
            border-radius: 10px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form input[type="email"],
        .login-form input[type="password"] {
            font-size: 16px;
            height: 45px;
            padding: 0 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            width: calc(100% - 20px);
            display: inline-block;
        }
        .login-form label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }
        .login-form label i {
            margin-right: 8px;
        }
        .login-form button[type="submit"] {
            font-size: 16px;
            background: #5e72e4;
            color: #fff;
            border-radius: 25px;
            padding: 12px 20px;
            border: none;
            width: 100%;
        }
        .login-form button[type="submit"]:hover {
            background: #4d5bf7;
        }
        .login-form .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .login-form .message {
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <form method="post">
            <h2>Connexion</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message"><?php echo $_SESSION['message']; ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </div>
        </form>
    </div>
</body>
</html>
