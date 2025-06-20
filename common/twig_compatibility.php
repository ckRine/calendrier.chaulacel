<?php
/**
 * Fichier de compatibilité pour Twig avec PHP 7.2
 * Définit les fonctions manquantes dans l'espace de noms Twig
 */

namespace Twig {
    if (!function_exists('Twig\json_encode')) {
        function json_encode($value, $options = 0, $depth = 512) {
            return \json_encode($value, $options, $depth);
        }
    }
}