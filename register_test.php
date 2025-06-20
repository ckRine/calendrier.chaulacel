<?php
// Script de test pour l'inscription
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test d'inscription</h1>";

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier la connexion à la base de données
echo "<h2>Vérification de la connexion à la base de données</h2>";
if (isset($pdo) && $pdo !== null) {
    echo "<p style='color:green'>✓ Connexion à la base de données réussie</p>";
} else {
    echo "<p style='color:red'>✗ Pas de connexion à la base de données</p>";
    
    // Essayer de créer la connexion
    try {
        echo "<p>Tentative de création de la connexion...</p>";
        
        // Créer une connexion sans spécifier de base de données
        $temp_pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
        $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la base de données si elle n'existe pas
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS chaulacel");
        
        // Se connecter à la base de données
        $pdo = new PDO("mysql:host=localhost;dbname=chaulacel;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green'>✓ Connexion créée avec succès</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>✗ Erreur lors de la création de la connexion : " . $e->getMessage() . "</p>";
    }
}

// Vérifier si la table users existe
echo "<h2>Vérification de la table users</h2>";
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "<p style='color:green'>✓ La table users existe</p>";
        } else {
            echo "<p style='color:orange'>⚠ La table users n'existe pas</p>";
            
            // Créer la table
            echo "<p>Tentative de création de la table...</p>";
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                last_login DATETIME NULL
            )");
            echo "<p style='color:green'>✓ Table users créée avec succès</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>✗ Erreur lors de la vérification/création de la table : " . $e->getMessage() . "</p>";
    }
}

// Formulaire de test d'inscription
echo "<h2>Formulaire de test d'inscription</h2>";
echo "<form method='post' action='register_test.php'>";
echo "<div><label for='email'>Email:</label> <input type='email' name='email' id='email' required></div>";
echo "<div><label for='password'>Mot de passe:</label> <input type='password' name='password' id='password' required></div>";
echo "<div><button type='submit' name='register'>S'inscrire</button></div>";
echo "</form>";

// Traiter le formulaire
if (isset($_POST['register'])) {
    echo "<h2>Résultat de l'inscription</h2>";
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    echo "<p>Tentative d'inscription avec l'email: " . htmlspecialchars($email) . "</p>";
    
    if (isset($pdo) && $pdo !== null) {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingUser) {
                echo "<p style='color:red'>✗ Cet email est déjà utilisé</p>";
            } else {
                // Hasher le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer le nouvel utilisateur
                $stmt = $pdo->prepare("INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$email, $hashedPassword]);
                
                // Récupérer l'ID de l'utilisateur
                $userId = $pdo->lastInsertId();
                
                echo "<p style='color:green'>✓ Inscription réussie ! ID utilisateur: " . $userId . "</p>";
                
                // Créer la session utilisateur
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                
                echo "<p>Session utilisateur créée.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>✗ Erreur lors de l'inscription : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Pas de connexion à la base de données</p>";
    }
}

// Afficher les utilisateurs existants
echo "<h2>Utilisateurs existants</h2>";
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->query("SELECT id, email, created_at FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Email</th><th>Date de création</th></tr>";
            
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . $user['created_at'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>Aucun utilisateur enregistré.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>✗ Erreur lors de la récupération des utilisateurs : " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>