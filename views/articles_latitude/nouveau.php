<?php require_once 'views/layouts/header.php'; 

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les valeurs du formulaire si erreur
$formData = $_SESSION['form_data'] ?? [];
$savedNom = htmlspecialchars($formData['nom'] ?? '');

$articlesArray = [];
while ($art = $articles->fetch(PDO::FETCH_ASSOC)) {
    $articlesArray[] = $art;
}
$hasArticles = !empty($articlesArray);
?>

<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Articles Latitude</h1>
            <div>
                <?php if($hasArticles): ?>
                <button type="button" id="btnSupprimerSelection" class="btn btn-warning me-2"
                        style="display:none;"
                        data-bs-toggle="modal" data-bs-target="#supprimerSelectionModal">
                    <i class="bi bi-trash me-1"></i><span class="btn-text">(<span id="selectionCount">0</span>)</span>
                </button>
                <?php endif; ?>
                <a href="index.php?page=latitude" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i><span class="btn-text">Retour</span>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="index.php?page=creer-article-latitude" method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            <i class="bi bi-tag blue icons"></i>Nom de l'article <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nom" name="nom" required 
                               value="<?php echo $savedNom; ?>"
                               placeholder="Ex: Carte postale, Flyer A5, Brochure...">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Créer l'article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if($hasArticles): ?>
    <div class="container mt-4 col-md-9 card-ref">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Articles existants</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th width="120" class="text-center">Actions</th>
                                <th width="40" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="checkAll" title="Tout sélectionner">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($articlesArray as $art): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($art['nom']); ?></td>
                                    <td class="text-center">
                                        <a href="index.php?page=editer-article-latitude&id=<?php echo $art['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Éditer">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input row-check" value="<?php echo $art['id']; ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="container mt-4 col-md-9">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3 mb-0">Aucun article enregistré</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal suppression sélection -->
<?php if($hasArticles): ?>
<div class="modal fade" id="supprimerSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Supprimer la sélection</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer les <strong id="modalSelectionCount">0</strong> article(s) sélectionné(s) ?</p>
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-octagon me-2"></i>
                <strong>Attention !</strong> Cette action est <strong>irréversible</strong>.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <form id="formSupprimerSelection" action="index.php?page=supprimer-selection-articles-latitude" method="POST" style="display:inline;">
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
?>

<?php require_once 'views/layouts/footer.php'; ?>
