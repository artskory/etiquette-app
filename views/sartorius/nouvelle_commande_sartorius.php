<?php require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array pour pouvoir le réutiliser
$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}

// Restaurer les données du formulaire depuis la session si erreur
$formData       = $_SESSION['form_data'] ?? [];
$savedRefId     = $formData['reference_id'] ?? '';
$savedDate      = $formData['date_production'] ?? '';
$savedNumCmd    = htmlspecialchars($formData['numero_commande'] ?? '');
$savedNumLot    = htmlspecialchars($formData['numero_lot'] ?? '');
$savedQuantites = $formData['quantites'] ?? [];
unset($_SESSION['form_data']);
?>
<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Nouvelle étiquette Sartorius</h1>
            <div>
                <a href="<?= BASE_URL ?>/sartorius/reference/ajout?from=nouvelle" class="btn btn-success me-2">
                    <i class="bi bi-tags me-1"></i>Références
                </a>
                <a href="<?= BASE_URL ?>/sartorius" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Annuler
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="commandeForm" action="<?= BASE_URL ?>/sartorius/creer" method="POST">
                    <?php echo CsrfToken::field(); ?>
                    <!-- Ligne fixe en haut : Référence, Date, N° Commande, N° Lot -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-hash blue icons"></i>Référence <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="reference_id" id="reference_id" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach($referencesArray as $ref): ?>
                                    <option value="<?php echo $ref['id']; ?>" <?php echo ($ref['id'] == $savedRefId) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ref['reference']) . ' - ' . htmlspecialchars($ref['designation']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-calendar blue icons"></i>Date de production <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="date_production" id="date_production" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php
                                $currentYear  = (int)date('Y');
                                $currentMonth = (int)date('m');
                                $months = [
                                    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
                                    '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
                                    '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                                ];
                                // De janvier N au mois courant de N+1 (janvier N+1 inclus)
                                $limitYear  = $currentYear + 1;
                                $limitMonth = $currentMonth; // même mois, année suivante
                                foreach($months as $num => $name) {
                                    // Année N : tous les mois
                                    $value   = $num . '/' . $currentYear;
                                    $display = $name . ' ' . $currentYear;
                                    $selected = ($value === $savedDate) ? ' selected' : '';
                                    echo "<option value='$value'$selected>$display</option>";
                                }
                                foreach($months as $num => $name) {
                                    // Année N+1 : de janvier jusqu'au mois courant inclus
                                    if((int)$num > $limitMonth) break;
                                    $value   = $num . '/' . $limitYear;
                                    $display = $name . ' ' . $limitYear;
                                    $selected = ($value === $savedDate) ? ' selected' : '';
                                    echo "<option value='$value'$selected>$display</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-cart4 blue icons"></i>N° Commande <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="numero_commande" id="numero_commande" required value="<?php echo $savedNumCmd; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="numero_lot" id="numero_lot" required value="<?php echo $savedNumLot; ?>">
                        </div>
                    </div>

                    <hr>

                    <!-- Lignes de quantités dynamiques -->
                    <div id="quantitesContainer">
                    <?php
                    $quantitesRestore = !empty($savedQuantites) ? array_values($savedQuantites) : [['quantite_par_carton'=>'','quantite_etiquettes'=>'']];
                    $totalRows = count($quantitesRestore);
                    foreach($quantitesRestore as $idx => $q):
                        $qpc = htmlspecialchars($q['quantite_par_carton'] ?? '');
                        $qe  = htmlspecialchars($q['quantite_etiquettes'] ?? '');
                        $isOnly = ($totalRows === 1);
                    ?>
                        <div class="quantite-row mb-3" data-row-index="<?php echo $idx; ?>">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="quantites[<?php echo $idx; ?>][quantite_par_carton]" required min="1" value="<?php echo $qpc; ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="quantites[<?php echo $idx; ?>][quantite_etiquettes]" required min="1" value="<?php echo $qe; ?>">
                                </div>
                                <div class="col-md-2 <?php echo $isOnly ? '' : 'd-flex gap-2'; ?>">
                                    <?php if($isOnly): ?>
                                        <button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneQuantite()">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-danger w-100" onclick="supprimerLigneQuantite(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
let quantiteRowIndex = <?php echo count($quantitesRestore); ?>;

function ajouterLigneQuantite() {
    const container = document.getElementById('quantitesContainer');
    
    // Ne permettre qu'une seule ligne supplémentaire (2 lignes max)
    if(container.querySelectorAll('.quantite-row').length >= 2) return;
    
    // Masquer le bouton + de la première ligne
    const firstAddBtn = document.querySelector('.btn-add-first');
    if(firstAddBtn) {
        firstAddBtn.style.display = 'none';
    }
    
    // Créer la nouvelle ligne
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
                    <i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control" name="quantites[${quantiteRowIndex}][quantite_etiquettes]" required min="1">
            </div>
            
            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="supprimerLigneQuantite(this)">
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
    
    // Animation de suppression
    row.classList.add('removing');
    
    // Supprimer après l'animation
    setTimeout(() => {
        row.remove();
        
        // Si il ne reste qu'une seule ligne, réafficher le bouton + de la première ligne
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
