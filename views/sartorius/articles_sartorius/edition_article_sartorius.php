<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4 col-md-9 overview-card">
    <div class="d-flex justify-content-between align-items-center mb-4 header-table">
        <h1 class="greeting-title">Édition Référence</h1>
        <div>
            <a href="<?= BASE_URL ?>/sartorius/reference/ajout" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>Annuler
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="referenceForm" action="<?= BASE_URL ?>/sartorius/reference/<?php echo $reference['id']; ?>/modifier" method="POST">
                <?php echo CsrfToken::field(); ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($referenceData['id']); ?>">
                
                <div class="row">    
                    <div class="col-md-6">
                        <i class="bi bi-hash blue icons"></i><label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference" name="reference" required 
                               value="<?php echo htmlspecialchars($referenceData['reference']); ?>"
                               placeholder="Entrez la référence">
                    </div>

                    <div class="col-md-6 mb-3">
                        <i class="bi bi-bookmarks blue icons"></i></i><label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation" required 
                               value="<?php echo htmlspecialchars($referenceData['designation']); ?>"
                               placeholder="Entrez la désignation">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" form="referenceForm" class="btn btn-info">
                        <i class="bi bi-check-circle me-1"></i>Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
