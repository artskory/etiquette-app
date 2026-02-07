<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-circle me-2"></i>Nouvelle étiquette</h1>
        <div>
            <a href="index.php?page=sartorius" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle me-1"></i>Annuler
            </a>
            <button type="submit" form="commandeForm" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="commandeForm" action="index.php?page=creer-commande" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reference_id" class="form-label">Référence <span class="text-danger">*</span></label>
                        <select class="form-select" id="reference_id" name="reference_id" required>
                            <option value="">-- Sélectionnez une référence --</option>
                            <?php while ($ref = $references->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $ref['id']; ?>">
                                    <?php echo htmlspecialchars($ref['reference']) . ' - ' . htmlspecialchars($ref['designation']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="quantite_par_carton" class="form-label">Quantité par carton <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantite_par_carton" name="quantite_par_carton" required min="1">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_production" class="form-label">Date de production (MM/AAAA) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="date_production" name="date_production" 
                               placeholder="MM/AAAA" pattern="(0[1-9]|1[0-2])\/\d{4}" required
                               title="Format: MM/AAAA (ex: 01/2024)">
                        <div class="form-text">Format: MM/AAAA (ex: 01/2024)</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="numero_commande" class="form-label">N° Commande <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="numero_commande" name="numero_commande" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="numero_lot" class="form-label">N° Lot <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="numero_lot" name="numero_lot" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="quantite_etiquettes" class="form-label">Quantité d'étiquettes <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantite_etiquettes" name="quantite_etiquettes" required min="1">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
