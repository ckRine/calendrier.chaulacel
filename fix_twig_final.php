<?php
/**
 * Solution finale pour le problème Twig
 * À exécuter sur le serveur de production
 */

// Contenu du fichier twig.php simplifié
$content = <<<'EOD'
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration de Twig avec cache désactivé
$loader = new \Twig\Loader\FilesystemLoader(ROOT_PATH . '/templates');

// Solution simple: désactiver complètement le cache et ne pas ajouter d'extensions
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => false, // Désactiver le mode debug pour éviter l'ajout d'extensions
    'auto_reload' => true
]);

// NE PAS ajouter d'extensions supplémentaires ici

// Fonction pour rendre un template
function render_template($template, $data = []) {
    global $twig, $pdo;
    
    // Vérifier si l'utilisateur est connecté
    $is_logged_in = isset($_SESSION['user_id']);
    $user = null;
    
    if ($is_logged_in && isset($pdo)) {
        // Récupérer les informations de l'utilisateur
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
        }
    }
    
    // Récupérer les zones sélectionnées
    $selectedZones = ['A']; // Par défaut, zone A
    if ($is_logged_in && isset($user['preferences'])) {
        $preferences = @json_decode($user['preferences'], true);
        if (isset($preferences['zones']) && !empty($preferences['zones'])) {
            $selectedZones = $preferences['zones'];
        }
    }
    
    // Déterminer la page courante
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    
    // Fusionner les données
    $data = array_merge($data, [
        'is_logged_in' => $is_logged_in,
        'user' => $user,
        'now' => new DateTime(),
        'selectedZones' => $selectedZones,
        'STATICS_PATH' => STATICS_PATH,
        'MODULES_PATH' => MODULES_PATH,
        'current_page' => $current_page
    ]);
    
    return $twig->render($template, $data);
}
EOD;

// Chemin vers le fichier twig.php
$twigPath = __DIR__ . '/common/twig.php';

// Vérifier si le fichier existe
if (!file_exists($twigPath)) {
    die("Erreur: Le fichier twig.php n'existe pas à l'emplacement attendu.");
}

// Sauvegarder le fichier original s'il n'existe pas déjà une sauvegarde
if (!file_exists($twigPath . '.bak')) {
    if (!copy($twigPath, $twigPath . '.bak')) {
        die("Erreur: Impossible de sauvegarder le fichier original.");
    }
}

// Écrire le nouveau contenu dans le fichier
if (!file_put_contents($twigPath, $content)) {
    die("Erreur: Impossible d'écrire dans le fichier twig.php.");
}

echo "Succès: Le fichier twig.php a été modifié avec succès.\n";

// Vider le cache Twig
$cachePath = __DIR__ . '/cache/twig';
if (is_dir($cachePath)) {
    $files = glob($cachePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "Succès: Le cache Twig a été vidé.\n";
} else {
    echo "Note: Le dossier de cache Twig n'existe pas.\n";
}

// Restaurer le fichier index.php original s'il existe une sauvegarde
$indexPath = __DIR__ . '/index.php';
$indexBackupPath = $indexPath . '.bak';

if (file_exists($indexBackupPath)) {
    if (copy($indexBackupPath, $indexPath)) {
        echo "Succès: Le fichier index.php original a été restauré.\n";
    } else {
        echo "Erreur: Impossible de restaurer le fichier index.php original.\n";
    }
}

echo "Terminé. Veuillez recharger votre application.\n";