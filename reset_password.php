<?php
// Page de réinitialisation de mot de passe
include('./common/conf.php');

$error = null;
$success = null;
$token = isset($_GET['token']) ? $_GET['token'] : null;
$validToken = false;
$userId = null;

// Vérifier si la table password_resets existe
if (isset($pdo) && $pdo !== null) {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_id (user_id)
        )");
    } catch (PDOException $e) {
        error_log('Erreur de création de table: ' . $e->getMessage());
    }
}

// Vérifier si le token est valide
if ($token && isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expiry > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reset) {
            $validToken = true;
            $userId = $reset['user_id'];
        } else {
            $error = "Ce lien de réinitialisation est invalide ou a expiré.";
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
        $error = "Une erreur est survenue lors de la vérification du token: " . $e->getMessage();
    }
} else if (!$token) {
    $error = "Aucun token de réinitialisation fourni.";
}

// Traiter le formulaire de réinitialisation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($password)) {
        $error = "Le mot de passe est requis.";
    } else if ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Mettre à jour le mot de passe de l'utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            // Supprimer le token de réinitialisation
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
        } catch (PDOException $e) {
            error_log('Erreur de base de données: ' . $e->getMessage());
            $error = "Une erreur est survenue lors de la réinitialisation du mot de passe: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - ChronoGestCal</title>
    <link rel="stylesheet" href="./statics/css/styles.css">
    <link rel="stylesheet" href="./statics/css/auth.css">
</head>
<body>
    <div class="reset-password-container">
        <h1>Réinitialisation de mot de passe</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
            <div class="back-link">
                <a href="./index.php">Retour à la page d'accueil</a>
            </div>
        <?php elseif ($validToken): ?>
            <form method="post">
                <div>
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-buttons">
                    <button type="submit">Réinitialiser le mot de passe</button>
                </div>
            </form>
        <?php else: ?>
            <div class="back-link">
                <a href="./index.php">Retour à la page d'accueil</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>