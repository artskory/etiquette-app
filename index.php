<?php
/**
 * Application Étiquettes
 * Version 0.0.1
 */

// Définir la version de l'application
define('APP_VERSION', '0.0.3');

// Démarrer la session
session_start();

// Charger les fichiers nécessaires
require_once 'config/database.php';
require_once 'models/Reference.php';
require_once 'models/Commande.php';
require_once 'controllers/ReferenceController.php';
require_once 'controllers/CommandeController.php';

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
    
    default:
        require_once 'views/home.php';
        break;
}
