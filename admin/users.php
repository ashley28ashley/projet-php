<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Traitement du formulaire d'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validation des champs (à améliorer selon vos besoins)
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $success = "Utilisateur ajouté avec succès.";
            } else {
                $error = "Erreur lors de l'ajout de l'utilisateur.";
            }
        }
    }
}

// Récupérer tous les utilisateurs
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Utilisateurs 👥</h2>

    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="add-user-form">
        <h3>Ajouter un nouvel utilisateur</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur:</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            <div class="form-group">
                <label for="role">Rôle:</label>
                <select id="role" name="role" required class="form-control">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Ajouter l'utilisateur</button>
        </form>
    </div>

    <h3>Liste des Utilisateurs</h3>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Créé le</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
                <form method="post" action="update_user.php">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <select name="role">
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                    <button type="submit">Modifier</button>
                </form>
            </td>
            <td><?= $user['created_at'] ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">🗑 Supprimer</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
