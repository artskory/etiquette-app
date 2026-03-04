<?php
/**
 * Application Étiquettes
 * Version 1.0.11 - Protection CSRF ajoutée
 */

// Désactiver l'affichage des erreurs en production
// Décommentez les 2 lignes suivantes pour débugger
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Définir la version de l'application
define('APP_VERSION', '1.0.11');

// Démarrer la session
session_start();

// Charger la classe CSRF en premier
require_once 'lib/CsrfToken.php';

// Charger les fichiers nécessaires
require_once 'config/database.php';
require_once 'models/Reference.php';
require_once 'models/Commande.php';
require_once 'models/CommandeLatitude.php';
require_once 'models/ArticleLatitude.php';
require_once 'controllers/ReferenceController.php';
require_once 'controllers/CommandeController.php';
require_once 'controllers/LatitudeController.php';
require_once 'controllers/ArticleLatitudeController.php';

// Récupérer la page demandée
$page = $_GET['page'] ?? 'home';

// Router les pages
switch($page) {
    case 'home':
        require_once 'views/home.php';
        break;
    
    case 'sartorius':
        $controller = new CommandeController();
        $controller->liste();
        break;
    
    case 'ajout-reference':
        $controller = new ReferenceController();
        $controller->ajout();
        break;
    
    case 'creer-reference':
        $controller = new ReferenceController();
        $controller->creer();
        break;
    
    case 'editer-reference':
        $controller = new ReferenceController();
        $controller->edition();
        break;
    
    case 'modifier-reference':
        $controller = new ReferenceController();
        $controller->modifier();
        break;
    
    case 'supprimer-reference':
        $controller = new ReferenceController();
        $controller->supprimer();
        break;
    
    case 'supprimer-selection-references':
        $controller = new ReferenceController();
        $controller->supprimerSelection();
        break;
    
    case 'nouvelle-commande':
        $controller = new CommandeController();
        $controller->nouvelle();
        break;
    
    case 'creer-commande':
        $controller = new CommandeController();
        $controller->creer();
        break;
    
    case 'editer-commande':
        $controller = new CommandeController();
        $controller->edition();
        break;
    
    case 'modifier-commande':
        $controller = new CommandeController();
        $controller->modifier();
        break;
    
    case 'supprimer-commande':
        $controller = new CommandeController();
        $controller->supprimer();
        break;
    
    case 'supprimer-selection-commandes':
        $controller = new CommandeController();
        $controller->supprimerSelection();
        break;
    
    case 'latitude':
        $controller = new LatitudeController();
        $controller->liste();
        break;
    
    case 'nouveau-article-latitude':
        $controller = new ArticleLatitudeController();
        $controller->nouveau();
        break;
    
    case 'creer-article-latitude':
        $controller = new ArticleLatitudeController();
        $controller->creer();
        break;
    
    case 'editer-article-latitude':
        $controller = new ArticleLatitudeController();
        $controller->edition();
        break;
    
    case 'modifier-article-latitude':
        $controller = new ArticleLatitudeController();
        $controller->modifier();
        break;
    
    case 'supprimer-article-latitude':
        $controller = new ArticleLatitudeController();
        $controller->supprimer();
        break;
    
    case 'supprimer-selection-articles-latitude':
        $controller = new ArticleLatitudeController();
        $controller->supprimerSelection();
        break;
    
    case 'nouvelle-commande-latitude':
        $controller = new LatitudeController();
        $controller->nouvelle();
        break;
    
    case 'creer-commande-latitude':
        $controller = new LatitudeController();
        $controller->creer();
        break;
    
    case 'editer-commande-latitude':
        $controller = new LatitudeController();
        $controller->edition();
        break;
    
    case 'modifier-commande-latitude':
        $controller = new LatitudeController();
        $controller->modifier();
        break;
    
    case 'supprimer-commande-latitude':
        $controller = new LatitudeController();
        $controller->supprimer();
        break;
    
    case 'supprimer-selection-commandes-latitude':
        $controller = new LatitudeController();
        $controller->supprimerSelection();
        break;
    
    default:
        require_once 'views/home.php';
        break;
}
