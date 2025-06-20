<?php
/**
 * Script d'installation pour cPanel
 * À exécuter après le déploiement sur cPanel
 */

echo "<h1>Configuration pour cPanel</h1>";

// 1. Vérifier les permissions des dossiers
echo "<h2>1. Vérification des permissions</h2>";
$directories = [
    './cache' => 0755,
    './cache/twig' => 0755,
    './vendor' => 0755,
    './common' => 0755
];

foreach ($directories as $dir => $perm) {
    if (!file_exists($dir)) {
        echo "<p>Création du dossier $dir... ";
        if (mkdir($dir, $perm, true)) {
            echo "OK</p>";
        } else {
            echo "ÉCHEC</p>";
        }
    } else {
        echo "<p>Le dossier $dir existe déjà.</p>";
    }
    
    echo "<p>Modification des permissions de $dir... ";
    if (chmod($dir, $perm)) {
        echo "OK</p>";
    } else {
        echo "ÉCHEC</p>";
    }
}

// 2. Modifier le fichier twig.php
echo "<h2>2. Configuration de Twig</h2>";
$twigPath = './common/twig.php';
$twigContent = <<<'EOD'
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

if (file_exists($twigPath)) {
    // Sauvegarder l'ancien fichier
    if (!file_exists($twigPath . '.bak')) {
        copy($twigPath, $twigPath . '.bak');
    }
    
    // Écrire le nouveau contenu
    if (file_put_contents($twigPath, $twigContent)) {
        echo "<p>Le fichier twig.php a été mis à jour avec succès.</p>";
    } else {
        echo "<p>Impossible de mettre à jour le fichier twig.php.</p>";
    }
} else {
    echo "<p>Le fichier twig.php n'existe pas.</p>";
}

// 3. Vider le cache Twig
echo "<h2>3. Vidage du cache Twig</h2>";
$cachePath = './cache/twig';
if (is_dir($cachePath)) {
    $files = glob($cachePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                echo "<p>Suppression du fichier $file... OK</p>";
            } else {
                echo "<p>Suppression du fichier $file... ÉCHEC</p>";
            }
        }
    }
} else {
    echo "<p>Le dossier de cache Twig n'existe pas.</p>";
}

echo "<h2>Configuration terminée</h2>";
echo "<p>Veuillez <a href='./'>recharger le site</a> pour vérifier que tout fonctionne correctement.</p>";