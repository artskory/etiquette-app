<?php
/**
 * Contrôleur pour les articles Latitude
 * Version 1.0.11 - Protection CSRF + Validation entrées
 */
class ArticleLatitudeController {
    private $db;
    private $article;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->article = new ArticleLatitude($this->db);
    }

    /**
     * Afficher la page d'ajout d'article
     */
    public function nouveau() {
        $articles = $this->article->readAll();
        require_once 'views/latitude/articles_latitude/nouveau_article_latitude.php';
    }

    /**
     * Créer un nouvel article
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Démarrer la session si pas déjà fait
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Récupérer la provenance pour redirection
            $from     = $_POST['from'] ?? '';
            $suffix   = (!empty($from)) ? '?from=' . urlencode($from) : '';
            $ajoutUrl = BASE_URL . '/latitude/article/nouveau' . $suffix;

            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/latitude?error=csrf_invalid");
                exit;
            }
            
            // Valider le nom de l'article
            $nom = Validator::text($_POST['nom'] ?? '');
            
            if ($nom === false || Validator::isEmpty($_POST['nom'] ?? '')) {
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=invalid_name");
                exit;
            }
            
            $this->article->nom = $nom;

            // Vérifier si l'article existe déjà
            if($this->article->exists()) {
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=article_exists");
                exit();
            }

            try {
                if($this->article->create()) {
                    // Ne PAS stocker en session pour vider les champs
                    unset($_SESSION['form_data']);
                    unset($_SESSION['ref_from']);
                    header("Location: " . $ajoutUrl . "&success=article_created");
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: " . $ajoutUrl . "&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création article: " . $e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=create_failed");
                exit();
            }
        }
    }

    /**
     * Afficher la page d'édition
     */
    public function edition() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/latitude/article/nouveau?error=invalid_id");
            exit();
        }
        
        $this->article->id = $id;
        $articleData = $this->article->readOne();
        
        if($articleData) {
            require_once 'views/latitude/articles_latitude/edition_article_latitude.php';
        } else {
            header("Location: " . BASE_URL . "/latitude/article/nouveau?error=not_found");
            exit();
        }
    }

    /**
     * Modifier un article
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/latitude?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID
            $id = Validator::id($_POST['id'] ?? 0);
            if ($id === false) {
                header("Location: " . BASE_URL . "/latitude/article/nouveau?error=invalid_id");
                exit;
            }
            
            // Valider le nom
            $nom = Validator::text($_POST['nom'] ?? '');
            if ($nom === false) {
                header("Location: " . BASE_URL . "/latitude/article/$id/editer?error=invalid_name");
                exit;
            }
            
            $this->article->id = $id;
            $this->article->nom = $nom;

            try {
                if($this->article->update()) {
                    header("Location: " . BASE_URL . "/latitude/article/nouveau?success=article_updated");
                    exit();
                } else {
                    header("Location: " . BASE_URL . "/latitude/article/" . $this->article->id . "/editer?error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification article: " . $e->getMessage());
                header("Location: " . BASE_URL . "/latitude/article/" . $this->article->id . "/editer?error=update_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer un article
     */
    public function supprimer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/latitude?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID
            $id = Validator::id($_POST['id'] ?? 0);
            if ($id === false) {
                header("Location: " . BASE_URL . "/latitude/article/nouveau?error=invalid_id");
                exit;
            }
            
            $this->article->id = $id;

            try {
                if($this->article->delete()) {
                    header("Location: " . BASE_URL . "/latitude/article/nouveau?success=article_deleted");
                    exit();
                } else {
                    header("Location: " . BASE_URL . "/latitude/article/nouveau?error=delete_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur suppression article: " . $e->getMessage());
                header("Location: " . BASE_URL . "/latitude/article/nouveau?error=delete_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer une sélection d'articles
     */
    public function supprimerSelection() {
        
        // Validation CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/latitude?error=csrf_invalid");
            exit;
        }
        
        // Valider le tableau d'IDs
        $ids = Validator::arrayOfIds($_POST['ids'] ?? []);
        
        if ($ids === false) {
            header("Location: " . BASE_URL . "/latitude/article/nouveau?error=no_selection");
            exit;
        }

        try {
            foreach($ids as $id) {
                $this->article->id = $id;
                $this->article->delete();
            }
            header("Location: " . BASE_URL . "/latitude/article/nouveau?success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: " . BASE_URL . "/latitude/article/nouveau?error=delete_failed");
            exit();
        }
    }

    /**
     * Récupérer tous les articles
     */
    public function getAll() {
        return $this->article->readAll();
    }
}
