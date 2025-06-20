<?php
// Fichier d'inscription utilisateur
header('Content-Type: application/json');

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin absolu vers le répertoire racine
$root_path = dirname(dirname(__FILE__));
require_once $root_path.'/common/conf.php';

// Vérifier si les données d'inscription sont présentes
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide']);
    exit;
}

// Valider le mot de passe (au moins 8 caractères)
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
    exit;
}

// Vérifier si l'utilisateur existe déjà
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // L'utilisateur existe déjà
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit;
        }
        
        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insérer le nouvel utilisateur
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashed_password]);
        
        // Récupérer l'ID de l'utilisateur
        $user_id = $pdo->lastInsertId();
        
        // Connecter automatiquement l'utilisateur
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        
        // Mettre à jour la date de dernière connexion
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Simuler un délai de traitement
        usleep(500000); // 500ms
        
        echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
        exit;
    }
} else {
    // Si pas de connexion à la base de données, on ne peut pas inscrire l'utilisateur
    echo json_encode(['success' => false, 'message' => 'Impossible de créer un compte']);
    exit;
}
?>