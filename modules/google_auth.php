<?php
// Google Authentication Handler

try {
	// Définir le chemin absolu vers le répertoire racine
	$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
	require_once $root_path.'/common/conf.php';

	// Rediriger vers la page d'accueil si l'utilisateur n'est pas connecté
	if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
		header('Content-Type: application/json');
		echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour utiliser Google Calendar']);
		exit;
	}

	// Vérifier si les bibliothèques Google sont installées
	if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
		header('Content-Type: application/json');
		echo json_encode(['success' => false, 'message' => 'Google API Client non installé']);
		exit;
	}

	require_once __DIR__ . '/../vendor/autoload.php';
		
	// Vérifier si le fichier credentials.json existe
	if (!file_exists(__DIR__ . '/../credentials.json')) {
		header('Content-Type: application/json');
		echo json_encode(['success' => false, 'message' => 'Fichier credentials.json manquant']);
		exit;
	}
		
	// Désactiver la vérification SSL pour CURL (uniquement en développement)
	$arrContextOptions = [
		"ssl" => [
			"verify_peer" => false,
			"verify_peer_name" => false,
		],
	];
	stream_context_set_default($arrContextOptions);
		
	// Configurer le client Google
	$client = new Google_Client();
	$client->setApplicationName('Calendrier Chaulacel');
	$client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
	$client->setAuthConfig(__DIR__ . '/../credentials.json');
	$client->setAccessType('offline');
	$client->setPrompt('select_account consent');
		
	// Désactiver la vérification SSL pour CURL dans le client Google
	$httpClient = new GuzzleHttp\Client(['verify' => false]);
	$client->setHttpClient($httpClient);
		
	// Définir l'URI de redirection
	$redirectUri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/calendrier.chaulacel/modules/google_auth.php";
	$client->setRedirectUri($redirectUri);
		
	// Traiter le code d'autorisation si présent
	if (isset($_GET['code'])) {
		$accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
		
		if (isset($accessToken['error'])) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Erreur d\'authentification: ' . $accessToken['error']]);
			exit;
		}
		
		// Sauvegarder le token dans la session
		$_SESSION['google_access_token'] = $accessToken;
		
		// Sauvegarder le token dans la base de données
		try {
			// Vérifier si la connexion à la base de données est disponible
			if (isset($pdo) && $pdo !== null) {
				// Vérifier si la table existe
				$stmt = $pdo->query("SHOW TABLES LIKE 'google_tokens'");
				if ($stmt->rowCount() == 0) {
					// Créer la table si elle n'existe pas
					$pdo->exec("CREATE TABLE IF NOT EXISTS google_tokens (
						id INT AUTO_INCREMENT PRIMARY KEY,
						user_id VARCHAR(50) NOT NULL,
						token TEXT NOT NULL,
						created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						UNIQUE KEY (user_id)
					)");
				}
				
				// Vérifier si un token existe déjà pour cet utilisateur
				$stmt = $pdo->prepare("SELECT id FROM google_tokens WHERE user_id = ?");
				$stmt->execute([$_SESSION['user_id']]);
				
				if ($stmt->fetch()) {
					// Mettre à jour le token existant
					$stmt = $pdo->prepare("UPDATE google_tokens SET token = ? WHERE user_id = ?");
					$stmt->execute([json_encode($accessToken), $_SESSION['user_id']]);
				} else {
					// Insérer un nouveau token
					$stmt = $pdo->prepare("INSERT INTO google_tokens (user_id, token) VALUES (?, ?)");
					$stmt->execute([$_SESSION['user_id'], json_encode($accessToken)]);
				}
			} else {
				// Mode sans base de données - stocker dans la session
				$_SESSION['google_connected'] = true;
				$_SESSION['google_access_token'] = $accessToken;
			}
		} catch (PDOException $e) {
			error_log('Erreur de base de données: ' . $e->getMessage());
			// Mode sans base de données - stocker dans la session
			$_SESSION['google_connected'] = true;
			$_SESSION['google_access_token'] = $accessToken;
		}
		
		// Afficher une page de confirmation et fermer la fenêtre
		header('Content-Type: text/html');
		echo '<!DOCTYPE html>
		<html>
		<head>
			<title>Authentification réussie</title>
			<style>
				body {
					font-family: Arial, sans-serif;
					text-align: center;
					margin-top: 50px;
					background-color: #f5f5f5;
				}
				.success {
					color: #4CAF50;
					font-size: 24px;
					margin-bottom: 20px;
				}
				.message {
					font-size: 18px;
					margin-bottom: 30px;
				}
				.closing {
					font-size: 14px;
					color: #666;
				}
			</style>
		</head>
		<body>
			<div class="success">✓ Authentification réussie</div>
			<div class="message">Connexion à Google Calendar établie</div>
			<div class="closing">Cette fenêtre va se fermer automatiquement...</div>
			<script>
				window.opener.postMessage("google-auth-success", "*");
				setTimeout(function() {
					window.close();
				}, 2000);
			</script>
		</body>
		</html>';
		exit;
	}

	// Générer l'URL d'authentification
	$authUrl = $client->createAuthUrl();
	header('Content-Type: application/json');
	echo json_encode(['success' => true, 'authUrl' => $authUrl]);
} catch (Exception $e) {
	header('Content-Type: application/json');
	echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>