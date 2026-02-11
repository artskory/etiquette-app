<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Édition Référence</h1>
        <div>
            <button type="submit" form="referenceForm" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
            <a href="index.php?page=ajout-reference" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle me-1"></i>Annuler
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="referenceForm" action="index.php?page=modifier-reference" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($referenceData['id']); ?>">
                
                <div class="row">    
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference" name="reference" required 
                               value="<?php echo htmlspecialchars($referenceData['reference']); ?>"
                               placeholder="Entrez la référence">
                    </div>

                    <div class="col-md-6">
                        <label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation" required 
                               value="<?php echo htmlspecialchars($referenceData['designation']); ?>"
                               placeholder="Entrez la désignation">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
