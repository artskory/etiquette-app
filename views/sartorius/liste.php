<?php 
require_once 'views/layouts/header.php'; 

$hasCommandes = false;
$commandesArray = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $hasCommandes = true;
    $commandesArray[] = $row;
}
?>

<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Étiquettes Sartorius</h1>
            <div>
                <a href="index.php?page=ajout-reference" class="btn btn-success me-2">
                    <i class="bi bi-bookmark-plus me-1"></i><span class="btn-text">Référence</span>
                </a>
                <a href="index.php?page=nouvelle-commande" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle me-1"></i><span class="btn-text">Nouveau</span>
                </a>
                <?php if($hasCommandes): ?>
                <button type="button" id="btnSupprimerSelection" class="btn btn-danger"
                        style="display:none;"
                        data-bs-toggle="modal" data-bs-target="#supprimerSelectionModal">
                    <i class="bi bi-trash me-1"></i><span class="btn-text">(<span id="selectionCount">0</span>)</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-5">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th>N° Commande</th>
                            <th>Cartons</th>
                            <th width="130" class="text-center">Actions</th>
                            <th width="40" class="text-center">
                                <input type="checkbox" class="form-check-input" id="checkAll" title="Tout sélectionner">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($commandesArray as $row):
                            $quantites = json_decode($row['quantites'] ?? '[]', true);
                            $total = 0;
                            if(is_array($quantites)) {
                                foreach($quantites as $qty) $total += (int)($qty['quantite_etiquettes'] ?? 0);
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td><?php echo htmlspecialchars($row['numero_commande']); ?></td>
                                <td><?php echo $total; ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=edition-commande&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-1" title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=telecharger-pdf&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-success" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input row-check" value="<?php echo $row['id']; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$hasCommandes): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Aucune commande enregistrée</p>
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
<div class="modal fade" id="supprimerSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Supprimer la sélection</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer les <strong id="modalSelectionCount">0</strong> commande(s) sélectionnée(s) ?</p>
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-octagon me-2"></i>
                <strong>Attention !</strong> Cette action est <strong>irréversible</strong>. Les commandes et leurs PDFs seront définitivement supprimés.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <form id="formSupprimerSelection" action="index.php?page=supprimer-selection-commandes" method="POST" style="display:inline;">
                <div id="selectionInputs"></div>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Supprimer la sélection</button>
            </form>
        </div>
    </div></div>
</div>

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

<?php require_once 'views/layouts/footer.php'; ?>
