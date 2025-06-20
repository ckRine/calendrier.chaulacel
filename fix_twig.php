<?php
/**
 * Script pour corriger le problème de json_encode dans Twig
 * À exécuter sur le serveur de production
 */

// Chemin vers le fichier ExtensionSet.php
$extensionSetPath = __DIR__ . '/vendor/twig/twig/src/ExtensionSet.php';

// Vérifier si le fichier existe
if (!file_exists($extensionSetPath)) {
    die("Erreur: Le fichier ExtensionSet.php n'existe pas à l'emplacement attendu.");
}

// Sauvegarder le fichier original
copy($extensionSetPath, $extensionSetPath . '.bak');

// Contenu du fichier modifié
$content = file_get_contents($extensionSetPath);

// Remplacer la méthode getSignature
$pattern = '/public function getSignature\(\)\s*\{.*?return json_encode\(\[(.*?)\]\);.*?\}/s';
$replacement = 'public function getSignature()
    {
        // Utiliser serialize au lieu de json_encode
        return serialize([${1}]);
    }';

$newContent = preg_replace($pattern, $replacement, $content);

// Vérifier si le remplacement a fonctionné
if ($newContent === $content) {
    die("Erreur: Impossible de trouver et remplacer la méthode getSignature.");
}

// Écrire le nouveau contenu dans le fichier
if (file_put_contents($extensionSetPath, $newContent)) {
    echo "Succès: Le fichier ExtensionSet.php a été modifié avec succès.\n";
} else {
    echo "Erreur: Impossible d'écrire dans le fichier ExtensionSet.php.\n";
}

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