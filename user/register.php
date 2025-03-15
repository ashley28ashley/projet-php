<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .signup-form {
            width: 400px;
            margin: 0 auto;
            padding: 30px 0;
        }
        .signup-form h2 {
            color: #333;
            margin: 0 0 30px;
            display: inline-block;
            border-bottom: 2px solid #5e72e4;
            padding-bottom: 10px;
        }
        .signup-form form {
            color: #999;
            border-radius: 10px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .signup-form .form-group {
            margin-bottom: 20px;
        }
        .signup-form input[type="text"],
        .signup-form input[type="email"],
        .signup-form input[type="password"] {
            font-size: 16px;
            height: 45px;
            padding: 0 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            width: calc(100% - 20px);
            display: inline-block;
        }
        .signup-form label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }
        .signup-form label i {
            margin-right: 8px;
        }
        .signup-form button[type="submit"] {
            font-size: 16px;
            background: #5e72e4;
            color: #fff;
            border-radius: 25px;
            padding: 12px 20px;
            border: none;
            width: 100%;
        }
        .signup-form button[type="submit"]:hover {
            background: #4d5bf7;
        }
        .signup-form .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="signup-form">
        <form method="post">
            <h2>Inscription</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="form-group">
                <label for="username"><i class="fa fa-user"></i> Nom d'utilisateur</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Mot de passe</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Mot de passe" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fa fa-lock"></i> Confirmer mot de passe</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirmer mot de passe" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            </div>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
