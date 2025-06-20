<?php
/**
 * Script pour restaurer le site après la maintenance d'urgence
 * À exécuter sur le serveur une fois les problèmes résolus
 */

// Chemin vers le fichier index.php
$indexPath = __DIR__ . '/index.php';
$backupPath = $indexPath . '.bak';

// Vérifier si le fichier de sauvegarde existe
if (!file_exists($backupPath)) {
    die("Erreur: Le fichier de sauvegarde index.php.bak n'existe pas.");
}

// Restaurer le fichier original
if (!copy($backupPath, $indexPath)) {
    die("Erreur: Impossible de restaurer le fichier original.");
}

echo "Succès: Le fichier index.php original a été restauré.\n";
echo "Terminé. Veuillez recharger votre application.\n";