<?php 
require_once 'views/layouts/header.php'; 

$referencesArray = [];
while ($ref = $references->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $ref;
}

// Options de date
$currentYear  = (int)date('Y');
$currentMonth = (int)date('m');
$months = [
    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
    '05' => 'Mai',     '06' => 'Juin',    '07' => 'Juillet', '08' => 'Août',
    '09' => 'Septembre','10' => 'Octobre','11' => 'Novembre','12' => 'Décembre'
];
$limitYear  = $currentYear + 1;
$limitMonth = $currentMonth;
$dateOpts = [];
foreach ($months as $num => $name) {
    $dateOpts[] = ['value' => "$num/$currentYear", 'label' => "$name $currentYear"];
}
foreach ($months as $num => $name) {
    if ((int)$num > $limitMonth) break;
    $dateOpts[] = ['value' => "$num/$limitYear", 'label' => "$name $limitYear"];
}
// Ajouter les dates hors plage présentes dans les étiquettes
foreach ($etiquettesBatch as $etiq) {
    $d = $etiq['date_production'] ?? '';
    $inList = array_filter($dateOpts, fn($o) => $o['value'] === $d);
    if ($d && empty($inList)) {
        [$m, $y] = explode('/', $d);
        $dateOpts[] = ['value' => $d, 'label' => ($months[$m] ?? $m) . ' ' . $y];
    }
}
?>

