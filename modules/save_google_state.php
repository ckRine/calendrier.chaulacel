<?php
// Fichier de sauvegarde de l'état de connexion à Google Calendar
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les données JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['connected'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Stocker l'état de connexion dans la session
$_SESSION['google_auto_connect'] = $data['connected'];

// Stocker l'état de connexion dans la base de données si disponible
if (isset($pdo) && $pdo !== null) {
    try {
        // Vérifier si la table existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_preferences'");
        if ($stmt->rowCount() == 0) {
            // Créer la table si elle n'existe pas
            $pdo->exec("CREATE TABLE IF NOT EXISTS user_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) NOT NULL,
                preferences TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY (user_id)
            )");
        }
        
        // Récupérer les préférences existantes
        $stmt = $pdo->prepare("SELECT preferences FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $preferences = [];
        if ($result) {
            $preferences = json_decode($result['preferences'], true);
        }
        
        // Mettre à jour l'état de connexion à Google
        $preferences['google_auto_connect'] = $data['connected'];
        $preferencesJson = json_encode($preferences);
        
        // Vérifier si des préférences existent déjà pour cet utilisateur
        if ($result) {
            // Mettre à jour les préférences existantes
            $stmt = $pdo->prepare("UPDATE user_preferences SET preferences = ?, updated_at = NOW() WHERE user_id = ?");
            $stmt->execute([$preferencesJson, $_SESSION['user_id']]);
        } else {
            // Insérer de nouvelles préférences
            $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, preferences) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $preferencesJson]);
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
    }
}

echo json_encode(['success' => true, 'message' => 'État de connexion Google sauvegardé']);
?>