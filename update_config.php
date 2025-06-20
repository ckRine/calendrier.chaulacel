<?php
// Script pour mettre à jour la configuration
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Mise à jour de la configuration</h1>";

$config_file = __DIR__ . '/common/conf.php';

if (file_exists($config_file)) {
    // Lire le contenu du fichier
    $content = file_get_contents($config_file);
    
    // Remplacer le nom de la base de données
    $content = str_replace(
        "\$db_name = 'chaulacel';", 
        "\$db_name = 'chronogestcal';", 
        $content
    );
    
    // Écrire le contenu mis à jour
    if (file_put_contents($config_file, $content)) {
        echo "<p style='color:green'>✓ Fichier de configuration mis à jour avec succès</p>";
    } else {
        echo "<p style='color:red'>✗ Erreur lors de la mise à jour du fichier de configuration</p>";
    }
} else {
    echo "<p style='color:red'>✗ Fichier de configuration non trouvé</p>";
}

echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>