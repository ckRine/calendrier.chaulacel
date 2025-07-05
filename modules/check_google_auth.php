<?php
// Vérifier l'état de l'authentification Google Calendar
header('Content-Type: application/json');

try {
	// Définir le chemin absolu vers le répertoire racine
	$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
	require_once $root_path.'/common/conf.php';

	// Vérifier si l'utilisateur est connecté
	if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
		echo json_encode(['success' => true, 'connected' => false, 'message' => 'Utilisateur non connecté', 'should_auto_connect' => false]);
		exit;
	}

	// Vérifier si les bibliothèques Google sont installées
	if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
			echo json_encode(['success' => false, 'message' => 'Google API Client non installé', 'connected' => false, 'should_auto_connect' => false]);
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
			'connected' => true,
			'should_auto_connect' => false
		]);
		exit;
	}

	// Vérifier si l'utilisateur a choisi de se connecter automatiquement
	$should_auto_connect = false;
	if (isset($_SESSION['google_auto_connect']) && $_SESSION['google_auto_connect']) {
		$should_auto_connect = true;
	} else {
		// Vérifier dans la base de données
		if (isset($pdo) && $pdo !== null) {
			try {
				$stmt = $pdo->prepare("SELECT preferences FROM user_preferences WHERE user_id = ?");
				$stmt->execute([$_SESSION['user_id']]);
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($result) {
					$preferences = json_decode($result['preferences'], true);
					if (isset($preferences['google_auto_connect']) && $preferences['google_auto_connect']) {
						$should_auto_connect = true;
						$_SESSION['google_auto_connect'] = true;
					}
				}
			} catch (PDOException $e) {
				error_log('Erreur de base de données: ' . $e->getMessage());
			}
		}
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
		'connected' => $connected,
		'should_auto_connect' => $should_auto_connect && !$connected
	]);
} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => 'Erreur: ' . $e->getMessage(),
		'connected' => false,
		'should_auto_connect' => false
	]);
}
?>