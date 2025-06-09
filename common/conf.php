<?php
// Fichier de configuration principal

// Définir les chemins
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel');
define('TEMPLATES_PATH', ROOT_PATH.'/templates');
define('STATICS_PATH', './statics');
define('MODULES_PATH', './modules');

// Vérifier si l'utilisateur vient de se déconnecter
if (!isset($_COOKIE['logged_out'])) {
	session_start();
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
			
		// Détruire la session
		session_destroy();
		
    // Rediriger vers la page d'accueil
		header('Location: ./index.php');
		exit;
}

// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'chaulacel'; // Nom de la base de données existante
$db_user = 'root';
$db_pass = '';

// Connexion à la base de données
$pdo = null;
if (class_exists('PDO')) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
