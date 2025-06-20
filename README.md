# ChronoGestCal - Calendrier

ChronoGestCal est une application web de calendrier qui permet de visualiser les jours fériés et les vacances scolaires en France par zones (A, B, C).

## Fonctionnalités

- Affichage des mois avec navigation fluide
- Mise en évidence des jours fériés
- Affichage des vacances scolaires par zones (A, B, C)
- Système d'authentification (connexion/inscription)
- Sauvegarde des préférences utilisateur
- Intégration avec Google Calendar
- Navigation rapide (aujourd'hui, date spécifique)
- Affichage des détails d'une journée
- Impression du calendrier avec différentes options
- Exportation au format iCalendar (.ics)
- Vue agenda hebdomadaire

## Structure du projet

- **common/** - Configuration et fonctions communes
  - `conf.php` - Configuration principale (BDD, chemins, etc.)
  - `twig.php` - Configuration de Twig
- **modules/** - Scripts PHP pour les fonctionnalités
  - Scripts d'authentification (login, register, etc.)
  - Scripts pour Google Calendar
  - Scripts de gestion des préférences
  - Scripts d'exportation
- **pages/** - Pages principales de l'application (ancienne structure)
- **statics/** - Ressources statiques
  - `css/` - Feuilles de style
  - `js/` - Scripts JavaScript
  - `img/` - Images
- **templates/** - Templates Twig
  - `base.twig` - Template de base
  - `calendrier.twig` - Vue calendrier
  - `agenda.twig` - Vue agenda
  - `mon-compte.twig` - Gestion du compte utilisateur
- **sql/** - Scripts SQL pour la création des tables
- **vendor/** - Dépendances (Composer)
- **cache/** - Cache pour Twig

## Organisation des fichiers CSS

Les styles CSS du projet sont organisés de manière modulaire pour faciliter la maintenance :

### Fichier principal
- `main.css` : Point d'entrée qui importe tous les autres fichiers CSS

### Variables et utilitaires
- `variables.css` : Définition des variables CSS globales (couleurs, espacements, etc.)
- `utilities.css` : Classes utilitaires réutilisables

### Composants principaux
- `layout.css` : Structure générale de la page
- `day-popup.css` : Styles pour la popup des jours
- `forms.css` : Styles communs pour les formulaires
- `modals.css` : Styles communs pour les fenêtres modales

### Composants d'authentification et utilisateur
- `auth.css` : Styles pour l'authentification
- `account.css` : Styles pour la page de compte utilisateur
- `dropdown.css` : Styles pour les menus déroulants

### Fonctionnalités spécifiques
- `agenda.css` : Styles pour la vue agenda
- `google-calendar.css` : Styles pour l'intégration Google Calendar
- `export-dialog.css` : Styles pour la boîte de dialogue d'exportation
- `print-dialog.css` : Styles pour la boîte de dialogue d'impression
- `logo.css` : Styles pour le logo
- `print.css` : Styles pour l'impression

## Comment utiliser les CSS

Pour ajouter de nouveaux styles :

1. Identifiez le fichier CSS approprié pour votre fonctionnalité
2. Ajoutez vos styles dans ce fichier
3. Si vous créez une nouvelle fonctionnalité, créez un nouveau fichier CSS et importez-le dans `main.css`

## Bonnes pratiques CSS

- Utilisez les variables CSS définies dans `variables.css`
- Préférez les classes utilitaires de `utilities.css` pour les styles communs
- Suivez la convention de nommage BEM (Block, Element, Modifier) pour les classes CSS
- Commentez les sections importantes de votre code CSS

## Installation

1. Cloner le dépôt
2. Configurer un serveur web avec PHP et MySQL
3. Installer les dépendances avec Composer : `composer install`
4. Créer une base de données nommée `chronogestcal`
5. Exécuter les scripts SQL dans le dossier `sql/`
6. Configurer les paramètres de connexion dans `common/conf.php`

## Utilisation

- Accéder à l'application via un navigateur
- Créer un compte pour sauvegarder vos préférences
- Sélectionner les zones scolaires à afficher
- Naviguer dans le calendrier avec les boutons ou les flèches du clavier
- Cliquer sur un jour pour voir les détails
- Utiliser les fonctions d'impression ou d'exportation selon vos besoins

## Intégration Google Calendar

Pour utiliser l'intégration avec Google Calendar :
1. Se connecter à l'application
2. Accéder à la page "Mon compte"
3. Cliquer sur "Connecter Google Calendar"
4. Autoriser l'accès à votre calendrier Google

## Technologies utilisées

- PHP
- JavaScript (vanilla)
- MySQL
- Twig (moteur de templates)
- API Calendrier Gouv.fr (jours fériés)
- API Education.gouv.fr (vacances scolaires)
- API Google Calendar

## Développement

Le projet est structuré de manière modulaire pour faciliter la maintenance et l'extension des fonctionnalités.