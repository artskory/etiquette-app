<?php require_once 'views/layouts/header.php'; 

// Convertir le PDOStatement en array pour la liste
$articlesArray = [];
while ($art = $articles->fetch(PDO::FETCH_ASSOC)) {
    $articlesArray[] = $art;
}
?>

<div class="container-fluid px-4 pb-5">
    <div class="container mt-4 col-md-9 overview-card">
        <div class="d-flex justify-content-between align-items-center mb-4 header-table">
            <h1 class="greeting-title">Ajout article Latitude</h1>
            <a href="index.php?page=latitude" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>

        <div class="card-body">
            <form action="index.php?page=creer-article-latitude" method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">
                        <i class="bi bi-tag blue icons"></i>Nom de l'article <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="nom" name="nom" required 
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
        <?php if(!empty($articlesArray)): ?>
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
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($articlesArray as $art): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($art['nom']); ?></td>
                                    <td class="text-center">
                                        <a href="index.php?page=editer-article-latitude&id=<?php echo $art['id']; ?>" 
                                        class="btn btn-sm btn-outline-primary me-2" 
                                        title="Éditer">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button onclick="confirmerSuppression(<?php echo $art['id']; ?>)" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <form id="deleteForm-<?php echo $art['id']; ?>" 
                                            action="index.php?page=supprimer-article-latitude" 
                                            method="POST" 
                                            style="display: none;">
                                            <input type="hidden" name="id" value="<?php echo $art['id']; ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3 mb-0">Aucun article enregistré</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<script>
function confirmerSuppression(id) {
    if(confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        document.getElementById('deleteForm-' + id).submit();
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
