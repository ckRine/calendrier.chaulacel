<?php
// Fetch Google Calendar Events
header('Content-Type: application/json');

try {
		// Définir le chemin absolu vers le répertoire racine
		$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
		require_once $root_path.'/common/conf.php';

		// Vérifier si l'utilisateur est connecté
		if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
				echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté', 'needAuth' => true]);
				exit;
		}

		// Vérifier si les bibliothèques Google sont installées
		if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
				echo json_encode(['success' => false, 'message' => 'Google API Client non installé']);
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
		
		// Récupérer les paramètres de date
		$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
		$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
		$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

		// Calculer les dates de début et de fin pour les 5 mois (-2, -1, 0, +1, +2)
		$startMonth = $month - 2 + $offset;
		$startYear = $year;
		$endMonth = $month + 2 + $offset;
		$endYear = $year;
		
		// Ajuster l'année si nécessaire
		while ($startMonth < 1) {
				$startMonth += 12;
				$startYear--;
		}
		while ($startMonth > 12) {
				$startMonth -= 12;
				$startYear++;
		}
		while ($endMonth < 1) {
				$endMonth += 12;
				$endYear--;
		}
		while ($endMonth > 12) {
				$endMonth -= 12;
				$endYear++;
		}
		
		// Calculer les dates de début et de fin pour la plage de 5 mois
		$timeMin = date('c', strtotime("$startYear-$startMonth-01 00:00:00"));
		$timeMax = date('c', strtotime("$endYear-$endMonth-" . date('t', strtotime("$endYear-$endMonth-01")) . " 23:59:59"));

		// Configurer le client Google
		$client = new Google_Client();
		$client->setApplicationName('Calendrier Chaulacel');
		$client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
		$client->setAuthConfig(__DIR__ . '/../credentials.json');
		
		// Désactiver la vérification SSL pour CURL dans le client Google
		$httpClient = new GuzzleHttp\Client(['verify' => false]);
		$client->setHttpClient($httpClient);
		
		// Récupérer le token d'accès
		$accessToken = null;
		
		// Essayer de récupérer depuis la base de données si disponible
		if (isset($pdo) && $pdo !== null) {
				try {
						$stmt = $pdo->prepare("SELECT token FROM google_tokens WHERE user_id = ?");
						$stmt->execute([$_SESSION['user_id']]);
						$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
						
						if ($tokenData) {
								$accessToken = json_decode($tokenData['token'], true);
						}
				} catch (PDOException $e) {
						error_log('Erreur de base de données: ' . $e->getMessage());
				}
		}
		
		// Si pas de token dans la base de données, essayer depuis la session
		if (!$accessToken && isset($_SESSION['google_access_token'])) {
				$accessToken = $_SESSION['google_access_token'];
		}
		
		// Vérifier si on a un token
		if (!$accessToken) {
				echo json_encode([
						'success' => false, 
						'message' => 'Non connecté à Google Calendar',
						'needAuth' => true
				]);
				exit;
		}
		
		$client->setAccessToken($accessToken);
		
		// Rafraîchir le token si nécessaire
		if ($client->isAccessTokenExpired()) {
				if ($client->getRefreshToken()) {
						$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
						$newAccessToken = $client->getAccessToken();
						
						// Mettre à jour le token dans la base de données si disponible
						if (isset($pdo) && $pdo !== null) {
								try {
										$stmt = $pdo->prepare("UPDATE google_tokens SET token = ? WHERE user_id = ?");
										$stmt->execute([json_encode($newAccessToken), $_SESSION['user_id']]);
								} catch (PDOException $e) {
										error_log('Erreur de base de données: ' . $e->getMessage());
								}
						}
						
						// Mettre à jour le token dans la session
						$_SESSION['google_access_token'] = $newAccessToken;
				} else {
						echo json_encode([
								'success' => false, 
								'message' => 'Token expiré, reconnexion nécessaire',
								'needAuth' => true
						]);
						exit;
				}
		}
		
		// Créer le service Calendar
		$service = new Google_Service_Calendar($client);
		
		// Récupérer les couleurs disponibles
		$colors = $service->colors->get();
		$eventColors = $colors->getEvent();
		$calendarColors = $colors->getCalendar();
		
		// Récupérer la liste des calendriers
		$calendarList = $service->calendarList->listCalendarList();
		
		// Paramètres pour la récupération des événements
		$optParams = [
				'maxResults' => 100,
				'orderBy' => 'startTime',
				'singleEvents' => true,
				'timeMin' => $timeMin,
				'timeMax' => $timeMax,
		];
		
		// Récupérer les événements de tous les calendriers
		$formattedEvents = [];
		$formattedCalendars = [];
		
		foreach ($calendarList as $calendarListEntry) {
				$calendarId = $calendarListEntry->getId();
				$calendarName = $calendarListEntry->getSummary();
				$calendarColorId = $calendarListEntry->getColorId();
				$calendarBackgroundColor = $calendarListEntry->getBackgroundColor() ?: 
																($calendarColorId && isset($calendarColors[$calendarColorId]) ? 
																$calendarColors[$calendarColorId]->getBackground() : '#4285F4');
				$calendarForegroundColor = $calendarListEntry->getForegroundColor() ?: 
																($calendarColorId && isset($calendarColors[$calendarColorId]) ? 
																$calendarColors[$calendarColorId]->getForeground() : '#FFFFFF');
				
				$formattedCalendars[] = [
						'id' => $calendarId,
						'name' => $calendarName,
						'backgroundColor' => $calendarBackgroundColor,
						'foregroundColor' => $calendarForegroundColor,
						'colorId' => $calendarColorId
				];
				
				try {
						$results = $service->events->listEvents($calendarId, $optParams);
						$events = $results->getItems();
						
						foreach ($events as $event) {
								$start = $event->start->dateTime;
								if (empty($start)) {
										$start = $event->start->date;
								}
								
								$end = $event->end->dateTime;
								if (empty($end)) {
										$end = $event->end->date;
								}
								
								// Déterminer la couleur de l'événement
								$eventColorId = $event->getColorId();
								$eventBackgroundColor = $eventColorId && isset($eventColors[$eventColorId]) ? 
																		$eventColors[$eventColorId]->getBackground() : $calendarBackgroundColor;
								$eventForegroundColor = $eventColorId && isset($eventColors[$eventColorId]) ? 
																		$eventColors[$eventColorId]->getForeground() : $calendarForegroundColor;
								
								$formattedEvents[] = [
										'id' => $event->id,
										'title' => $event->getSummary(),
										'start' => $start,
										'end' => $end,
										'description' => $event->getDescription(),
										'source' => 'google',
										'calendarId' => $calendarId,
										'calendarName' => $calendarName,
										'backgroundColor' => $eventBackgroundColor,
										'foregroundColor' => $eventForegroundColor,
										'colorId' => $eventColorId ?: $calendarColorId
								];
						}
				} catch (Exception $e) {
						// Ignorer les erreurs pour un calendrier spécifique et continuer
						error_log('Erreur lors de la récupération des événements pour le calendrier ' . $calendarId . ': ' . $e->getMessage());
				}
		}
		
		echo json_encode([
				'success' => true,
				'events' => $formattedEvents,
				'calendars' => $formattedCalendars,
				'debug' => [
						'year' => $year,
						'month' => $month,
						'timeMin' => $timeMin,
						'timeMax' => $timeMax,
						'eventCount' => count($formattedEvents),
						'calendarCount' => count($calendarList->getItems())
				]
		]);
} catch (Exception $e) {
		echo json_encode([
				'success' => false,
				'message' => 'Erreur lors de la récupération des événements: ' . $e->getMessage(),
				'trace' => $e->getTraceAsString()
		]);
}
?>