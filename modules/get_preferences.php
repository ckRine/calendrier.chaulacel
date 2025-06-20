<?php
// Fichier de récupération des préférences utilisateur
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si l'utilisateur est connecté
$isConnected = isset($_SESSION['user_id']) && strpos($_SESSION['user_id'], 'temp_') !== 0;

// Préférences par défaut
$defaultPreferences = [
    'zones' => ['A'],
    'selected_calendars' => [],
    'google_auto_connect' => false
];
$preferences = $defaultPreferences;

// Si l'utilisateur est connecté, récupérer ses préférences
if ($isConnected) {
    // Vérifier d'abord dans la session
    if (isset($_SESSION['zones'])) {
        $preferences['zones'] = $_SESSION['zones'];
    }
    
    if (isset($_SESSION['selected_calendars'])) {
        $preferences['selected_calendars'] = $_SESSION['selected_calendars'];
    }
    
    if (isset($_SESSION['google_auto_connect'])) {
        $preferences['google_auto_connect'] = $_SESSION['google_auto_connect'];
    }
    
    // Sinon, essayer de récupérer depuis la base de données
    if (isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare("SELECT preferences FROM user_preferences WHERE user_id = ?");
            $stmt->execute([(int)$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $dbPreferences = json_decode($result['preferences'], true);
                
                if (isset($dbPreferences['zones'])) {
                    $preferences['zones'] = $dbPreferences['zones'];
                    // Mettre à jour la session
                    $_SESSION['zones'] = $dbPreferences['zones'];
                }
                
                if (isset($dbPreferences['selected_calendars'])) {
                    $preferences['selected_calendars'] = $dbPreferences['selected_calendars'];
                    // Mettre à jour la session
                    $_SESSION['selected_calendars'] = $dbPreferences['selected_calendars'];
                }
                
                if (isset($dbPreferences['google_auto_connect'])) {
                    $preferences['google_auto_connect'] = $dbPreferences['google_auto_connect'];
                    // Mettre à jour la session
                    $_SESSION['google_auto_connect'] = $dbPreferences['google_auto_connect'];
                }
            }
        } catch (PDOException $e) {
            error_log('Erreur de base de données: ' . $e->getMessage());
        }
    }
}

echo json_encode([
    'success' => true,
    'zones' => $preferences['zones'],
    'selected_calendars' => $preferences['selected_calendars'],
    'google_auto_connect' => $preferences['google_auto_connect'],
    'isConnected' => $isConnected
]);
?>