<?php
// Fichier de déconnexion utilisateur
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Détruire la session
session_unset();
session_destroy();

// Supprimer le cookie de connexion automatique s'il existe
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
?>