<?php
require_once '../config/db.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Récupérer tous les utilisateurs
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Utilisateurs 👥</h2>

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
