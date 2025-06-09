<?php
// Vérifier l'état de l'authentification Google Calendar
header('Content-Type: application/json');

try {
	// Définir le chemin absolu vers le répertoire racine
	$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
	require_once $root_path.'/common/conf.php';

	// Vérifier si l'utilisateur est connecté
	if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
		echo json_encode(['success' => true, 'connected' => false, 'message' => 'Utilisateur non connecté']);
		exit;
	}

	// Vérifier si les bibliothèques Google sont installées
	if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
			echo json_encode(['success' => false, 'message' => 'Google API Client non installé', 'connected' => false]);
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

	require_once __DIR__ . '/../vendor/autoload.php';
	
	// Vérifier si un token existe dans la session
	if (isset($_SESSION['google_access_token']) && isset($_SESSION['google_connected']) && $_SESSION['google_connected']) {
		echo json_encode([
			'success' => true,
			'connected' => true
		]);
		exit;
	}
	
	// Vérifier si un token existe dans la base de données
	$connected = false;
	
	// Vérifier si la connexion à la base de données est disponible
	if (isset($pdo) && $pdo !== null) {
			try {
					$stmt = $pdo->prepare("SELECT token FROM google_tokens WHERE user_id = ?");
					$stmt->execute([$_SESSION['user_id']]);
					$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if ($tokenData) {
							$connected = true;
							// Stocker le token dans la session pour une reconnexion automatique
							$_SESSION['google_access_token'] = json_decode($tokenData['token'], true);
							$_SESSION['google_connected'] = true;
					}
			} catch (PDOException $e) {
					error_log('Erreur de base de données: ' . $e->getMessage());
			}
	} else {
			// Mode démo sans base de données
			$connected = isset($_SESSION['google_connected']) && $_SESSION['google_connected'];
	}

	echo json_encode([
			'success' => true,
			'connected' => $connected
	]);
} catch (Exception $e) {
	echo json_encode([
			'success' => false,
			'message' => 'Erreur: ' . $e->getMessage(),
			'connected' => false
	]);
}
?>