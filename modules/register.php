<?php
// Fichier d'inscription utilisateur
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si les données d'inscription sont présentes
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

// Mode de démonstration - accepter n'importe quel email/mot de passe
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = $email;

// Simuler un délai de traitement
usleep(500000); // 500ms

echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
?>