<?php
/**
 * Solution d'urgence pour le site calendrier.chaulacel.com
 * 
 * Ce script crée un fichier index.php minimal qui fonctionne sans Twig
 * À exécuter directement sur le serveur
 */

// Contenu du fichier index.php d'urgence
$content = <<<'EOD'
<?php
// Fichier index.php d'urgence sans Twig
// Démarrer la session
session_start();

// Configuration de base
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('STATICS_PATH', './statics');
define('MODULES_PATH', './modules');

// Connexion à la base de données
$db_host = 'localhost';
$db_name = 'chronogestcal';
$db_user = 'root';
$db_pass = '';

// Connexion à la base de données
$pdo = null;
if (class_exists('PDO')) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
$user = null;

if ($is_logged_in && $pdo) {
    // Récupérer les informations de l'utilisateur
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
    }
}

// Page d'accueil d'urgence
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoGestCal - Maintenance</title>
    <link rel="stylesheet" href="<?php echo STATICS_PATH; ?>/css/base.css">
    <link rel="stylesheet" href="<?php echo STATICS_PATH; ?>/css/main.css">
    <style>
        .maintenance-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .maintenance-title {
            color: #0066cc;
            margin-bottom: 20px;
        }
        .maintenance-message {
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .maintenance-status {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .maintenance-links {
            margin-top: 30px;
        }
        .maintenance-links a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .maintenance-links a:hover {
            background-color: #0055aa;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <h1 class="maintenance-title">ChronoGestCal</h1>
        
        <div class="maintenance-status">
            <?php if ($is_logged_in && $user): ?>
                <p>Connecté en tant que: <?php echo htmlspecialchars($user['email']); ?></p>
                <p><a href="?logout=1">Se déconnecter</a></p>
            <?php else: ?>
                <p>Vous n'êtes pas connecté.</p>
            <?php endif; ?>
        </div>
        
        <div class="maintenance-message">
            <h2>Site en maintenance</h2>
            <p>Notre site est actuellement en maintenance pour résoudre des problèmes techniques.</p>
            <p>Nous travaillons à rétablir tous les services le plus rapidement possible.</p>
            <p>Merci de votre patience et de votre compréhension.</p>
        </div>
        
        <div class="maintenance-links">
            <a href="mailto:contact@chaulacel.com">Nous contacter</a>
        </div>
    </div>
</body>
</html>
EOD;

// Chemin vers le fichier index.php
$indexPath = __DIR__ . '/index.php';

// Vérifier si le fichier existe
if (!file_exists($indexPath)) {
    die("Erreur: Le fichier index.php n'existe pas à l'emplacement attendu.");
}

// Sauvegarder le fichier original
if (!copy($indexPath, $indexPath . '.bak')) {
    die("Erreur: Impossible de sauvegarder le fichier original.");
}

// Écrire le nouveau contenu dans le fichier
if (!file_put_contents($indexPath, $content)) {
    die("Erreur: Impossible d'écrire dans le fichier index.php.");
}

echo "Succès: Le fichier index.php a été remplacé par une version d'urgence sans Twig.\n";
echo "Terminé. Veuillez recharger votre application.\n";