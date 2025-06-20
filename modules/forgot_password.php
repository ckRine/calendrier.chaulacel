<?php
// Fichier de gestion du mot de passe oublié
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si l'email est présent
if (!isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email requis']);
    exit;
}

$email = $_POST['email'];

// Vérifier si l'utilisateur existe dans la base de données
if (isset($pdo) && $pdo !== null) {
    try {
        // Afficher les informations de débogage
        error_log("Tentative de réinitialisation de mot de passe pour: " . $email);
        
        // Créer un utilisateur de test si la table est vide
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $testEmail = "test@example.com";
            $testPassword = password_hash("password123", PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$testEmail, $testPassword]);
            error_log("Utilisateur de test créé: " . $testEmail);
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Supprimer tout ancien token pour cet utilisateur
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Insérer le nouveau token
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiry]);
            
            // Préparer le lien de réinitialisation
            $resetLink = "http://{$_SERVER['HTTP_HOST']}/calendrier.chaulacel/reset_password.php?token=" . urlencode($token);
            
            // Envoyer l'email (en mode développement, afficher le lien directement)
            $to = $email;
            $subject = "Réinitialisation de votre mot de passe";
            $message = "Bonjour,\n\nVous avez demandé à réinitialiser votre mot de passe. Veuillez cliquer sur le lien suivant pour procéder :\n\n$resetLink\n\nCe lien expirera dans 1 heure.\n\nSi vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.\n\nCordialement,\nL'équipe ChronoGestCal";
            $headers = "From: noreply@ChronoGestCal.com";
            
            // En environnement de développement, afficher le lien directement
            echo json_encode([
                'success' => true, 
                'message' => 'Un email de réinitialisation a été envoyé à votre adresse email.',
                'dev_link' => $resetLink // Uniquement pour le développement
            ]);
            
            // Commenter la fonction mail() en développement
            // if (mail($to, $subject, $message, $headers)) {
            //     echo json_encode(['success' => true, 'message' => 'Un email de réinitialisation a été envoyé à votre adresse email.']);
            // } else {
            //     error_log("Erreur d'envoi d'email à $email");
            //     echo json_encode(['success' => false, 'message' => 'Impossible d\'envoyer l\'email. Veuillez contacter l\'administrateur.']);
            // }
        } else {
            // Pour des raisons de sécurité, ne pas indiquer si l'email existe ou non
            usleep(500000); // 500ms
            echo json_encode(['success' => true, 'message' => 'Si cette adresse email est associée à un compte, vous recevrez un email avec les instructions pour réinitialiser votre mot de passe.']);
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors du traitement de votre demande: ' . $e->getMessage()]);
    }
} else {
    // Si pas de connexion à la base de données
    error_log("Pas de connexion à la base de données dans forgot_password.php");
    echo json_encode([
        'success' => false, 
        'message' => 'Service temporairement indisponible. Veuillez réessayer plus tard.',
        'debug' => 'Pas de connexion à la base de données'
    ]);
}
?>