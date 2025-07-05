<?php
// Déconnexion de Google Calendar
header('Content-Type: application/json');

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Définir le chemin absolu vers le répertoire racine
    $root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
    require_once $root_path.'/common/conf.php';

    if (!isset($_SESSION['user_id']) || strpos($_SESSION['user_id'], 'temp_') === 0) {
        echo json_encode(['success' => false, 'message' => 'Aucune connexion à déconnecter']);
        exit;
    }

    // Supprimer le token de la base de données
    if (isset($pdo) && $pdo !== null) {
        $stmt = $pdo->prepare("DELETE FROM google_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    // Supprimer le token de la session
    if (isset($_SESSION['google_access_token'])) {
        unset($_SESSION['google_access_token']);
    }
    if (isset($_SESSION['google_connected'])) {
        unset($_SESSION['google_connected']);
    }

    echo json_encode(['success' => true, 'message' => 'Déconnexion de Google Calendar réussie']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la déconnexion: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>