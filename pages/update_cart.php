<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_items']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
    exit;
}

$id_user = $_SESSION['id_user'];
$id_items = (int)$_POST['id_items'];
$action = $_POST['action'];

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT quantité FROM panier WHERE id_user = ? AND id_items = ?");
    $stmt->execute([$id_user, $id_items]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $quantité = $result['quantité'];
        if ($action === 'add') {
            $quantité++;
        } else {
            $quantité--;
        }

        if ($quantité > 0) {
            $stmt = $conn->prepare("UPDATE panier SET quantité = ? WHERE id_user = ? AND id_items = ?");
            $stmt->execute([$quantité, $id_user, $id_items]);
        } else {
            $stmt = $conn->prepare("DELETE FROM panier WHERE id_user = ? AND id_items = ?");
            $stmt->execute([$id_user, $id_items]);
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Panier mis à jour avec succès']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du panier: ' . $e->getMessage()]);
}
?>
