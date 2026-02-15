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
        require_once 'views/articles_latitude/nouveau.php';
    }

    /**
     * Créer un nouvel article
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->article->nom = $_POST['nom'] ?? '';

            // Vérifier si l'article existe déjà
            if($this->article->exists()) {
                header("Location: index.php?page=nouveau-article-latitude&error=article_exists");
                exit();
            }

            try {
                if($this->article->create()) {
                    header("Location: index.php?page=nouveau-article-latitude&success=article_created");
                    exit();
                } else {
                    header("Location: index.php?page=nouveau-article-latitude&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création article: " . $e->getMessage());
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
            require_once 'views/articles_latitude/edition.php';
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
     * Récupérer tous les articles
     */
    public function getAll() {
        return $this->article->readAll();
    }
}
