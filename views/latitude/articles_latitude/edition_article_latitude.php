<?php require_once 'views/layouts/header.php'; ?>

    <div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Éditer l'article Latitude</h1>
            <a href="<?= BASE_URL ?>/latitude/article/nouveau" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/latitude/article/<?php echo $article['id']; ?>/modifier" method="POST">
                    <?php echo CsrfToken::field(); ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($articleData['id']); ?>">
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            <i class="bi bi-tag blue icons"></i>Nom de l'article <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nom" name="nom" required 
                            value="<?php echo htmlspecialchars($articleData['nom']); ?>"
                            placeholder="Ex: Carte postale, Flyer A5, Brochure...">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-check-circle me-1"></i>Sauvegarder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
