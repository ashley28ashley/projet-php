<?php
session_start();
require_once '../config/db.php';

// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_produit'])) {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
    exit;
}

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_produit = (int)$_POST['id_produit'];
    $id_utilisateur = (int)$_SESSION['id_utilisateur'];

    // Vérification produit existe
    $stmt = $conn->prepare("SELECT id FROM items WHERE id = ?");
    $stmt->execute([$id_produit]);
    if (!$stmt->fetch()) {
        throw new Exception("Produit introuvable");
    }

    // Gestion quantité
    $stmt = $conn->prepare("SELECT quantité FROM panier WHERE id_utilisateur = ? AND id_produit = ?");
    $stmt->execute([$id_utilisateur, $id_produit]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $new_quantite = $result['quantité'] + 1;
        $stmt = $conn->prepare("UPDATE panier SET quantité = ? WHERE id_utilisateur = ? AND id_produit = ?");
        $stmt->execute([$new_quantite, $id_utilisateur, $id_produit]);
    } else {
        $stmt = $conn->prepare("INSERT INTO panier (id_utilisateur, id_produit, quantité) VALUES (?, ?, 1)");
        $stmt->execute([$id_utilisateur, $id_produit]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Produit ajouté au panier']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => "Erreur base de données : " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
