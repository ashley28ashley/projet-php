<?php
session_start();
include '../includes/header.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les informations du formulaire 
    $nom_carte = $_POST["nom_carte"];
    $numero_carte = $_POST["numero_carte"];
    $date_expiration = $_POST["date_expiration"];
    $cvv = $_POST["cvv"];

    // Simuler un traitement réussi 
    $message = "Paiement simulé réussi ! Merci pour votre commande.";

    // Supprimer les informations de la commande
    unset($_SESSION['commande_confirmee']);
    unset($_SESSION['montant_total']);

    // Afficher un message de succès
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-success" role="alert">';
    echo '<h3>' . $message . '</h3>';
    echo '<p>Votre commande a été traitée avec succès. Vous recevrez un e-mail de confirmation sous peu.</p>';
    echo '<a href="catalogue.php" class="btn btn-primary">Retour au catalogue</a>';
    echo '</div>';
    echo '</div>';
} else {
    // ici Si on accède à cette page sans soumettre le formulaire, rediriger vers le formulaire de paiement
    header("Location: formulaire_paiement.php");
    exit;
}

include '../includes/footer.php';
?>