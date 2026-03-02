<?php
/**
 * Contrôleur pour les articles Latitude
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
            
            $this->article->nom = $_POST['nom'] ?? '';

            // Vérifier si l'article existe déjà
            if($this->article->exists()) {
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?page=nouveau-article-latitude&error=article_exists");
                exit();
            }

            try {
                if($this->article->create()) {
                    // Ne PAS stocker en session pour vider les champs
                    unset($_SESSION['form_data']);
                    header("Location: index.php?page=nouveau-article-latitude&success=article_created");
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: index.php?page=nouveau-article-latitude&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création article: " . $e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?page=nouveau-article-latitude&error=create_failed");
                exit();
            }
        }
    }

    /**
     * Afficher la page d'édition
     */
    public function edition() {
        $id = $_GET['id'] ?? 0;
        $this->article->id = $id;
        $articleData = $this->article->readOne();
        
        if($articleData) {
            require_once 'views/latitude/articles_latitude/edition_article_latitude.php';
        } else {
            header("Location: index.php?page=nouveau-article-latitude&error=not_found");
            exit();
        }
    }

    /**
     * Modifier un article
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->article->id = $_POST['id'] ?? '';
            $this->article->nom = $_POST['nom'] ?? '';

            try {
                if($this->article->update()) {
                    header("Location: index.php?page=nouveau-article-latitude&success=article_updated");
                    exit();
                } else {
                    header("Location: index.php?page=editer-article-latitude&id=" . $this->article->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification article: " . $e->getMessage());
                header("Location: index.php?page=editer-article-latitude&id=" . $this->article->id . "&error=update_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer un article
     */
    public function supprimer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $this->article->id = $id;

            try {
                if($this->article->delete()) {
                    header("Location: index.php?page=nouveau-article-latitude&success=article_deleted");
                    exit();
                } else {
                    header("Location: index.php?page=nouveau-article-latitude&error=delete_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur suppression article: " . $e->getMessage());
                header("Location: index.php?page=nouveau-article-latitude&error=delete_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer une sélection d'articles
     */
    public function supprimerSelection() {
        $ids = $_POST['ids'] ?? [];
        if(empty($ids)) {
            header("Location: index.php?page=nouveau-article-latitude&error=no_selection");
            exit();
        }

        try {
            foreach($ids as $id) {
                $this->article->id = intval($id);
                $this->article->delete();
            }
            header("Location: index.php?page=nouveau-article-latitude&success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: index.php?page=nouveau-article-latitude&error=delete_failed");
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
