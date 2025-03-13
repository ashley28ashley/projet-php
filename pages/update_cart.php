<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_produit']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
    exit;
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_produit = (int)$_POST['id_produit'];
$action = $_POST['action'];

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT quantité FROM panier WHERE id_utilisateur = ? AND id_produit = ?");
    $stmt->execute([$id_utilisateur, $id_produit]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $quantité = $result['quantité'];
        if ($action === 'add') {
            $quantité++;
        } else {
            $quantité--;
        }

        if ($quantité > 0) {
            $stmt = $conn->prepare("UPDATE panier SET quantité = ? WHERE id_utilisateur = ? AND id_produit = ?");
            $stmt->execute([$quantité, $id_utilisateur, $id_produit]);
        } else {
            $stmt = $conn->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_produit = ?");
            $stmt->execute([$id_utilisateur, $id_produit]);
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Panier mis à jour avec succès']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du panier: ' . $e->getMessage()]);
}
?>
