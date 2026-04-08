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
                <a href="<?= BASE_URL ?>/sartorius/nouvelle" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle me-1"></i><span class="btn-text">Nouveau</span>
                </a>
                <?php if($hasCommandes): ?>
                <button type="button" id="btnCombiner" class="btn btn-info me-2"
                        style="display:none;">
                    <i class="bi bi-collection me-1"></i><span class="btn-text">Combiner (<span id="selectionCount">0</span>)</span>
                </button>
                <button type="button" id="btnSupprimerSelection" class="btn btn-danger"
                        style="display:none;"
                        data-bs-toggle="modal" data-bs-target="#supprimerSelectionModal">
                    <i class="bi bi-trash"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if($hasCommandes): ?>
        <form id="formCombiner" action="<?= BASE_URL ?>/sartorius/combiner" method="POST" style="display:none;">
            <?php echo CsrfToken::field(); ?>
            <div id="combineInputs"></div>
        </form>
        <?php endif; ?>

        <div class="card mt-5">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
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
                            $etiquettes = json_decode($row['etiquettes'] ?? 'null', true);
                            if (!$etiquettes || !is_array($etiquettes)) {
                                $quantites = json_decode($row['quantites'] ?? '[]', true);
                                $etiquettes = [[
                                    'reference'       => $row['reference'] ?? '',
                                    'designation'     => $row['designation'] ?? '',
                                    'numero_commande' => $row['numero_commande'],
                                    'quantites'       => is_array($quantites) ? $quantites : [],
                                ]];
                            }
                            $firstEtiq = $etiquettes[0];
                            $nbBlocs   = count($etiquettes);
                            $total = 0;
                            foreach ($etiquettes as $etiq) {
                                foreach (($etiq['quantites'] ?? []) as $qty) {
                                    $total += (int)($qty['quantite_etiquettes'] ?? 0);
                                }
                            }
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        $refs    = array_values(array_filter(array_map(fn($e) => $e['reference'] ?? '', $etiquettes)));
                                        $visible = array_slice($refs, 0, 2);
                                        $extra   = count($refs) - 2;
                                        echo htmlspecialchars(implode(' / ', $visible));
                                        if ($extra > 0) echo ' <span class="badge bg-secondary">+' . $extra . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $cmds    = array_values(array_filter(array_map(fn($e) => $e['numero_commande'] ?? '', $etiquettes)));
                                        $visible = array_slice($cmds, 0, 2);
                                        $extra   = count($cmds) - 2;
                                        echo htmlspecialchars(implode(' / ', $visible));
                                        if ($extra > 0) echo ' <span class="badge bg-secondary">+' . $extra . '</span>';
                                    ?>
                                </td>
                                <td><?php echo $total; ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/sartorius/commande/<?php echo $row['id']; ?>/editer"
                                       class="btn btn-sm btn-outline-primary me-1" title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/sartorius/commande/<?php echo $row['id']; ?>/telecharger"
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
                                <td colspan="5" class="text-center text-muted py-4">
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
    <!-- PAGINATION - À ajouter juste avant le </div> de fermeture -->
    <?php if($totalPages > 1): ?>
    <nav aria-label="Pagination des commandes" class="mt-4">
        <ul class="pagination justify-content-center">
            
            <!-- Bouton Page précédente -->
            <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= BASE_URL ?>/sartorius?p=<?= $page - 1 ?>" aria-label="Page précédente">
                        <span aria-hidden="true">Précédent</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Précédent</span>
                </li>
            <?php endif; ?>

            <!-- Numéros de pages -->
            <?php
            // Afficher max 5 pages autour de la page courante
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            // Ajuster si on est au début ou à la fin
            if($page <= 3) {
                $endPage = min(5, $totalPages);
            }
            if($page > $totalPages - 3) {
                $startPage = max(1, $totalPages - 4);
            }
            
            for($i = $startPage; $i <= $endPage; $i++):
            ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/sartorius?p=<?= $i ?>">
                        <?= $i ?>
                        <?php if($i === $page): ?>
                            <span class="visually-hidden">(page courante)</span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Bouton Page suivante -->
            <?php if($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= BASE_URL ?>/sartorius?p=<?= $page + 1 ?>" aria-label="Page suivante">
                        <span aria-hidden="true">Suivant</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Suivant</span>
                </li>
            <?php endif; ?>

            <!-- Bouton Dernière page -->
            
        </ul>
    </nav>
    <?php endif; ?>
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
            <form id="formSupprimerSelection" action="<?= BASE_URL ?>/sartorius/supprimer-selection" method="POST" style="display:inline;">
                <?php echo CsrfToken::field(); ?>
                <div id="selectionInputs"></div>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Supprimer la sélection</button>
            </form>
        </div>
    </div></div>
</div>

<script>
(function () {
    var checkAll      = document.getElementById('checkAll');
    var rowChecks     = document.querySelectorAll('.row-check');
    var btnCombiner   = document.getElementById('btnCombiner');
    var btnSupprimer  = document.getElementById('btnSupprimerSelection');
    var cntBadge      = document.getElementById('selectionCount');
    var modalCnt      = document.getElementById('modalSelectionCount');
    var selInputs     = document.getElementById('selectionInputs');
    var combInputs    = document.getElementById('combineInputs');
    var formCombiner  = document.getElementById('formCombiner');

    function update() {
        var checked = document.querySelectorAll('.row-check:checked');
        var n = checked.length;

        // Compteur affiché dans le bouton Combiner
        if (cntBadge) cntBadge.textContent = n;
        if (modalCnt) modalCnt.textContent = n;

        // Afficher / masquer les deux boutons ensemble
        var show = n > 0 ? 'inline-block' : 'none';
        if (btnCombiner)  btnCombiner.style.display  = show;
        if (btnSupprimer) btnSupprimer.style.display = show;

        // Indeterminate sur le checkAll
        if (checkAll) {
            checkAll.indeterminate = n > 0 && n < rowChecks.length;
            checkAll.checked = rowChecks.length > 0 && n === rowChecks.length;
        }

        // Synchroniser les deux formulaires avec les mêmes IDs
        [selInputs, combInputs].forEach(function (div) {
            if (!div) return;
            div.innerHTML = '';
            checked.forEach(function (cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                div.appendChild(inp);
            });
        });
    }

    if (checkAll) checkAll.addEventListener('change', function () {
        rowChecks.forEach(function (cb) { cb.checked = checkAll.checked; });
        update();
    });
    rowChecks.forEach(function (cb) { cb.addEventListener('change', update); });

    if (btnCombiner) btnCombiner.addEventListener('click', function () {
        if (formCombiner) formCombiner.submit();
    });
})();
</script>

<?php require_once 'views/layouts/footer.php'; ?>
