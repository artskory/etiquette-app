<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ajout Référence</h1>
        <div>
            <button type="submit" form="referenceForm" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
            <a href="index.php?page=sartorius" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle me-1"></i>Annuler
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="referenceForm" action="index.php?page=creer-reference" method="POST">
                <div class="row">    
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reference" name="reference" required 
                                placeholder="Entrez la référence">
                    </div>

                    <div class="col-md-6">
                        <label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation" required 
                               placeholder="Entrez la désignation">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des références existantes -->
    <div class="card shadow-sm mt-4">
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
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Récupérer toutes les références
                        require_once 'config/database.php';
                        require_once 'models/Reference.php';
                        
                        $database = new Database();
                        $db = $database->getConnection();
                        $referenceModel = new Reference($db);
                        $stmt = $referenceModel->readAll();
                        
                        $hasReferences = false;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            $hasReferences = true;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['reference']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=editer-reference&id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-2" 
                                       title="Éditer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button onclick="confirmerSuppressionReference(<?php echo $row['id']; ?>)" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    <form id="deleteRefForm-<?php echo $row['id']; ?>" 
                                          action="index.php?page=supprimer-reference" 
                                          method="POST" 
                                          style="display: none;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        
                        <?php if(!$hasReferences): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
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

<script>
function confirmerSuppressionReference(id) {
    if(confirm('Êtes-vous sûr de vouloir supprimer cette référence ?')) {
        document.getElementById('deleteRefForm-' + id).submit();
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
