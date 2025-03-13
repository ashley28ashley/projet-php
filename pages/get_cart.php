<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode([]);
    exit;
}

$id_utilisateur = $_SESSION['id_utilisateur'];

try {
    $stmt = $conn->prepare("
        SELECT p.id_produit, i.name, i.price, i.image, p.quantité 
        FROM panier p 
        JOIN items i ON p.id_produit = i.id 
        WHERE p.id_utilisateur = ?
    ");
    $stmt->execute([$id_utilisateur]);
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($panier);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération du panier: ' . $e->getMessage()]);
}
