<?php
/**
 * Script pour désactiver le cache Twig
 * À exécuter sur le serveur de production
 */

// Chemin vers le fichier twig.php
$twigPath = __DIR__ . '/common/twig.php';

// Vérifier si le fichier existe
if (!file_exists($twigPath)) {
    die("Erreur: Le fichier twig.php n'existe pas à l'emplacement attendu.");
}

// Sauvegarder le fichier original
if (!copy($twigPath, $twigPath . '.bak')) {
    die("Erreur: Impossible de sauvegarder le fichier original.");
}

// Contenu du nouveau fichier
$content = <<<'EOD'
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration de Twig - Désactivation du cache pour éviter les problèmes de json_encode
$loader = new \Twig\Loader\FilesystemLoader(ROOT_PATH . '/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // Désactiver le cache pour éviter les problèmes
    'debug' => true,
    'auto_reload' => true
]);

// Ajouter l'extension Debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

// Fonction pour rendre un template
function render_template($template, $data = []) {
    global $twig, $pdo;
    
    // Vérifier si l'utilisateur est connecté
    $is_logged_in = isset($_SESSION['user_id']);
    $user = null;
    
    if ($is_logged_in) {
        // Récupérer les informations de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les zones sélectionnées
    $selectedZones = ['A']; // Par défaut, zone A
    if ($is_logged_in && isset($user['preferences'])) {
        $preferences = json_decode($user['preferences'], true);
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

// Écrire le nouveau contenu dans le fichier
if (!file_put_contents($twigPath, $content)) {
    die("Erreur: Impossible d'écrire dans le fichier twig.php.");
}

echo "Succès: Le cache Twig a été désactivé avec succès.\n";

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

echo "Terminé. Veuillez recharger votre application.\n";