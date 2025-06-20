<?php
/**
 * Script pour corriger le problème de json_encode dans Twig
 * À exécuter sur le serveur de production
 */

// Chemin vers le fichier ExtensionSet.php
$extensionSetPath = __DIR__ . '/vendor/twig/twig/src/ExtensionSet.php';
$newFilePath = __DIR__ . '/vendor/twig/twig/src/ExtensionSet.php.new';

// Vérifier si les fichiers existent
if (!file_exists($extensionSetPath)) {
    die("Erreur: Le fichier ExtensionSet.php n'existe pas à l'emplacement attendu.");
}

if (!file_exists($newFilePath)) {
    die("Erreur: Le fichier de remplacement ExtensionSet.php.new n'existe pas.");
}

// Sauvegarder le fichier original
if (!copy($extensionSetPath, $extensionSetPath . '.bak')) {
    die("Erreur: Impossible de sauvegarder le fichier original.");
}

// Remplacer le fichier
if (!copy($newFilePath, $extensionSetPath)) {
    die("Erreur: Impossible de remplacer le fichier ExtensionSet.php.");
}

echo "Succès: Le fichier ExtensionSet.php a été remplacé avec succès.\n";

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