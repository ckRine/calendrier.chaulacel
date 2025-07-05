<?php
// Fichier de configuration principal

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Déterminer l'environnement (dev ou prod)
$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$is_prod = (strpos($http_host, 'localhost') === false && strpos($http_host, '127.0.0.1') === false);

// Définir les chemins selon l'environnement
if ($is_prod) {
    // Environnement de production
    define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
} else {
    // Environnement de développement
    define('ROOT_PATH', dirname(__DIR__));
}

define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('STATICS_PATH', './statics');
define('MODULES_PATH', './modules');

// Vérifier si l'utilisateur vient de se déconnecter
if (!isset($_COOKIE['logged_out'])) {
	// Vérifier si l'utilisateur a un cookie de connexion automatique
	if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
		$remember_token = $_COOKIE['remember_token'];
		
		// Vérifier si le token est valide
		if (isset($pdo) && $pdo !== null) {
			try {
				$stmt = $pdo->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()");
				$stmt->execute([$remember_token]);
				$token_data = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if ($token_data) {
					// Récupérer les informations de l'utilisateur
					$stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ?");
					$stmt->execute([$token_data['user_id']]);
					$user = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if ($user) {
						// Connecter automatiquement l'utilisateur
						$_SESSION['user_id'] = $user['id'];
						$_SESSION['user_email'] = $user['email'];
						
						// Mettre à jour la date de dernière connexion
						$stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
						$stmt->execute([$user['id']]);
						
						// Renouveler le token
						$expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
						$stmt = $pdo->prepare("UPDATE remember_tokens SET expires_at = ? WHERE token = ?");
						$stmt->execute([$expires_at, $remember_token]);
						
						// Renouveler le cookie
						setcookie('remember_token', $remember_token, strtotime('+30 days'), '/', '', false, true);
					}
				}
			} catch (PDOException $e) {
				error_log('Erreur lors de la vérification du token de connexion automatique: ' . $e->getMessage());
			}
		}
	}
}

// Vérifier si l'utilisateur a demandé une déconnexion via un paramètre
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
	// Vider complètement le tableau de session
	$_SESSION = array();

	// Détruire le cookie de session
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	
	// Supprimer le cookie de connexion automatique
	setcookie('remember_token', '', time() - 3600, '/', '', false, true);
	
	// Supprimer le token de la base de données si l'utilisateur est connecté
	if (isset($pdo) && $pdo !== null && isset($_SESSION['user_id'])) {
		try {
			$stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
			$stmt->execute([$_SESSION['user_id']]);
		} catch (PDOException $e) {
			error_log('Erreur lors de la suppression du token de connexion automatique: ' . $e->getMessage());
		}
	}
			
	// Détruire la session
	session_destroy();
		
    // Rediriger vers la page d'accueil
	header('Location: calendrier');
	exit;
}

// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'chaulacel'; // Nom de la base de données
$db_user = 'root';
$db_pass = '';

// Connexion à la base de données
$pdo = null;
if (class_exists('PDO')) {
    try {
        // Vérifier si la base de données existe, sinon la créer
        $temp_pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
        
        // Se connecter à la base de données
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la table users si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL,
            last_login DATETIME NULL
        )");
        
        // Créer la table password_resets si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id)
        )");
        
        // Créer la table google_tokens si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS google_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id)
        )");
        
    } catch (PDOException $e) {
        // En cas d'erreur, continuer sans base de données
        error_log("Erreur de connexion à la base de données: " . $e->getMessage());
    }
} else {
    error_log("Extension PDO non disponible sur ce serveur");
}

// Charger les préférences utilisateur
$selectedZones = ['A']; // Par défaut
?>