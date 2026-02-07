<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-tags-fill me-2"></i>Étiquettes Sartorius</h1>
        <div>
            <a href="index.php?page=ajout-reference" class="btn btn-success me-2">
                <i class="bi bi-bookmark-plus me-1"></i>Référence
            </a>
            <a href="index.php?page=nouvelle-commande" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Nouveau
            </a>
        </div>
    </div>

    <?php if(isset($_GET['error']) && $_GET['error'] === 'delete'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Erreur lors de la suppression de la commande.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?page=telecharger-pdf&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button onclick="confirmerSuppression(<?php echo $row['id']; ?>)" 
                                            class="btn btn-sm btn-outline-danger" 
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

<?php require_once 'views/layouts/footer.php'; ?>
