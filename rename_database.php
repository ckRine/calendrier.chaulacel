<?php
// Script pour renommer la base de données
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Renommage de la base de données</h1>";

// Configuration de la base de données
$db_host = 'localhost';
$old_db_name = 'chaulacel';
$new_db_name = 'chronogestcal';
$db_user = 'root';
$db_pass = '';

try {
    // Se connecter à MySQL sans spécifier de base de données
    echo "<p>Connexion à MySQL...</p>";
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Connexion réussie</p>";
    
    // Vérifier si la nouvelle base de données existe déjà
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$new_db_name'");
    if ($stmt->rowCount() > 0) {
        echo "<p>La base de données '$new_db_name' existe déjà, suppression...</p>";
        $pdo->exec("DROP DATABASE IF EXISTS `$new_db_name`");
        echo "<p style='color:green'>✓ Base de données '$new_db_name' supprimée</p>";
    }
    
    // Vérifier si l'ancienne base de données existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$old_db_name'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color:orange'>⚠ La base de données '$old_db_name' n'existe pas, création d'une nouvelle base de données...</p>";
        $pdo->exec("CREATE DATABASE `$new_db_name`");
        echo "<p style='color:green'>✓ Base de données '$new_db_name' créée</p>";
    } else {
        // Créer la nouvelle base de données
        echo "<p>Création de la base de données '$new_db_name'...</p>";
        $pdo->exec("CREATE DATABASE `$new_db_name`");
        echo "<p style='color:green'>✓ Base de données '$new_db_name' créée</p>";
        
        // Obtenir la liste des tables de l'ancienne base de données
        $stmt = $pdo->query("SHOW TABLES FROM `$old_db_name`");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p>Copie des tables et des données...</p>";
            
            // Pour chaque table, copier la structure et les données
            foreach ($tables as $table) {
                echo "<p>Copie de la table '$table'...</p>";
                
                // Créer la structure de la table dans la nouvelle base de données
                $pdo->exec("CREATE TABLE `$new_db_name`.`$table` LIKE `$old_db_name`.`$table`");
                
                // Copier les données
                $pdo->exec("INSERT INTO `$new_db_name`.`$table` SELECT * FROM `$old_db_name`.`$table`");
                
                echo "<p style='color:green'>✓ Table '$table' copiée</p>";
            }
            
            echo "<p style='color:green'>✓ Toutes les tables et données ont été copiées</p>";
        } else {
            echo "<p style='color:orange'>⚠ Aucune table trouvée dans la base de données '$old_db_name'</p>";
        }
    }
    
    echo "<h2>La base de données a été renommée avec succès!</h2>";
    echo "<p>N'oubliez pas de mettre à jour le fichier de configuration (conf.php) pour utiliser la nouvelle base de données.</p>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur: " . $e->getMessage() . "</p>";
}
?>