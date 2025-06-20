<?php
// Fichier de sauvegarde des préférences utilisateur
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

if (!isset($data['zones']) || !is_array($data['zones'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Stocker les préférences dans la session
$_SESSION['zones'] = $data['zones'];

// Stocker les préférences dans la base de données si disponible
if (isset($pdo) && $pdo !== null) {
    try {
        // Vérifier si la table existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_preferences'");
        if ($stmt->rowCount() == 0) {
            // Créer la table si elle n'existe pas
            $pdo->exec("CREATE TABLE IF NOT EXISTS user_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                preferences TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
        
        // Vérifier si des préférences existent déjà pour cet utilisateur
        $stmt = $pdo->prepare("SELECT id FROM user_preferences WHERE user_id = ?");
        $stmt->execute([(int)$_SESSION['user_id']]);
        
        $preferences = json_encode(['zones' => $data['zones']]);
        
        if ($stmt->fetch()) {
            // Mettre à jour les préférences existantes
            $stmt = $pdo->prepare("UPDATE user_preferences SET preferences = ?, updated_at = NOW() WHERE user_id = ?");
            $stmt->execute([$preferences, (int)$_SESSION['user_id']]);
        } else {
            // Insérer de nouvelles préférences
            $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, preferences) VALUES (?, ?)");
            $stmt->execute([(int)$_SESSION['user_id'], $preferences]);
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
    }
}

echo json_encode(['success' => true, 'message' => 'Préférences sauvegardées']);
?>