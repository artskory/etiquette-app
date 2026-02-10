<?php
/**
 * Application Étiquettes
 * Version 0.0.1
 */

// Définir la version de l'application
define('APP_VERSION', '0.2.4');

// Démarrer la session
session_start();

// Charger les fichiers nécessaires
require_once 'config/database.php';
require_once 'models/Reference.php';
require_once 'models/Commande.php';
require_once 'models/CommandeLatitude.php';
require_once 'controllers/ReferenceController.php';
require_once 'controllers/CommandeController.php';
require_once 'controllers/LatitudeController.php';

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
    
    case 'nouvelle-commande':
        $controller = new CommandeController();
        $controller->nouvelle();
        break;
    
    case 'creer-commande':
        $controller = new CommandeController();
        $controller->creer();
        break;
    
    case 'edition-commande':
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
    
    case 'telecharger-pdf':
        $controller = new CommandeController();
        $controller->telecharger();
        break;
    
    case 'supprimer-tout':
        $controller = new CommandeController();
        $controller->supprimerTout();
        break;
    
    // Routes Latitude
    case 'latitude':
        $controller = new LatitudeController();
        $controller->liste();
        break;
    
    case 'latitude-nouvelle':
        $controller = new LatitudeController();
        $controller->nouvelle();
        break;
    
    case 'latitude-edition':
        $controller = new LatitudeController();
        $controller->edition();
        break;
    
    case 'latitude-creer':
        $controller = new LatitudeController();
        $controller->creer();
        break;
    
    case 'latitude-modifier':
        $controller = new LatitudeController();
        $controller->modifier();
        break;
    
    case 'latitude-supprimer':
        $controller = new LatitudeController();
        $controller->supprimer();
        break;
    
    case 'latitude-telecharger':
        $controller = new LatitudeController();
        $controller->telecharger();
        break;
    
    case 'latitude-supprimer-tout':
        $controller = new LatitudeController();
        $controller->supprimerTout();
        break;
    
    default:
        require_once 'views/home.php';
        break;
}