<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Édition étiquette Sartorius</h1>
            <div>
                <a href="<?= BASE_URL ?>/sartorius" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Annuler
                </a>
            </div>
        </div>

        <form id="commandeForm" action="<?= BASE_URL ?>/sartorius/commande/<?= $commandeData['id'] ?>/modifier" method="POST">
        <?php echo CsrfToken::field(); ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($commandeData['id']) ?>">
        <div id="blocs-container">

        <?php foreach ($etiquettesBatch as $blocIdx => $etiq):
            $qArray = $etiq['quantites'] ?? [['quantite_par_carton' => '', 'quantite_etiquettes' => '']];
        ?>
            <div class="card mb-3 bloc-commande" id="bloc-<?= $blocIdx ?>" data-bloc-index="<?= $blocIdx ?>">
                <div class="card-body">

                    <?php if ($blocIdx > 0): ?>
                    <div class="d-flex justify-content-end align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-danger btn-supprimer-bloc" onclick="supprimerBloc(this)">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-hash blue icons"></i>Référence <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="commandes[<?= $blocIdx ?>][reference_id]" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($referencesArray as $ref): ?>
                                    <option value="<?= $ref['id'] ?>" <?= ($ref['id'] == $etiq['reference_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ref['reference']) . ' - ' . htmlspecialchars($ref['designation']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-calendar blue icons"></i>Date de production <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="commandes[<?= $blocIdx ?>][date_production]" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($dateOpts as $opt): ?>
                                    <option value="<?= $opt['value'] ?>" <?= ($opt['value'] === $etiq['date_production']) ? 'selected' : '' ?>>
                                        <?= $opt['label'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-cart4 blue icons"></i>N° Commande <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="commandes[<?= $blocIdx ?>][numero_commande]"
                                   value="<?= htmlspecialchars($etiq['numero_commande']) ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-tags blue icons"></i>N° Lot <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="commandes[<?= $blocIdx ?>][numero_lot]"
                                   value="<?= htmlspecialchars($etiq['numero_lot']) ?>" required>
                        </div>
                    </div>

                    <hr>

                    <div class="quantites-container">
                    <?php
                        $nbBlocs    = count($etiquettesBatch);
                        $isLastBloc = ($blocIdx === $nbBlocs - 1);
                        $nbRows     = count($qArray);
                    ?>
                    <?php foreach ($qArray as $qIdx => $qty):
                        $qpc        = htmlspecialchars($qty['quantite_par_carton'] ?? '');
                        $qe         = htmlspecialchars($qty['quantite_etiquettes'] ?? '');
                        $isFirstRow = ($qIdx === 0);
                        $isOnly     = ($nbRows === 1);
                    ?>
                        <div class="quantite-row mb-3" data-row-index="<?= $qIdx ?>">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-stack blue icons"></i>Quantité par carton <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           name="commandes[<?= $blocIdx ?>][quantites][<?= $qIdx ?>][quantite_par_carton]"
                                           required min="1" value="<?= $qpc ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-boxes blue icons"></i>Nombre de cartons <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           name="commandes[<?= $blocIdx ?>][quantites][<?= $qIdx ?>][quantite_etiquettes]"
                                           required min="1" value="<?= $qe ?>">
                                </div>
                                <div class="col-md-2">
                                    <?php if ($isOnly && $isLastBloc): ?>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-primary flex-fill btn-add-first" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-success flex-fill btn-add-bloc-trigger" onclick="ajouterBloc()" title="Ajouter un formulaire">
                                                <i class="bi bi-file-earmark-plus"></i>
                                            </button>
                                        </div>
                                    <?php elseif ($isOnly && !$isLastBloc): ?>
                                        <button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    <?php elseif ($isFirstRow): ?>
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

                </div>
            </div>
        <?php endforeach; ?>

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
.bloc-commande:nth-child(even) > .card-body { background-color: #f2f3f5; }
.quantite-row  { animation: slideDown 0.3s ease-out; opacity: 1; }
.bloc-commande { animation: slideDown 0.3s ease-out; }
@keyframes slideDown {
    from { transform: translateY(-15px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}
.quantite-row.removing,
.bloc-commande.removing { animation: slideUp 0.3s ease-in forwards; }
@keyframes slideUp {
    from { transform: translateY(0);     opacity: 1; }
    to   { transform: translateY(-15px); opacity: 0; }
}
</style>

<script>
const REFS      = <?= json_encode($referencesArray) ?>;
const DATE_OPTS = <?= json_encode($dateOpts) ?>;
const qtyPerBloc = <?= json_encode(array_combine(
    array_keys($etiquettesBatch),
    array_map(fn($e) => count($e['quantites'] ?? [['']]), $etiquettesBatch)
)) ?>;
let blocCounter = <?= count($etiquettesBatch) ?>;

// ── Règle unique : recalcule les boutons de tous les blocs ───────────────────
// Ligne 1 d'un bloc avec 1 seule ligne :
//   - dernier bloc  → + | ⊞
//   - autre bloc    → + (pleine largeur)
// Ligne 1 d'un bloc avec plusieurs lignes : vide
// Lignes suivantes :
//   - dernière ligne du dernier bloc → 🗑 | ⊞
//   - toutes les autres              → 🗑 (pleine largeur)
function rafraichirBoutons() {
    const blocs = document.querySelectorAll('#blocs-container .bloc-commande');
    blocs.forEach((bloc, blocIdx) => {
        const isLastBloc = (blocIdx === blocs.length - 1);
        const rows       = bloc.querySelectorAll('.quantites-container .quantite-row');
        const nbRows     = rows.length;

        rows.forEach((row, rowIdx) => {
            const col      = row.querySelector('.col-md-2');
            const isFirst  = (rowIdx === 0);
            const isLastRow = (rowIdx === nbRows - 1);

            if (nbRows === 1) {
                // Seule ligne du bloc
                if (isLastBloc) {
                    col.innerHTML = `
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-primary flex-fill btn-add-first" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>
                            <button type="button" class="btn btn-success flex-fill" onclick="ajouterBloc()" title="Ajouter un formulaire"><i class="bi bi-file-earmark-plus"></i></button>
                        </div>`;
                } else {
                    col.innerHTML = `<button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneQuantite(this)" title="Ajouter une ligne"><i class="bi bi-plus-lg"></i></button>`;
                }
            } else if (isFirst) {
                // Première ligne parmi plusieurs : vide
                col.innerHTML = '';
            } else if (isLastRow && isLastBloc) {
                // Dernière ligne du dernier bloc : 🗑 | ⊞
                col.innerHTML = `
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-danger flex-fill" onclick="supprimerLigneQuantite(this)"><i class="bi bi-trash"></i></button>
                        <button type="button" class="btn btn-success flex-fill" onclick="ajouterBloc()" title="Ajouter un formulaire"><i class="bi bi-file-earmark-plus"></i></button>
                    </div>`;
            } else {
                // Toutes les autres lignes suivantes : 🗑 pleine largeur
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
    const blocs = document.querySelectorAll('#blocs-container .bloc-commande');
    blocs.forEach((bloc, idx) => {
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
    qtyPerBloc[idx] = 1;
    blocCounter++;
    rafraichirBoutons();
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Initialiser les boutons au chargement
rafraichirBoutons();
</script>

<?php require_once 'views/layouts/footer.php'; ?>
