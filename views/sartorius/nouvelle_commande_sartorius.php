<?php require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array pour pouvoir le réutiliser
$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}

// Restaurer les données du formulaire depuis la session si erreur
$formData    = $_SESSION['form_data'] ?? [];
$savedBlocks = $formData['commandes'] ?? [];
unset($_SESSION['form_data']);

// Bloc par défaut si aucune restauration
if (empty($savedBlocks)) {
    $savedBlocks = [[
        'reference_id'    => '',
        'date_production' => '',
        'numero_commande' => '',
        'numero_lot'      => '',
        'quantites'       => [['quantite_par_carton' => '', 'quantite_etiquettes' => '']],
    ]];
}
// Données du premier bloc pour pré-remplissage du bloc 0
$bloc0          = $savedBlocks[0] ?? [];
$savedRefId     = $bloc0['reference_id'] ?? '';
$savedDate      = $bloc0['date_production'] ?? '';
$savedNumCmd    = htmlspecialchars($bloc0['numero_commande'] ?? '');
$savedNumLot    = htmlspecialchars($bloc0['numero_lot'] ?? '');
$savedQuantites = $bloc0['quantites'] ?? [['quantite_par_carton' => '', 'quantite_etiquettes' => '']];
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
        <form id="commandeForm" action="<?= BASE_URL ?>/sartorius/creer" method="POST">
        <?php echo CsrfToken::field(); ?>
        <div id="blocs-container">
        <div class="card mb-3 bloc-commande" id="bloc-0" data-bloc-index="0">
            <div class="card-body">
                    <!-- Ligne fixe en haut : Référence, Date, N° Commande, N° Lot -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-hash blue icons"></i>Référence <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="commandes[0][reference_id]" required>
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
                            <select class="form-select" name="commandes[0][date_production]" required>
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
                            <input type="text" class="form-control" name="commandes[0][numero_commande]" required value="<?php echo $savedNumCmd; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="commandes[0][numero_lot]" required value="<?php echo $savedNumLot; ?>">
                        </div>
                    </div>

                    <hr>

                    <!-- Lignes de quantités dynamiques -->
                    <div id="quantitesContainer" class="quantites-container">
                    <?php
                    $quantitesRestore = !empty($savedQuantites) ? array_values($savedQuantites) : [['quantite_par_carton'=>'','quantite_etiquettes'=>'']];
                    $totalRows = count($quantitesRestore);
                    foreach($quantitesRestore as $idx => $q):
                        $qpc = htmlspecialchars($q['quantite_par_carton'] ?? '');
                        $qe  = htmlspecialchars($q['quantite_etiquettes'] ?? '');
                    ?>
                        <div class="quantite-row mb-3" data-row-index="<?php echo $idx; ?>">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="commandes[0][quantites][<?php echo $idx; ?>][quantite_par_carton]" required min="1" value="<?php echo $qpc; ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="commandes[0][quantites][<?php echo $idx; ?>][quantite_etiquettes]" required min="1" value="<?php echo $qe; ?>">
                                </div>
                                <div class="col-md-2"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
            </div>
        </div>
        </div><!-- /blocs-container -->
        </form>
        <div class="text-end mt-3">
            <button type="submit" form="commandeForm" class="btn btn-info">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
        </div>
    
    </div>   
</div>

<style>
.bloc-commande:nth-child(even),
.bloc-commande:nth-child(even) > .card-body {
    background-color: #f2f3f5;
}

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
<?php
$currentYear  = (int)date('Y');
$currentMonth = (int)date('m');
$months = [
    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
    '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
    '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
];
$limitYear  = $currentYear + 1;
$limitMonth = $currentMonth;
$dateOpts = [];
foreach($months as $num => $name) {
    $dateOpts[] = ['value' => "$num/$currentYear", 'label' => "$name $currentYear"];
}
foreach($months as $num => $name) {
    if((int)$num > $limitMonth) break;
    $dateOpts[] = ['value' => "$num/$limitYear", 'label' => "$name $limitYear"];
}
?>
const REFS       = <?php echo json_encode(array_map(function($r) {
    return [
        'id'                  => $r['id'],
        'reference'           => $r['reference'],
        'designation'         => $r['designation'],
        'quantite_par_carton' => $r['quantite_par_carton'] ?? null,
    ];
}, $referencesArray)); ?>;
const DATE_OPTS  = <?php echo json_encode($dateOpts); ?>;
const qtyPerBloc = { 0: <?php echo count($quantitesRestore); ?> };
let blocCounter  = 1;

// ── Auto-remplissage Quantité par carton selon la référence sélectionnée ─────
function attachRefChangeListener(selectEl) {
    selectEl.addEventListener('change', function() {
        const selectedId = parseInt(this.value);
        const ref = REFS.find(r => parseInt(r.id) === selectedId);
        const bloc = this.closest('.bloc-commande');
        const qpcInput = bloc ? bloc.querySelector('input[name*="[quantite_par_carton]"]') : null;
        if (qpcInput && ref && ref.quantite_par_carton !== null) {
            qpcInput.value = ref.quantite_par_carton;
        } else if (qpcInput) {
            qpcInput.value = '';
        }
    });
}

