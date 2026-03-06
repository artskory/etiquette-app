<?php
/**
 * Application Étiquettes
 * Version 2.0.0 - Réécriture d'URL propre
 */

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

define('APP_VERSION', '2.0.0');

session_start();

// Déterminer le chemin de base de l'application (fonctionne en sous-dossier)
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

require_once 'lib/CsrfToken.php';
require_once 'lib/Validator.php';
require_once 'config/database.php';
require_once 'models/Reference.php';
require_once 'models/Commande.php';
require_once 'models/CommandeLatitude.php';
require_once 'models/ArticleLatitude.php';
require_once 'controllers/ReferenceController.php';
require_once 'controllers/CommandeController.php';
require_once 'controllers/LatitudeController.php';
require_once 'controllers/ArticleLatitudeController.php';

// ============================================
// HELPER : génération d'URL propres
// ============================================

/**
 * Génère une URL propre.
 * Exemple : url('sartorius', 'commande', 5, 'editer') → /sartorius/commande/5/editer
 */
function url(string ...$segments): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path = '/' . implode('/', array_filter(array_map('strval', $segments)));
    return $base . $path;
}

// ============================================
// ROUTEUR : analyse du chemin de l'URL
// ============================================

// Récupérer le chemin de la requête sans le chemin de base du script
$requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
$scriptBase  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// Supprimer le chemin de base et les paramètres GET
$path = parse_url($requestUri, PHP_URL_PATH);
if ($scriptBase !== '' && strpos($path, $scriptBase) === 0) {
    $path = substr($path, strlen($scriptBase));
}
$path = '/' . trim($path, '/');

// Découper le chemin en segments
$segments = array_values(array_filter(explode('/', $path)));

// Paramètre de pagination (?p=X)
$pageNum = isset($_GET['p']) ? (int)$_GET['p'] : 1;

// ============================================
// ROUTING
// ============================================

// Segment 0 = module (sartorius | latitude)
// Segment 1 = sous-ressource (commande | reference | article)
// Segment 2 = id
// Segment 3 = action

$module = $segments[0] ?? 'home';

switch ($module) {

    // ------------------------------------------
    // ACCUEIL
    // ------------------------------------------
    case '':
    case 'home':
        require_once 'views/home.php';
        break;

    // ------------------------------------------
    // MODULE SARTORIUS
    // ------------------------------------------
    case 'sartorius':
        $sub    = $segments[1] ?? null;
        $id     = isset($segments[2]) && is_numeric($segments[2]) ? (int)$segments[2] : null;
        $action = $id !== null ? ($segments[3] ?? null) : ($segments[2] ?? null);

        // /sartorius/reference/...
        if ($sub === 'reference') {
            $controller = new ReferenceController();
            switch ($action) {
                case 'ajout':    $controller->ajout();    break;
                case 'creer':    $controller->creer();    break;
                case 'editer':   $controller->edition();  break;
                case 'modifier': $controller->modifier(); break;
                case 'supprimer':
                    if ($id) {
                        $controller->supprimer();
                    } else {
                        $controller->supprimerSelection();
                    }
                    break;
                default:         $controller->ajout();    break;
            }
        }
        // /sartorius/commande/...  ou  /sartorius/{action}
        else {
            $controller = new CommandeController();
            // Si on a un id dans l'URL (/sartorius/commande/{id}/{action})
            if ($sub === 'commande' && $id) {
                // Passer l'id via $_GET pour rétrocompatibilité avec les controllers
                $_GET['id'] = $id;
                switch ($action) {
                    case 'editer':     $controller->edition();   break;
                    case 'modifier':   $controller->modifier();  break;
                    case 'supprimer':  $controller->supprimer(); break;
                    case 'telecharger': $controller->telecharger(); break;
                    default:           $controller->liste();     break;
                }
            } else {
                // /sartorius, /sartorius/nouvelle, /sartorius/creer, /sartorius/supprimer-selection
                switch ($sub) {
                    case null:
                    case 'liste':              $controller->liste();              break;
                    case 'nouvelle':           $controller->nouvelle();           break;
                    case 'creer':              $controller->creer();              break;
                    case 'supprimer-selection': $controller->supprimerSelection(); break;
                    default:                   $controller->liste();              break;
                }
            }
        }
        break;

    // ------------------------------------------
    // MODULE LATITUDE
    // ------------------------------------------
    case 'latitude':
        $sub    = $segments[1] ?? null;
        $id     = isset($segments[2]) && is_numeric($segments[2]) ? (int)$segments[2] : null;
        $action = $id !== null ? ($segments[3] ?? null) : ($segments[2] ?? null);

        // /latitude/article/...
        if ($sub === 'article') {
            $controller = new ArticleLatitudeController();
            switch ($action) {
                case 'nouveau':  $controller->nouveau();  break;
                case 'creer':    $controller->creer();    break;
                case 'editer':   $controller->edition();  break;
                case 'modifier': $controller->modifier(); break;
                case 'supprimer':
                    if ($id) {
                        $controller->supprimer();
                    } else {
                        $controller->supprimerSelection();
                    }
                    break;
                default:         $controller->nouveau();  break;
            }
        }
        // /latitude/commande/... ou /latitude/{action}
        else {
            $controller = new LatitudeController();
            if ($sub === 'commande' && $id) {
                $_GET['id'] = $id;
                switch ($action) {
                    case 'editer':      $controller->edition();    break;
                    case 'modifier':    $controller->modifier();   break;
                    case 'supprimer':   $controller->supprimer();  break;
                    case 'telecharger': $controller->telecharger(); break;
                    default:            $controller->liste();      break;
                }
            } else {
                switch ($sub) {
                    case null:
                    case 'liste':               $controller->liste();              break;
                    case 'nouvelle':            $controller->nouvelle();           break;
                    case 'creer':               $controller->creer();              break;
                    case 'supprimer-selection': $controller->supprimerSelection(); break;
                    default:                    $controller->liste();              break;
                }
            }
        }
        break;

    // ------------------------------------------
    // PAGE 404
    // ------------------------------------------
    default:
        http_response_code(404);
        require_once 'views/home.php';
        break;
}
