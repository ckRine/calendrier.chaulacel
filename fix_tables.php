<?php
// Script pour corriger les tables
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Correction des tables</h1>";

// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'chaulacel';
$db_user = 'root';
$db_pass = '';

try {
    // Se connecter à la base de données
    echo "<p>Connexion à la base de données $db_name...</p>";
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion réussie</p>";
    
    // Créer la table remember_tokens si elle n'existe pas
    echo "<p>Création de la table remember_tokens...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS remember_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(100) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✓ Table remember_tokens créée</p>";
    
    echo "<h2>Toutes les tables ont été corrigées avec succès!</h2>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
}
?>