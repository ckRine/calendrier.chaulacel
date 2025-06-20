# Changelog

## 2024-06-17 - Correction des problèmes de compatibilité PHP

### Problèmes résolus
- Correction de l'erreur `Call to undefined function Twig\json_encode()`
- Correction de l'erreur `Unable to register extension "Twig\Extension\EscaperExtension"`

### Solutions appliquées
- Désactivation du cache Twig pour éviter les problèmes de sérialisation
- Mise en place d'une solution d'urgence temporaire (page de maintenance)

### Notes techniques
- Le serveur de production utilise PHP 7.2.34 alors que les dépendances requièrent PHP 7.4.0+
- La fonction json_encode() n'était pas disponible dans l'espace de noms Twig
- Les extensions Twig étaient déjà initialisées lors de l'ajout de nouvelles extensions