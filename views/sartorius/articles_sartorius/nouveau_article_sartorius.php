<?php require_once 'views/layouts/header.php'; ?>

<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détecter la provenance (nouvelle étiquette ou liste sartorius)
$from = $_GET['from'] ?? $_SESSION['ref_from'] ?? '';
if (!empty($_GET['from'])) {
    $_SESSION['ref_from'] = $_GET['from'];
}
$retourUrl = ($from === 'nouvelle')
    ? BASE_URL . '/sartorius/nouvelle'
    : BASE_URL . '/sartorius';

// Récupérer les valeurs du formulaire si erreur
$formData = $_SESSION['form_data'] ?? [];
$savedReference = htmlspecialchars($formData['reference'] ?? '');
$savedDesignation = htmlspecialchars($formData['designation'] ?? '');

// Charger les références
require_once 'config/database.php';
require_once 'models/Reference.php';
$database = new Database();
$db = $database->getConnection();
$referenceModel = new Reference($db);
$stmt = $referenceModel->readAll();

$referencesArray = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $referencesArray[] = $row;
}
$hasReferences = !empty($referencesArray);
?>

<div class="container mt-4 col-md-9 overview-card">
    <div class="d-flex justify-content-between align-items-center mb-4 header-table">
        <h1 class="greeting-title">Références Sartorius</h1>
        <div>
            <?php if($hasReferences): ?>
            <button type="button" id="btnSupprimerSelection" class="btn btn-warning me-2"
                    style="display:none;"
                    data-bs-toggle="modal" data-bs-target="#supprimerSelectionModal">
                <i class="bi bi-trash me-1"></i><span class="btn-text">(<span id="selectionCount">0</span>)</span>
            </button>
            <?php endif; ?>
            <a href="<?= $retourUrl ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i><span class="btn-text">Retour</span>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="referenceForm" action="<?= BASE_URL ?>/sartorius/reference/creer" method="POST">
                <?php echo CsrfToken::field(); ?>
                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <i class="bi bi-hash blue icons"></i>
                        <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference" name="reference" required
                               value="<?php echo $savedReference; ?>"
                               placeholder="Entrez la référence">
                    </div>
                    <div class="col-md-6">
                        <i class="bi bi-bookmarks blue icons"></i>
                        <label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation" required
                               value="<?php echo $savedDesignation; ?>"
                               placeholder="Entrez la désignation">
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" form="referenceForm" class="btn btn-info">
                        <i class="bi bi-check-circle me-1"></i>Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mt-4 col-md-9 card-ref">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Références existantes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th width="120" class="text-center">Actions</th>
                            <th width="40" class="text-center">
                                <input type="checkbox" class="form-check-input" id="checkAll" title="Tout sélectionner">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($referencesArray as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/sartorius/reference/<?php echo $row['id']; ?>/editer"
                                       class="btn btn-sm btn-outline-primary" title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input row-check" value="<?php echo $row['id']; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$hasReferences): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Aucune référence enregistrée</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal suppression sélection -->
<?php if($hasReferences): ?>
<div class="modal fade" id="supprimerSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Supprimer la sélection</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer les <strong id="modalSelectionCount">0</strong> référence(s) sélectionnée(s) ?</p>
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-octagon me-2"></i>
                <strong>Attention !</strong> Cette action est <strong>irréversible</strong>.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <form id="formSupprimerSelection" action="<?= BASE_URL ?>/sartorius/reference/supprimer-selection" method="POST" style="display:inline;">
                <?php echo CsrfToken::field(); ?>    
                <div id="selectionInputs"></div>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Supprimer la sélection</button>
            </form>
        </div>
    </div></div>
</div>
<?php endif; ?>

<script>
(function() {
    var checkAll  = document.getElementById('checkAll');
    var rowChecks = document.querySelectorAll('.row-check');
    var btnSel    = document.getElementById('btnSupprimerSelection');
    var cntBadge  = document.getElementById('selectionCount');
    var modalCnt  = document.getElementById('modalSelectionCount');
    var inputsDiv = document.getElementById('selectionInputs');

    function update() {
        var checked = document.querySelectorAll('.row-check:checked');
        var n = checked.length;
        if(cntBadge) cntBadge.textContent = n;
        if(modalCnt) modalCnt.textContent = n;
        if(btnSel)   btnSel.style.display = n > 0 ? 'inline-block' : 'none';
        if(checkAll) {
            checkAll.indeterminate = n > 0 && n < rowChecks.length;
            checkAll.checked = rowChecks.length > 0 && n === rowChecks.length;
        }
        if(inputsDiv) {
            inputsDiv.innerHTML = '';
            checked.forEach(function(cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                inputsDiv.appendChild(inp);
            });
        }
    }

    if(checkAll) checkAll.addEventListener('change', function() {
        rowChecks.forEach(function(cb) { cb.checked = checkAll.checked; });
        update();
    });
    rowChecks.forEach(function(cb) { cb.addEventListener('change', update); });
})();
</script>

<?php 
// Nettoyer les données du formulaire de la session après affichage
unset($_SESSION['form_data']);
// Ne pas nettoyer ref_from ici, il sera nettoyé par le controller après redirection
?>

<?php require_once 'views/layouts/footer.php'; ?>
