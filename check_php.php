<?php
/**
 * Script de vérification de la configuration PHP
 * Permet de diagnostiquer les problèmes liés aux extensions PHP
 */

// Désactiver l'affichage des erreurs pour ce script
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Informations de base sur PHP
echo "<h1>Informations PHP</h1>";
echo "<p>Version PHP: " . phpversion() . "</p>";
echo "<p>Fichier php.ini chargé: " . php_ini_loaded_file() . "</p>";

// Vérifier si l'extension JSON est activée
echo "<h2>Extension JSON</h2>";
if (function_exists('json_encode')) {
    echo "<p style='color:green'>✓ La fonction json_encode() est disponible.</p>";
} else {
    echo "<p style='color:red'>✗ La fonction json_encode() n'est PAS disponible.</p>";
}

if (extension_loaded('json')) {
    echo "<p style='color:green'>✓ L'extension JSON est chargée.</p>";
} else {
    echo "<p style='color:red'>✗ L'extension JSON n'est PAS chargée.</p>";
}

// Vérifier les extensions chargées
echo "<h2>Extensions PHP chargées</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Vérifier les fonctions disponibles dans l'espace de noms global
echo "<h2>Fonctions disponibles dans l'espace de noms global</h2>";
echo "<p>json_encode existe: " . (function_exists('json_encode') ? 'Oui' : 'Non') . "</p>";
echo "<p>serialize existe: " . (function_exists('serialize') ? 'Oui' : 'Non') . "</p>";

// Vérifier les fonctions dans l'espace de noms Twig
echo "<h2>Fonctions dans l'espace de noms Twig</h2>";
echo "<p>Cette vérification peut provoquer une erreur si la fonction n'existe pas.</p>";

try {
    // Tenter d'appeler la fonction dans l'espace de noms Twig
    $result = @call_user_func('Twig\json_encode', ['test' => 'value']);
    echo "<p style='color:green'>✓ La fonction Twig\\json_encode() est disponible.</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>✗ La fonction Twig\\json_encode() n'est PAS disponible.</p>";
    echo "<p>Erreur: " . $e->getMessage() . "</p>";
}

// Vérifier les paramètres PHP importants
echo "<h2>Paramètres PHP importants</h2>";
echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";
echo "<p>max_execution_time: " . ini_get('max_execution_time') . "</p>";
echo "<p>display_errors: " . ini_get('display_errors') . "</p>";
echo "<p>error_reporting: " . ini_get('error_reporting') . "</p>";

// Vérifier les droits d'accès aux dossiers importants
echo "<h2>Droits d'accès aux dossiers</h2>";
$directories = [
    '.' => 'Répertoire racine',
    './cache' => 'Dossier de cache',
    './cache/twig' => 'Cache Twig',
    './vendor' => 'Dossier vendor',
    './vendor/twig' => 'Dossier Twig',
    './common' => 'Dossier common'
];

foreach ($directories as $dir => $description) {
    if (file_exists($dir)) {
        echo "<p>$description ($dir): ";
        echo "Existe, ";
        echo "Permissions: " . substr(sprintf('%o', fileperms($dir)), -4);
        echo is_writable($dir) ? ", Accessible en écriture" : ", NON accessible en écriture";
        echo "</p>";
    } else {
        echo "<p>$description ($dir): N'existe pas</p>";
    }
}

echo "<h2>Fin du rapport</h2>";