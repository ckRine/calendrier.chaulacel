<?php
// Fichier de gestion du mot de passe oublié
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si l'email est présent
if (!isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email requis']);
    exit;
}

$email = $_POST['email'];

// En mode démonstration, simuler l'envoi d'un email
// Dans un environnement de production, vous devriez :
// 1. Vérifier si l'email existe dans la base de données
// 2. Générer un token unique et l'enregistrer dans la base de données avec une date d'expiration
// 3. Envoyer un email avec un lien contenant ce token

// Simuler un délai de traitement
usleep(500000); // 500ms

echo json_encode(['success' => true, 'message' => 'Si cette adresse email est associée à un compte, vous recevrez un email avec les instructions pour réinitialiser votre mot de passe.']);
?>