<?php
// Script pour corriger la table user_preferences
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Correction de la table user_preferences</h1>";

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
    
    // Vérifier si la table user_preferences existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_preferences'");
    if ($stmt->rowCount() > 0) {
        echo "<p>La table user_preferences existe déjà, suppression...</p>";
        $pdo->exec("DROP TABLE user_preferences");
        echo "<p style='color:green'>✓ Table supprimée</p>";
    }
    
    // Créer la table user_preferences
    echo "<p>Création de la table user_preferences...</p>";
    $pdo->exec("CREATE TABLE user_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        preferences TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green'>✓ Table user_preferences créée</p>";
    
    echo "<h2>La table user_preferences a été corrigée avec succès!</h2>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
}
?>