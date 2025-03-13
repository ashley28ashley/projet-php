<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    // Rediriger vers la page de connexion si non connecté
    header("Location: login.php");
    exit();
}

// Inclure le fichier de configuration de la base de données
require_once '../config/db.php';

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement de la mise à jour du profil si formulaire soumis
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    
    // Vérifier si l'email existe déjà pour un autre utilisateur
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check_email->execute([$email, $user_id]);
    if ($check_email->rowCount() > 0) {
        $message = '<div class="alert alert-danger">Cet email est déjà utilisé par un autre compte.</div>';
    } else {
        // Mettre à jour le profil
        $update = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $update->execute([$username, $email, $user_id]);
        
        if ($update->rowCount() > 0) {
            $_SESSION["username"] = $username;
            $message = '<div class="alert alert-success">Votre profil a été mis à jour avec succès.</div>';
            // Mettre à jour les informations de l'utilisateur
            $user["username"] = $username;
            $user["email"] = $email;
        } else {
            $message = '<div class="alert alert-info">Aucune modification n\'a été effectuée.</div>';
        }
    }
}

// Traitement du changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Vérifier si le mot de passe actuel est correct
    if (!password_verify($current_password, $user["password"])) {
        $message = '<div class="alert alert-danger">Le mot de passe actuel est incorrect.</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Les nouveaux mots de passe ne correspondent pas.</div>';
    } elseif (strlen($new_password) < 8) {
        $message = '<div class="alert alert-danger">Le nouveau mot de passe doit contenir au moins 8 caractères.</div>';
    } else {
        // Mettre à jour le mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed_password, $user_id]);
        
        if ($update->rowCount() > 0) {
            $message = '<div class="alert alert-success">Votre mot de passe a été modifié avec succès.</div>';
        } else {
            $message = '<div class="alert alert-danger">Une erreur est survenue lors de la modification du mot de passe.</div>';
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
                <div class="card-header">
                    <h2>Informations personnelles</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user["role"]); ?>" readonly>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour le profil</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Changer le mot de passe</h2>
                </div>
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
                            <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Mes commandes récentes</h2>
                </div>
                <div class="card-body">
                    <p>Fonctionnalité des commandes en cours de développement.</p>
                    
                    <?php
                    /* Commenté pour éviter l'erreur fatale
                    // Vérifier si la table 'orders' existe
                    try {
                        $check_table = $conn->query("SHOW TABLES LIKE 'orders'");
                        if ($check_table->rowCount() > 0) {
                            // La table existe, récupérer les commandes
                            $orders_stmt = $conn->prepare("
                                SELECT orders.id, orders.order_date, orders.status, SUM(order_items.quantity * products.price) as total
                                FROM orders
                                JOIN order_items ON orders.id = order_items.order_id
                                JOIN products ON order_items.product_id = products.id
                                WHERE orders.user_id = ?
                                GROUP BY orders.id
                                ORDER BY orders.order_date DESC
                                LIMIT 5
                            ");
                            $orders_stmt->execute([$user_id]);
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
                                            <td>' . $order["id"] . '</td>
                                            <td>' . date("d/m/Y", strtotime($order["order_date"])) . '</td>
                                            <td>' . htmlspecialchars($order["status"]) . '</td>
                                            <td>' . number_format($order["total"], 2, ',', ' ') . ' €</td>
                                            <td><a href="order_details.php?id=' . $order["id"] . '" class="btn btn-sm btn-info">Détails</a></td>
                                        </tr>';
                                }
                                
                                echo '</tbody></table>';
                            } else {
                                echo '<p>Vous n\'avez pas encore passé de commande.</p>';
                            }
                        } else {
                            echo '<p>Le système de commandes n\'est pas encore configuré.</p>';
                        }
                    } catch (PDOException $e) {
                        echo '<p>Erreur lors de la récupération des commandes.</p>';
                    }
                    */
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include_once '../includes/footer.php';
?>