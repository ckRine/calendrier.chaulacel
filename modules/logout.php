<?php
// Fichier de déconnexion utilisateur
header('Content-Type: application/json');

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin absolu vers le répertoire racine
$root_path = dirname(dirname(__FILE__));
require_once $root_path.'/common/conf.php';

// Vider complètement le tableau de session
$_SESSION = array();

// Détruire le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Supprimer le cookie de connexion automatique
setcookie('remember_token', '', time() - 3600, '/', '', false, true);

// Supprimer le token de la base de données si l'utilisateur est connecté
if (isset($pdo) && $pdo !== null && isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du token de connexion automatique: ' . $e->getMessage());
    }
}

// Définir un cookie pour indiquer que l'utilisateur s'est déconnecté
setcookie('logged_out', '1', time() + 60, '/', '', false, true);

// Détruire la session
session_destroy();

echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
?>