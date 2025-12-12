# Etudaviz

Etudaviz est une plateforme web dédiée à l'orientation étudiante, permettant aux utilisateurs de découvrir des formations, consulter des avis d'étudiants, et gérer leur profil. Le projet utilise des données provenant d'APIs publiques comme ONISEP pour fournir des informations actualisées sur les établissements et formations en France.

## Fonctionnalités principales

- **Recherche de formations** : Explorez des milliers de formations via l'API ONISEP, avec filtres par région, type, etc.
- **Avis et témoignages** : Consultez et déposez des avis sur les formations, avec notation par critères.
- **Gestion des comptes utilisateurs** : Inscription, connexion, activation par email, gestion du profil.
- **Orientation personnalisée** : Outils d'aide à l'orientation avec parcours guidés.
- **Métier** : Recherche de métiers via l'API ESCO, avec descriptions et compétences.
- **Cookies et confidentialité** : Gestion des préférences de cookies, politique de confidentialité.

## Technologies utilisées

- **Backend** : PHP 8+, MySQL (via PDO)
- **Frontend** : HTML5, CSS3, JavaScript
- **Bibliothèques** : PHPMailer pour les emails, cURL pour les APIs
- **APIs externes** : ONISEP (formations), ESCO (métiers), Parcoursup (données éducatives)
- **Documentation** : Générée avec Doxygen (dossiers `doc/` et `phpdocs/`)

## Installation

### Prérequis

- Serveur web (Apache/Nginx) avec PHP 8+
- MySQL 5.7+
- Composer (pour les dépendances PHP si nécessaire)
- Accès à internet pour les APIs

### Étapes

1. **Clonez le dépôt** :
   ```bash
   git clone https://github.com/votre-repo/etudaviz.git
   cd etudaviz
   ```

2. **Déployez sur un serveur** :
   - Copiez les fichiers dans le répertoire web de votre serveur.

## Utilisation

- Accédez à `index.php` pour la page d'accueil.
- Utilisez `recherche.php` pour rechercher des formations.
- Connectez-vous via `login.php` pour déposer des avis.
- Consultez la documentation dans `doc/index.html`.

## Support

Pour toute question ou problème, contactez-nous via la page [contact.php](contact.php) ou par email à contact@etudaviz.fr.

## Remerciements

Merci à CY Tech Paris Université pour le cadre de ce projet, et aux APIs ONISEP et ESCO pour les données fournies.

## Structure du projet

Le projet est organisé de manière modulaire pour faciliter la maintenance et l'extension. Voici une vue d'ensemble détaillée :

### Fichiers principaux (dans web/)

- `web/index.php` : Page d'accueil de la plateforme.
- `web/formations.php` : Page listant les formations disponibles.
- `web/fiche_formation.php` : Détails d'une formation spécifique.
- `web/metiers.php` : Recherche et affichage des métiers.
- `web/fiche_metier.php` : Détails d'un métier.
- `web/avis.php` : Gestion des avis utilisateurs.
- `web/ajouter_avis.php` : Formulaire pour ajouter un avis.
- `web/recherche.php` : Moteur de recherche global.
- `web/orientation.php` : Outils d'orientation personnalisée.
- `web/test-orientation.php` : Test d'orientation.
- `web/profil.php` : Gestion du profil utilisateur.
- `web/inscription.php` : Formulaire d'inscription.
- `web/login.php` : Page de connexion.
- `web/logout.php` : Déconnexion.
- `web/activation.php` : Activation du compte par email.
- `web/mdp-oublie.php` : Récupération de mot de passe.
- `web/reset-mdp.php` : Réinitialisation du mot de passe.
- `web/contact.php` : Page de contact.
- `web/apropos.php` : À propos du projet.
- `web/confidentialite.php` : Politique de confidentialité.
- `web/cookies.php` : Gestion des cookies.
- `web/mentions-legales.php` : Mentions légales.
- `web/private.php` : Page privée (probablement pour les utilisateurs connectés).
- `web/toggle_favori.php` : Gestion des favoris.
- `web/get-departements.php` : API pour récupérer les départements.
- `web/load-etablissements.php` : Chargement des établissements.
- `web/load-metiers.php` : Chargement des métiers.
- `web/functions.inc.php` : Fonctions utilitaires communes.
- `web/robots.txt` : Fichier pour les moteurs de recherche.
- `web/auth/sitemap.xml` : Plan du site.

### Dossiers

- `web/auth/` : Scripts liés à l'authentification.
- `web/css/` : Feuilles de style CSS.
  - `web/css/style.css` : Styles principaux.
  - `web/css/style_nuit.css` : Mode nuit.
- `web/data/` : Données statiques.
  - `web/data/list-mots-interdits.txt` : Liste de mots interdits.
  - `web/data/csv/` : Fichiers CSV de données.
- `web/images/` : Ressources images.
  - `web/images/avatars/` : Avatars des utilisateurs.
- `web/include/` : Fichiers inclus (comme `web/functions.inc.php`).
- `web/js/` : Scripts JavaScript.
- `web/phpmailer/` : Bibliothèque PHPMailer pour l'envoi d'emails.
- `doc/` : Documentation générée avec Doxygen.

Cette structure permet une séparation claire des préoccupations : logique métier, présentation, données et documentation.

## Contribution

- Forkez le projet et créez une branche pour vos modifications.
- Respectez les standards de code PHP (PSR-12).
- Testez les changements sur un environnement local.

## Auteurs

- Dihya Mokri
- Loris Beguin
- Léa Bonacorsi
- Mathis Albrun

Projet réalisé dans le cadre d'un cours à CY Tech Paris Université.