<?php
// Fichier de connexion utilisateur
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si les données de connexion sont présentes
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;

// Mode de démonstration - accepter n'importe quel email/mot de passe
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = $email;

// Vérifier si l'utilisateur avait une connexion Google Calendar
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT token FROM google_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tokenData) {
            // Stocker le token dans la session pour une reconnexion automatique
            $_SESSION['google_access_token'] = json_decode($tokenData['token'], true);
            $_SESSION['google_connected'] = true;
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
    }
}

// Simuler un délai de traitement
usleep(500000); // 500ms
setcookie('logged_out', '', time() - 3600, '/', '', false, true);

echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
?>