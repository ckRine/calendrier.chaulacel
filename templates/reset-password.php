<?php
// Page de réinitialisation de mot de passe
include('./common/conf.php');

// Vérifier si un token est présent
if (!isset($_GET['token'])) {
    header('Location: ./index.php');
    exit;
}

$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Calendrier Scolaire</title>
    <link rel="stylesheet" href="./statics/css/styles.css">
</head>
<body>
    <div class="reset-password-container">
        <h1>Réinitialisation de mot de passe</h1>
        <form id="reset-password-form">
            <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div>
                <label for="new-password">Nouveau mot de passe</label>
                <input type="password" id="new-password" name="password" required>
            </div>
            <div>
                <label for="confirm-password">Confirmer le mot de passe</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
            <div class="form-buttons">
                <button type="submit">Réinitialiser</button>
            </div>
        </form>
        <div id="reset-message"></div>
        <div class="back-link">
            <a href="./index.php">Retour à l'accueil</a>
        </div>
    </div>

    <script>
        document.getElementById('reset-password-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const password = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const token = document.getElementById('token').value;
            
            // Vérifier que les mots de passe correspondent
            if (password !== confirmPassword) {
                document.getElementById('reset-message').innerHTML = '<div class="error">Les mots de passe ne correspondent pas</div>';
                return;
            }
            
            // Afficher un message de chargement
            document.getElementById('reset-message').innerHTML = '<div class="loading">Traitement en cours...</div>';
            
            // Envoyer la requête au serveur
            fetch('./modules/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `token=${encodeURIComponent(token)}&password=${encodeURIComponent(password)}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('reset-message').innerHTML = `<div class="success">${data.message}</div>`;
                    document.getElementById('reset-password-form').style.display = 'none';
                } else {
                    document.getElementById('reset-message').innerHTML = `<div class="error">${data.message}</div>`;
                }
            })
            .catch(error => {
                document.getElementById('reset-message').innerHTML = '<div class="error">Erreur de connexion au serveur</div>';
                console.error('Erreur:', error);
            });
        });
    </script>
</body>
</html>