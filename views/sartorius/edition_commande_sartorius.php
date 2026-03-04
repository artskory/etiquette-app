<?php 
require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array
$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}

// Décoder les quantités JSON
$quantitesArray = json_decode($commandeData['quantites'] ?? '[]', true);
if(!$quantitesArray || !is_array($quantitesArray) || empty($quantitesArray)) {
    // Si pas de quantités JSON, utiliser les anciennes colonnes
    $quantitesArray = [[
        'quantite_par_carton' => $commandeData['quantite_par_carton'] ?? 0,
        'quantite_etiquettes' => $commandeData['quantite_etiquettes'] ?? 0
    ]];
}
?>
<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Édition étiquette Sartorius</h1>
            <div>
                <a href="index.php?page=sartorius" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Annuler
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="commandeForm" action="index.php?page=modifier-commande" method="POST">
                    <?php echo CsrfToken::field(); ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($commandeData['id']); ?>">
                    
                    <!-- Ligne fixe en haut -->
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

                    <!-- Lignes de quantités dynamiques -->
                    <div id="quantitesContainer">
                        <?php foreach($quantitesArray as $index => $qty): ?>
                            <div class="quantite-row mb-3" data-row-index="<?php echo $index; ?>">
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" name="quantites[<?php echo $index; ?>][quantite_par_carton]" 
                                            value="<?php echo htmlspecialchars($qty['quantite_par_carton']); ?>" required min="1">
                                    </div>
                                    
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <i class="bi bi-boxes blue icons"></i>Quantité d'étiquettes <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" name="quantites[<?php echo $index; ?>][quantite_etiquettes]" 
                                            value="<?php echo htmlspecialchars($qty['quantite_etiquettes']); ?>" required min="1">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <?php if($index === 0): ?>
                                            <button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneQuantite()" 
                                                    style="<?php echo count($quantitesArray) > 1 ? 'display:none;' : ''; ?>">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        <?php else: ?>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-primary flex-fill" onclick="ajouterLigneQuantite()">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger flex-fill" onclick="supprimerLigneQuantite(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-end">
                        <button type="submit" form="commandeForm" class="btn btn-info">
                            <i class="bi bi-check-circle me-1"></i>Sauvegarder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.quantite-row {
    animation: slideDown 0.3s ease-out;
    opacity: 1;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.quantite-row.removing {
    animation: slideUp 0.3s ease-in forwards;
}

@keyframes slideUp {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(-20px);
        opacity: 0;
    }
}
</style>

<script>
let quantiteRowIndex = <?php echo count($quantitesArray); ?>;

function ajouterLigneQuantite() {
    const container = document.getElementById('quantitesContainer');
    
    const firstAddBtn = document.querySelector('.btn-add-first');
    if(firstAddBtn) {
        firstAddBtn.style.display = 'none';
    }
    
    const newRow = document.createElement('div');
    newRow.className = 'quantite-row mb-3';
    newRow.setAttribute('data-row-index', quantiteRowIndex);
    
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label">
                    <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" name="quantites[${quantiteRowIndex}][quantite_par_carton]" required min="1">
            </div>
            
            <div class="col-md-5">
                <label class="form-label">
                    <i class="bi bi-boxes blue icons"></i>Quantité d'étiquettes <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" name="quantites[${quantiteRowIndex}][quantite_etiquettes]" required min="1">
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="button" class="btn btn-primary flex-fill" onclick="ajouterLigneQuantite()">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button type="button" class="btn btn-danger flex-fill" onclick="supprimerLigneQuantite(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    quantiteRowIndex++;
}

function supprimerLigneQuantite(button) {
    const row = button.closest('.quantite-row');
    const container = document.getElementById('quantitesContainer');
    
    row.classList.add('removing');
    
    setTimeout(() => {
        row.remove();
        
        const remainingRows = container.querySelectorAll('.quantite-row');
        if(remainingRows.length === 1) {
            const firstAddBtn = document.querySelector('.btn-add-first');
            if(firstAddBtn) {
                firstAddBtn.style.display = 'block';
            }
        }
    }, 300);
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
