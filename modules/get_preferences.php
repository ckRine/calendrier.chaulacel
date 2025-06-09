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
    'selected_calendars' => []
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
    
    // Sinon, essayer de récupérer depuis la base de données
    if (isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare("SELECT preferences FROM user_preferences WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
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
    'isConnected' => $isConnected
]);
?>