<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: login.php");
    exit();
}

// Inclure la configuration de la base de données
require_once '../config/db.php';

// Récupérer les informations de l'utilisateur
$id_user = $_SESSION["id_utilisateur"];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement de la mise à jour du profil
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);

    // Vérifier si l'email existe déjà
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check_email->execute([$email, $id_user]);
    if ($check_email->rowCount() > 0) {
        $message = '<div class="alert alert-danger">Cet email est déjà utilisé.</div>';
    } else {
        $update = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update->execute([$username, $email, $id_user]);

        if ($update->rowCount() > 0) {
            $_SESSION["username"] = $username;
            $message = '<div class="alert alert-success">Profil mis à jour.</div>';
            $user["username"] = $username;
            $user["email"] = $email;
        } else {
            $message = '<div class="alert alert-info">Aucune modification effectuée.</div>';
        }
    }
}

// Changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (!password_verify($current_password, $user["password"])) {
        $message = '<div class="alert alert-danger">Mot de passe actuel incorrect.</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Les mots de passe ne correspondent pas.</div>';
    } elseif (strlen($new_password) < 8) {
        $message = '<div class="alert alert-danger">Le mot de passe doit contenir au moins 8 caractères.</div>';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed_password, $id_user]);

        if ($update->rowCount() > 0) {
            $message = '<div class="alert alert-success">Mot de passe modifié.</div>';
        } else {
            $message = '<div class="alert alert-danger">Erreur lors de la modification.</div>';
        }
    }
}

// Inclure l'entête
include_once '../includes/header.php';
?>

<div class="container my-4">
    <h1>Mon Compte</h1>
    <?php echo $message; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h2>Informations personnelles</h2></div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user["username"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user["role"]); ?>" readonly>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h2>Changer le mot de passe</h2></div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Changer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des commandes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h2>Mes commandes récentes</h2></div>
                <div class="card-body">
                    <?php
                    try {
                        $orders_stmt = $conn->prepare("
                            SELECT o.id, o.date_commande, o.status, 
                                   SUM(o.quantite * i.prix) as total
                            FROM orders o
                            JOIN items i ON o.id_item = i.id
                            WHERE o.id_user = ?
                            GROUP BY o.id
                            ORDER BY o.date_commande DESC
                            LIMIT 5
                        ");
                        $orders_stmt->execute([$id_user]);
                        $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($orders) > 0) {
                            echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th>Commande #</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                            
                            foreach ($orders as $order) {
                                echo '<tr>
                                        <td>' . htmlspecialchars($order["id"]) . '</td>
                                        <td>' . date("d/m/Y", strtotime($order["date_commande"])) . '</td>
                                        <td>' . htmlspecialchars($order["status"]) . '</td>
                                        <td>' . number_format($order["total"], 2, ',', ' ') . ' €</td>
                                        <td><a href="order_details.php?id=' . $order["id"] . '" class="btn btn-sm btn-info">Voir</a></td>
                                    </tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<p>Vous n\'avez pas encore passé de commande.</p>';
                        }
                    } catch (PDOException $e) {
                        echo '<p class="alert alert-danger">Erreur lors de la récupération des commandes.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
