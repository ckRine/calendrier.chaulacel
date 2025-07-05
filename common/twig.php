<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration de Twig avec cache désactivé
$loader = new \Twig\Loader\FilesystemLoader(TEMPLATES_PATH);

// Solution simple: désactiver complètement le cache et ne pas ajouter d'extensions
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => false, // Désactiver le mode debug pour éviter l'ajout d'extensions
    'auto_reload' => true
]);

// NE PAS ajouter d'extensions supplémentaires ici

// Fonction pour rendre un template
function render_template($template, $data = []) {
    global $twig, $pdo;
    
    // Vérifier si l'utilisateur est connecté
    $is_logged_in = isset($_SESSION['user_id']);
    $user = null;
    
    if ($is_logged_in && isset($pdo)) {
        // Récupérer les informations de l'utilisateur
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
        }
    }
    
    // Récupérer les zones sélectionnées
    $selectedZones = ['A']; // Par défaut, zone A
    if ($is_logged_in && isset($user['preferences'])) {
        $preferences = @json_decode($user['preferences'], true);
        if (isset($preferences['zones']) && !empty($preferences['zones'])) {
            $selectedZones = $preferences['zones'];
        }
    }
    
    // Déterminer la page courante
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    
    // Fusionner les données
    $data = array_merge($data, [
        'is_logged_in' => $is_logged_in,
        'user' => $user,
        'now' => new DateTime(),
        'selectedZones' => $selectedZones,
        'STATICS_PATH' => STATICS_PATH,
        'MODULES_PATH' => MODULES_PATH,
        'current_page' => $current_page
    ]);
    
    return $twig->render($template, $data);
}