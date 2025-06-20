<?php
// Script pour créer les tables nécessaires
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Création des tables</h1>";

// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'chaulacel';
$db_user = 'root';
$db_pass = '';

try {
    // Créer une connexion sans spécifier de base de données
    echo "<p>Connexion à MySQL...</p>";
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion réussie</p>";
    
    // Créer la base de données si elle n'existe pas
    echo "<p>Création de la base de données $db_name...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "<p style='color:green'>✓ Base de données créée ou déjà existante</p>";
    
    // Se connecter à la base de données
    echo "<p>Connexion à la base de données $db_name...</p>";
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion à la base de données réussie</p>";
    
    // Supprimer les tables existantes si elles existent
    echo "<p>Suppression des tables existantes...</p>";
    $pdo->exec("DROP TABLE IF EXISTS password_resets");
    $pdo->exec("DROP TABLE IF EXISTS users");
    echo "<p style='color:green'>✓ Tables supprimées</p>";
    
    // Créer la table users
    echo "<p>Création de la table users...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(191) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL,
        last_login DATETIME NULL,
        UNIQUE KEY unique_email (email(191))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✓ Table users créée</p>";
    
    // Créer la table password_resets
    echo "<p>Création de la table password_resets...</p>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expiry DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✓ Table password_resets créée</p>";
    
    echo "<h2>Toutes les tables ont été créées avec succès!</h2>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
}
?>