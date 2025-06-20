<?php
// Fichier de gestion de la réinitialisation du mot de passe
header('Content-Type: application/json');

// Définir le chemin absolu vers le répertoire racine
$root_path = $_SERVER['DOCUMENT_ROOT'].'/calendrier.chaulacel';
require_once $root_path.'/common/conf.php';

// Vérifier si les données nécessaires sont présentes
if (!isset($_POST['token']) || !isset($_POST['password'])) {
	echo json_encode(['success' => false, 'message' => 'Token et nouveau mot de passe requis']);
	exit;
}

$token = $_POST['token'];
$password = $_POST['password'];

// En mode démonstration, simuler la réinitialisation du mot de passe
// Dans un environnement de production, vous devriez :
// 1. Vérifier si le token existe et n'est pas expiré
// 2. Récupérer l'utilisateur associé au token
// 3. Mettre à jour le mot de passe de l'utilisateur (avec hachage)
// 4. Supprimer le token ou le marquer comme utilisé

// Simuler un délai de traitement
usleep(500000); // 500ms

echo json_encode(['success' => true, 'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.']);
?>