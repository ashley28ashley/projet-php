<?php
require_once '../config/db.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Vérifier si un ID utilisateur est fourni
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Empêcher la suppression de son propre compte
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }

    header("Location: users.php");
    exit;
}
?>
