<?php
session_start();
include '../includes/header.php';

// Vérifier si la commande a été confirmée (vous devrez peut-être adapter cela à votre logique)
if (!isset($_SESSION['commande_confirmee'])) {
    header('Location: checkout.php'); 
    exit;
}

// Récupérer le montant total 
$montantTotal = isset($_SESSION['montant_total']) ? $_SESSION['montant_total'] : 0;

?>

<div class="container mt-5">
    <h2>Informations de Paiement</h2>
    <p>Veuillez remplir le formulaire ci-dessous pour finaliser votre commande d'un montant de <?php echo htmlspecialchars($montantTotal); ?> €.</p>

    <form action="traitement_paiement_fictif.php" method="post">
        <div class="form-group">
            <label for="nom_carte">Nom sur la carte :</label>
            <input type="text" class="form-control" id="nom_carte" name="nom_carte" required>
        </div>
        <div class="form-group">
            <label for="numero_carte">Numéro de carte :</label>
            <input type="text" class="form-control" id="numero_carte" name="numero_carte" pattern="[0-9]{16}" placeholder="16 chiffres" required>
        </div>
        <div class="form-group">
            <label for="date_expiration">Date d'expiration :</label>
            <input type="text" class="form-control" id="date_expiration" name="date_expiration" placeholder="MM/AA" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" required>
        </div>
        <div class="form-group">
            <label for="cvv">CVV :</label>
            <input type="text" class="form-control" id="cvv" name="cvv" pattern="[0-9]{3,4}" placeholder="3 ou 4 chiffres" required>
        </div>
        <button type="submit" class="btn btn-primary">Confirmer le Paiement</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>