// ── Règle unique : recalcule les boutons de tous les blocs ────────────────────
// Ligne seule + dernier bloc  → [+] [⊞]
// Ligne seule + autre bloc    → [+] pleine largeur
// Première ligne (multi)      → vide
// Dernière ligne + dernier bloc → [🗑] [⊞]
// Toutes les autres           → [🗑] pleine largeur
function rafraichirBoutons() {
    const blocs = document.querySelectorAll('#blocs-container .bloc-commande');
    blocs.forEach((bloc, blocIdx) => {
        const isLastBloc = (blocIdx === blocs.length - 1);
        const rows       = bloc.querySelectorAll('.quantites-container .quantite-row');
        const nbRows     = rows.length;

        rows.forEach((row, rowIdx) => {
            const col       = row.querySelector('.col-md-2');
            const isFirst   = (rowIdx === 0);
            const isLastRow = (rowIdx === nbRows - 1);

            if (nbRows === 1) {
                col.innerHTML = isLastBloc
                    ? `<div class="d-flex gap-1">
                           <button type="button" class="btn btn-primary flex-fill" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>
                           <button type="button" class="btn btn-success flex-fill" onclick="ajouterBloc()" title="Ajouter un formulaire"><i class="bi bi-file-earmark-plus"></i></button>
                       </div>`
                    : `<button type="button" class="btn btn-primary w-100" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>`;
            } else if (isFirst) {
                col.innerHTML = '';
            } else if (isLastRow && isLastBloc) {
                col.innerHTML = `
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-danger flex-fill" onclick="supprimerLigneQuantite(this)"><i class="bi bi-trash"></i></button>
                        <button type="button" class="btn btn-success flex-fill" onclick="ajouterBloc()" title="Ajouter un formulaire"><i class="bi bi-file-earmark-plus"></i></button>
                    </div>`;
            } else {
                col.innerHTML = `<button type="button" class="btn btn-danger w-100" onclick="supprimerLigneQuantite(this)"><i class="bi bi-trash"></i></button>`;
            }
        });
    });
}

function ajouterLigneQuantite(btn) {
    const bloc      = btn.closest('.bloc-commande');
    const blocIdx   = parseInt(bloc.dataset.blocIndex);
    const container = bloc.querySelector('.quantites-container');
    const qIdx      = qtyPerBloc[blocIdx] ?? 1;

    const newRow = document.createElement('div');
    newRow.className = 'quantite-row mb-3';
    newRow.dataset.rowIndex = qIdx;
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label"><i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="commandes[${blocIdx}][quantites][${qIdx}][quantite_par_carton]" required min="1">
            </div>
            <div class="col-md-5">
                <label class="form-label"><i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="commandes[${blocIdx}][quantites][${qIdx}][quantite_etiquettes]" required min="1">
            </div>
            <div class="col-md-2"></div>
        </div>`;
    container.appendChild(newRow);
    qtyPerBloc[blocIdx] = qIdx + 1;
    rafraichirBoutons();
}

function supprimerLigneQuantite(btn) {
    const row = btn.closest('.quantite-row');
    row.classList.add('removing');
    setTimeout(() => { row.remove(); rafraichirBoutons(); }, 300);
}

function supprimerBloc(btn) {
    const bloc = btn.closest('.bloc-commande');
    bloc.classList.add('removing');
    setTimeout(() => { bloc.remove(); renommerBlocs(); }, 300);
}

function renommerBlocs() {
    document.querySelectorAll('.bloc-commande').forEach((bloc, idx) => {
        const old = parseInt(bloc.dataset.blocIndex);
        bloc.dataset.blocIndex = idx;
        bloc.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/commandes\[\d+\]/, `commandes[${idx}]`);
        });
        qtyPerBloc[idx] = qtyPerBloc[old];
        const btnDel = bloc.querySelector('.btn-supprimer-bloc');
        if (btnDel) btnDel.style.display = (idx === 0) ? 'none' : '';
    });
    rafraichirBoutons();
}

function ajouterBloc() {
    const idx       = blocCounter;
    const container = document.getElementById('blocs-container');
    const refsOpts  = REFS.map(r => `<option value="${r.id}">${escHtml(r.reference)} - ${escHtml(r.designation)}</option>`).join('');
    const dateOpts  = DATE_OPTS.map(o => `<option value="${o.value}">${o.label}</option>`).join('');

    const bloc = document.createElement('div');
    bloc.className = 'card mb-3 bloc-commande';
    bloc.dataset.blocIndex = idx;
    bloc.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button type="button" class="btn btn-sm btn-danger btn-supprimer-bloc" onclick="supprimerBloc(this)"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-hash blue icons"></i>Référence <span class="text-danger">*</span></label>
                    <select class="form-select" name="commandes[${idx}][reference_id]" required>
                        <option value="">-- Sélectionnez --</option>${refsOpts}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-calendar blue icons"></i>Date de production <span class="text-danger">*</span></label>
                    <select class="form-select" name="commandes[${idx}][date_production]" required>
                        <option value="">-- Sélectionnez --</option>${dateOpts}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-cart4 blue icons"></i>N° Commande <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="commandes[${idx}][numero_commande]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="commandes[${idx}][numero_lot]" required>
                </div>
            </div>
            <hr>
            <div class="quantites-container">
                <div class="quantite-row mb-3" data-row-index="0">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label"><i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="commandes[${idx}][quantites][0][quantite_par_carton]" required min="1">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label"><i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="commandes[${idx}][quantites][0][quantite_etiquettes]" required min="1">
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>`;
    container.appendChild(bloc);
    const newSelect = bloc.querySelector(`select[name="commandes[${idx}][reference_id]"]`);
    if (newSelect) attachRefChangeListener(newSelect);
    qtyPerBloc[idx] = 1;
    blocCounter++;
    rafraichirBoutons();
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Initialiser les boutons et les listeners au chargement
document.addEventListener('DOMContentLoaded', function() {
    rafraichirBoutons();
    const bloc0Select = document.querySelector('#bloc-0 select[name="commandes[0][reference_id]"]');
    if (bloc0Select) attachRefChangeListener(bloc0Select);
});
</script>


<?php require_once 'views/layouts/footer.php'; ?>
