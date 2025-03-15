<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

$id_user = $_SESSION['id_utilisateur'];

try {
    $stmt = $conn->prepare("
        SELECT p.id, p.nom, p.prix, p.image, pa.quantité 
        FROM panier pa
        JOIN items p ON pa.id_item = p.id
        WHERE pa.id_utilisateur = ?
    ");
    $stmt->execute([$id_user]);
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $panier]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>
