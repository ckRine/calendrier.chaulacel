<?php
// Fichier de débogage pour vérifier la connexion à la base de données
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Débogage de la connexion à la base de données</h1>";

// Vérifier si PDO est disponible
echo "<h2>Vérification de PDO</h2>";
if (class_exists('PDO')) {
    echo "<p style='color:green'>✓ PDO est disponible</p>";
    
    // Afficher les drivers PDO disponibles
    echo "<p>Drivers PDO disponibles : " . implode(', ', PDO::getAvailableDrivers()) . "</p>";
} else {
    echo "<p style='color:red'>✗ PDO n'est pas disponible</p>";
}

// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'chaulacel';
$db_user = 'root';
$db_pass = '';

echo "<h2>Configuration de la base de données</h2>";
echo "<ul>";
echo "<li>Hôte : $db_host</li>";
echo "<li>Base de données : $db_name</li>";
echo "<li>Utilisateur : $db_user</li>";
echo "</ul>";

// Tester la connexion à MySQL sans spécifier de base de données
echo "<h2>Test de connexion à MySQL</h2>";
try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion à MySQL réussie</p>";
    
    // Vérifier si la base de données existe
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
    $dbExists = (bool) $stmt->fetchColumn();
    
    if ($dbExists) {
        echo "<p style='color:green'>✓ La base de données '$db_name' existe</p>";
    } else {
        echo "<p style='color:orange'>⚠ La base de données '$db_name' n'existe pas</p>";
        
        // Créer la base de données
        echo "<p>Tentative de création de la base de données...</p>";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
        echo "<p style='color:green'>✓ Base de données créée</p>";
    }
    
    // Se connecter à la base de données
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion à la base de données '$db_name' réussie</p>";
    
    // Vérifier si la table users existe
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'users'");
    $tableExists = (bool) $stmt->fetchColumn();
    
    if ($tableExists) {
        echo "<p style='color:green'>✓ La table 'users' existe</p>";
        
        // Compter les utilisateurs
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "<p>Nombre d'utilisateurs : $userCount</p>";
        
        if ($userCount > 0) {
            echo "<p>Liste des utilisateurs :</p>";
            echo "<ul>";
            $stmt = $pdo->query("SELECT id, email FROM users");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>ID: {$row['id']} - Email: {$row['email']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:orange'>⚠ Aucun utilisateur dans la table</p>";
            
            // Créer un utilisateur de test
            echo "<p>Création d'un utilisateur de test...</p>";
            $testEmail = "test@example.com";
            $testPassword = password_hash("password123", PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$testEmail, $testPassword]);
            echo "<p style='color:green'>✓ Utilisateur de test créé : $testEmail (mot de passe: password123)</p>";
        }
    } else {
        echo "<p style='color:orange'>⚠ La table 'users' n'existe pas</p>";
        
        // Créer la table users
        echo "<p>Création de la table 'users'...</p>";
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL,
            last_login DATETIME NULL
        )");
        echo "<p style='color:green'>✓ Table 'users' créée</p>";
        
        // Créer un utilisateur de test
        echo "<p>Création d'un utilisateur de test...</p>";
        $testEmail = "test@example.com";
        $testPassword = password_hash("password123", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$testEmail, $testPassword]);
        echo "<p style='color:green'>✓ Utilisateur de test créé : $testEmail (mot de passe: password123)</p>";
    }
    
    // Vérifier si la table password_resets existe
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = 'password_resets'");
    $tableExists = (bool) $stmt->fetchColumn();
    
    if ($tableExists) {
        echo "<p style='color:green'>✓ La table 'password_resets' existe</p>";
    } else {
        echo "<p style='color:orange'>⚠ La table 'password_resets' n'existe pas</p>";
        
        // Créer la table password_resets
        echo "<p>Création de la table 'password_resets'...</p>";
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id)
        )");
        echo "<p style='color:green'>✓ Table 'password_resets' créée</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Erreur de connexion : " . $e->getMessage() . "</p>";
}

echo "<h2>Informations sur le serveur</h2>";
echo "<ul>";
echo "<li>PHP version : " . phpversion() . "</li>";
echo "<li>Document root : " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>HTTP Host : " . $_SERVER['HTTP_HOST'] . "</li>";
echo "</ul>";

echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>