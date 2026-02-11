<?php require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array
$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}
?>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Édition étiquette</h1>
        <div>
            <button type="submit" form="commandeForm" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
            <a href="index.php?page=sartorius" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle me-1"></i>Annuler
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="commandeForm" action="index.php?page=modifier-commande" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($commandeData['id']); ?>">
                
                <!-- Ligne fixe en haut : Référence, Date, N° Commande, N° Lot -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-bookmarks blue icons"></i>Référence <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="reference_id" required>
                            <option value="">-- Sélectionnez --</option>
                            <?php foreach($referencesArray as $ref): ?>
                                <option value="<?php echo $ref['id']; ?>" 
                                    <?php echo ($ref['id'] == $commandeData['reference_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref['reference']) . ' - ' . htmlspecialchars($ref['designation']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar blue icons"></i>Date de production <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="date_production" required>
                            <option value="">-- Sélectionnez --</option>
                            <?php
                            $currentYear = date('Y');
                            $months = [
                                '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
                                '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
                                '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                            ];
                            for($year = $currentYear; $year <= $currentYear + 5; $year++) {
                                foreach($months as $num => $name) {
                                    $value = $num . '/' . $year;
                                    $display = $name . ' ' . $year;
                                    $selected = ($value == $commandeData['date_production']) ? 'selected' : '';
                                    echo "<option value='$value' $selected>$display</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-cart4 blue icons"></i>N° Commande <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="numero_commande" 
                               value="<?php echo htmlspecialchars($commandeData['numero_commande']); ?>" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="numero_lot" 
                               value="<?php echo htmlspecialchars($commandeData['numero_lot']); ?>" required>
                    </div>
                </div>

                <hr>

                <!-- Ligne de quantités (une seule ligne en édition) -->
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" name="quantite_par_carton" 
                               value="<?php echo htmlspecialchars($commandeData['quantite_par_carton']); ?>" required min="1">
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="bi bi-boxes blue icons"></i>Quantité d'étiquettes <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" name="quantite_etiquettes" 
                               value="<?php echo htmlspecialchars($commandeData['quantite_etiquettes']); ?>" required min="1">
                    </div>
                    
                    <div class="col-md-2">
                        <!-- Pas de bouton + en édition -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
