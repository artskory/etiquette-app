<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-4 col-md-9">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nouvelle étiquette Latitude</h1>
        <div>
            <button type="submit" form="latitudeForm" class="btn btn-success me-2">
                <i class="bi bi-check-circle me-1"></i>Sauvegarder
            </button>
            <a href="index.php?page=latitude" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Annuler
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="latitudeForm" action="index.php?page=latitude-creer" method="POST">
                <!-- N° Commande -->
                <div class="mb-4">
                    <label for="numero_commande" class="form-label">
                        <i class="bi bi-hash blue icons"></i>N° Commande <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="numero_commande" name="numero_commande" required 
                           placeholder="Ex: 2510-4028">
                </div>

                <hr class="my-4">

                <div id="articlesContainer">
                    <!-- Ligne article initiale -->
                    <div class="article-row mb-3" data-row-index="0">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Article <span class="text-danger">*</span></label>
                                <select class="form-select" name="articles[0][type]" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Carte postale">Carte postale</option>
                                    <option value="Carte stickers">Carte stickers</option>
                                    <option value="Set de table">Set de table</option>
                                    <option value="Livre">Livre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité d'article <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="articles[0][quantite]" min="1" required 
                                       placeholder="Ex: 900">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nombre d'exemplaire <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="articles[0][nombre_cartons]" min="1" required 
                                       placeholder="Ex: 25">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" onclick="ajouterLigneArticle()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.article-row {
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

.article-row.removing {
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
let articleRowIndex = 1;

function ajouterLigneArticle() {
    const container = document.getElementById('articlesContainer');
    
    // Créer la nouvelle ligne
    const newRow = document.createElement('div');
    newRow.className = 'article-row mb-3';
    newRow.setAttribute('data-row-index', articleRowIndex);
    
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Article <span class="text-danger">*</span></label>
                <select class="form-select" name="articles[${articleRowIndex}][type]" required>
                    <option value="">Sélectionner...</option>
                    <option value="Carte postale">Carte postale</option>
                    <option value="Carte stickers">Carte stickers</option>
                    <option value="Set de table">Set de table</option>
                    <option value="Livre">Livre</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantité d'article <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="articles[${articleRowIndex}][quantite]" min="1" required placeholder="Ex: 900">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nombre d'exemplaire <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="articles[${articleRowIndex}][nombre_cartons]" min="1" required placeholder="Ex: 25">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger w-100" onclick="supprimerLigneArticle(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    articleRowIndex++;
}

function supprimerLigneArticle(button) {
    const row = button.closest('.article-row');
    
    // Animation de suppression
    row.classList.add('removing');
    
    // Supprimer après l'animation
    setTimeout(() => {
        row.remove();
    }, 300);
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
