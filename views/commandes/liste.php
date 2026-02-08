<?php require_once 'views/layouts/header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-tags-fill me-2"></i>
            Étiquettes App
        </a>
    </div>
</nav>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Étiquettes Sartorius</h1>
        <div>
            <a href="index.php?page=ajout-reference" class="btn btn-success me-2">
                <i class="bi bi-bookmark-plus me-1"></i>Référence
            </a>
            <a href="index.php?page=nouvelle-commande" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle me-1"></i>Nouveau
            </a>
            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#viderPdfModal">
                <i class="bi bi-trash me-1"></i>Vider PDF
            </button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#supprimerToutModal">
                <i class="bi bi-exclamation-triangle me-1"></i>Supprimer tout
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>N° Commande</th>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th width="200" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasCommandes = false;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                            $hasCommandes = true;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['numero_commande']); ?></td>
                                <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=edition-commande&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-2" 
                                       title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=telecharger-pdf&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-success me-2" 
                                       title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button onclick="confirmerSuppression(<?php echo $row['id']; ?>)" 
                                            class="btn btn-sm btn-outline-danger me-2" 
                                            title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    <form id="deleteForm-<?php echo $row['id']; ?>" 
                                          action="index.php?page=supprimer-commande" 
                                          method="POST" 
                                          style="display: none;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        
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
</div>

<!-- Modal Vider PDF -->
<div class="modal fade" id="viderPdfModal" tabindex="-1" aria-labelledby="viderPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="viderPdfModalLabel">
                    <i class="bi bi-trash me-2"></i>Vider les fichiers PDF
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer <strong>tous les fichiers PDF</strong> du dossier pdfs ?</p>
                <p class="text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Cette action supprimera uniquement les fichiers PDF. Les commandes en base de données seront conservées.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="index.php?page=vider-pdf" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-trash me-1"></i>Vider les PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer toutes les commandes -->
<div class="modal fade" id="supprimerToutModal" tabindex="-1" aria-labelledby="supprimerToutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="supprimerToutModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Supprimer toutes les commandes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer <strong>TOUTES les commandes</strong> ?</p>
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-octagon me-2"></i>
                    <strong>Attention !</strong> Cette action est <strong>irréversible</strong>. 
                    Toutes les commandes seront définitivement supprimées de la base de données.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="index.php?page=supprimer-tout" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Tout supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
