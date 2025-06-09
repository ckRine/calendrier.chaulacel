<?php
// Google Calendar API Integration Module

// Vérifier si les bibliothèques Google sont installées
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class GoogleCalendarSync {
    private $client;
    private $service;
    
    public function __construct() {
        if (!class_exists('Google_Client')) {
            throw new Exception('Google API Client non installé. Exécutez "composer install"');
        }
        
        if (!file_exists(__DIR__ . '/../credentials.json')) {
            throw new Exception('Fichier credentials.json manquant');
        }
        
        $this->client = new Google_Client();
        $this->client->setApplicationName('Calendrier Chaulacel');
        $this->client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $this->client->setAuthConfig(__DIR__ . '/../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        
        $this->service = new Google_Service_Calendar($this->client);
    }
    
    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }
    
    public function setAccessToken($code) {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($accessToken['error'])) {
            throw new Exception($accessToken['error_description'] ?? $accessToken['error']);
        }
        
        $this->client->setAccessToken($accessToken);
        
        // Save the token to the session
        $_SESSION['google_access_token'] = $accessToken;
        
        // Save the token to the database for the current user
        if (isset($_SESSION['user_id'])) {
            $this->saveTokenToDatabase($_SESSION['user_id'], $accessToken);
        }
        
        return $accessToken;
    }
    
    public function loadAccessToken($userId) {
        // Load token from database
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT token FROM google_tokens WHERE user_id = ?");
            $stmt->execute([$userId]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($tokenData) {
                $accessToken = json_decode($tokenData['token'], true);
                $this->client->setAccessToken($accessToken);
                
                // Refresh token if needed
                if ($this->client->isAccessTokenExpired()) {
                    if ($this->client->getRefreshToken()) {
                        $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                        $this->saveTokenToDatabase($userId, $this->client->getAccessToken());
                    }
                }
                
                return true;
            }
        } catch (PDOException $e) {
            error_log('Erreur de base de données: ' . $e->getMessage());
        }
        
        return false;
    }
    
    private function saveTokenToDatabase($userId, $token) {
        global $pdo;
        
        try {
            // Vérifier si la table existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'google_tokens'");
            if ($stmt->rowCount() == 0) {
                // Créer la table si elle n'existe pas
                $pdo->exec("CREATE TABLE IF NOT EXISTS google_tokens (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY (user_id)
                )");
            }
            
            // Check if token already exists
            $stmt = $pdo->prepare("SELECT id FROM google_tokens WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->fetch()) {
                // Update existing token
                $stmt = $pdo->prepare("UPDATE google_tokens SET token = ? WHERE user_id = ?");
                $stmt->execute([json_encode($token), $userId]);
            } else {
                // Insert new token
                $stmt = $pdo->prepare("INSERT INTO google_tokens (user_id, token) VALUES (?, ?)");
                $stmt->execute([$userId, json_encode($token)]);
            }
        } catch (PDOException $e) {
            error_log('Erreur de base de données: ' . $e->getMessage());
            throw new Exception('Erreur lors de la sauvegarde du token: ' . $e->getMessage());
        }
    }
    
    public function getEvents($calendarId = 'primary', $timeMin = null, $timeMax = null) {
        if (!$timeMin) {
            $timeMin = date('c', strtotime('first day of this month 00:00:00'));
        }
        
        if (!$timeMax) {
            $timeMax = date('c', strtotime('last day of this month 23:59:59'));
        }
        
        $optParams = [
            'maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
        ];
        
        try {
            $results = $this->service->events->listEvents($calendarId, $optParams);
            return $results->getItems();
        } catch (Exception $e) {
            error_log('Error fetching Google Calendar events: ' . $e->getMessage());
            return [];
        }
    }
    
    public function formatEventsForCalendar($events) {
        $formattedEvents = [];
        
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            
            $end = $event->end->dateTime;
            if (empty($end)) {
                $end = $event->end->date;
            }
            
            $formattedEvents[] = [
                'id' => $event->id,
                'title' => $event->getSummary(),
                'start' => $start,
                'end' => $end,
                'description' => $event->getDescription(),
                'source' => 'google'
            ];
        }
        
        return $formattedEvents;
    }
}
?>