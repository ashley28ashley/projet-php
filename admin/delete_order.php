<?php
require_once '../config/db.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

// Vérifier si un ID de commande est fourni
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Supprimer la commande et ses détails
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);

    header("Location: orders.php");
    exit;
}
?>
