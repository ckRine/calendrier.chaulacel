<?php
require_once __DIR__ . '/common/conf.php';
require_once __DIR__ . '/common/twig.php';

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
$user = null;

if ($is_logged_in) {
    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer les zones sélectionnées
$selectedZones = ['A']; // Par défaut, zone A
if ($is_logged_in && isset($user['preferences'])) {
    $preferences = json_decode($user['preferences'], true);
    if (isset($preferences['zones']) && !empty($preferences['zones'])) {
        $selectedZones = $preferences['zones'];
    }
}

// Rendre le template
echo $twig->render('agenda.twig', [
    'is_logged_in' => $is_logged_in,
    'user' => $user,
    'now' => new DateTime(),
    'selectedZones' => $selectedZones,
    'STATICS_PATH' => STATICS_PATH,
    'MODULES_PATH' => MODULES_PATH,
    'current_page' => 'agenda'
]);