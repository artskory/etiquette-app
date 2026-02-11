<?php require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array pour pouvoir le réutiliser
$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}
?>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nouvelle étiquette</h1>
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
            <form id="commandeForm" action="index.php?page=creer-commande" method="POST">
                <!-- Ligne fixe en haut : Référence, Date, N° Commande, N° Lot -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-bookmarks blue icons"></i>Référence <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="reference_id" id="reference_id" required>
                            <option value="">-- Sélectionnez --</option>
                            <?php foreach($referencesArray as $ref): ?>
                                <option value="<?php echo $ref['id']; ?>">
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
                                    echo "<option value='$value'>$display</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-cart4 blue icons"></i>N° Commande <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="numero_commande" id="numero_commande" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="numero_lot" id="numero_lot" required>
                    </div>
                </div>

                <hr>

                <!-- Lignes de quantités dynamiques -->
                <div id="quantitesContainer">
                    <!-- Première ligne de quantités -->
                    <div class="quantite-row mb-3" data-row-index="0">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" name="quantites[0][quantite_par_carton]" required min="1">
                            </div>
                            
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="bi bi-boxes blue icons"></i>Quantité d'étiquettes <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" name="quantites[0][quantite_etiquettes]" required min="1">
                            </div>
                            
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneQuantite()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
let quantiteRowIndex = 1;

function ajouterLigneQuantite() {
    const container = document.getElementById('quantitesContainer');
    
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
