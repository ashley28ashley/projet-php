<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_item']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
    exit;
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_item = (int)$_POST['id_item']; // Correction ici
$action = $_POST['action'];

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT quantité FROM panier WHERE id_utilisateur = ? AND id_item = ?");
    $stmt->execute([$id_utilisateur, $id_item]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $quantité = (int)$result['quantité']; // Cast en int pour éviter les erreurs
        if ($action === 'add') {
            $quantité++;
        } elseif ($action === 'remove') {
            $quantité--;
        }

        if ($quantité > 0) {
            $stmt = $conn->prepare("UPDATE panier SET quantité = ? WHERE id_utilisateur = ? AND id_item = ?");
            $stmt->execute([$quantité, $id_utilisateur, $id_item]);
        } else {
            $stmt = $conn->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_item = ?");
            $stmt->execute([$id_utilisateur, $id_item]);
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Panier mis à jour avec succès']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du panier: ' . $e->getMessage()]);
}
?>
