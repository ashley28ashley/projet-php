<?php
require_once '../config/db.php'; // Inclure le fichier de connexion à la base de données

// Informations de l'administrateur
$username = 'ashley';
$email = 'ashley@admin.com';
$password = 'ashley';

// Hacher le mot de passe
$password_hashé = password_hash($password, PASSWORD_DEFAULT);

// Préparer la requête SQL
$sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')";
$stmt = $conn->prepare($sql);

// Exécuter la requête
$stmt->execute([$username, $email, $password_hashé]);

echo "Administrateur ajouté avec succès.";
?>
