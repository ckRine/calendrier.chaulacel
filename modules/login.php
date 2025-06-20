<?php
// Fichier de connexion utilisateur
header('Content-Type: application/json');

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin absolu vers le répertoire racine
$root_path = dirname(dirname(__FILE__));
require_once $root_path.'/common/conf.php';

// Vérifier si les données de connexion sont présentes
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;

// Vérifier si l'utilisateur existe dans la base de données
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // L'utilisateur existe, vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Mot de passe correct
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
            } else {
                // Mot de passe incorrect
                echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
                exit;
            }
        } else {
            // L'utilisateur n'existe pas
            echo json_encode(['success' => false, 'message' => 'Cet email n\'est pas enregistré', 'register' => true]);
            exit;
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion']);
        exit;
    }
} else {
    // Si pas de connexion à la base de données, on ne peut pas vérifier l'utilisateur
    echo json_encode(['success' => false, 'message' => 'Impossible de vérifier les identifiants', 'register' => true]);
    exit;
}

// Vérifier si l'utilisateur avait une connexion Google Calendar
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT token FROM google_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tokenData) {
            // Stocker le token dans la session pour une reconnexion automatique
            $_SESSION['google_access_token'] = json_decode($tokenData['token'], true);
            $_SESSION['google_connected'] = true;
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
    }
}

// Mettre à jour la date de dernière connexion
if (isset($pdo) && $pdo !== null && isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Gérer le "Se souvenir de moi"
        if ($remember) {
            // Créer la table remember_tokens si elle n'existe pas
            $pdo->exec("CREATE TABLE IF NOT EXISTS remember_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(100) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_id (user_id)
            )");
            
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Supprimer tout ancien token pour cet utilisateur
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Insérer le nouveau token
            $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $token, $expires_at]);
            
            // Définir un cookie pour le token de connexion automatique
            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
        }
    } catch (PDOException $e) {
        error_log('Erreur de mise à jour de la date de connexion ou du token: ' . $e->getMessage());
    }
}

// Simuler un délai de traitement
usleep(500000); // 500ms
setcookie('logged_out', '', time() - 3600, '/', '', false, true);

echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
?>