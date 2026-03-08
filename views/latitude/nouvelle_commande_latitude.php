<?php 
require_once 'views/layouts/header.php'; 

// Convertir les articles en array
$articlesArray = [];
if(isset($articles)) {
    while ($art = $articles->fetch(PDO::FETCH_ASSOC)) {
        $articlesArray[] = $art;
    }
}
?>
    <div class="container-fluid px-4 pb-5">
        <div class="container mt-4 col-md-9 overview-card">
            <div class="d-flex justify-content-between align-items-center mb-4 header-table">
                <h1 class="greeting-title">Nouvelle étiquette Latitude</h1>
                <div>
                    <a href="<?= BASE_URL ?>/latitude/article/nouveau?from=nouvelle" class="btn btn-success me-2">
                        <i class="bi bi-tag me-1"></i>Articles
                    </a>
                    <a href="<?= BASE_URL ?>/latitude" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Annuler
                    </a>
                </div>
            </div>

            <div class="card mt-5">
                <form id="latitudeForm" action="<?= BASE_URL ?>/latitude/creer" method="POST">
                    <?php echo CsrfToken::field(); ?>
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
                                    <select class="form-select article-select" name="articles[0][type]" required>
                                        <option value="">Sélectionner...</option>
                                        <?php if(!empty($articlesArray)): ?>
                                            <?php foreach($articlesArray as $art): ?>
                                                <option value="<?php echo htmlspecialchars($art['nom']); ?>">
                                                    <?php echo htmlspecialchars($art['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Aucun article - Créez-en un !</option>
                                        <?php endif; ?>
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
                                    <button type="button" class="btn btn-primary w-100 btn-add-first" onclick="ajouterLigneArticle()">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" form="latitudeForm" class="btn btn-info me-2">
                            <i class="bi bi-check-circle me-1"></i>Sauvegarder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Stocker les articles en JSON pour JavaScript -->
<script id="articlesData" type="application/json">
<?php echo json_encode($articlesArray); ?>
</script>

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
// Charger les articles depuis le JSON
const articlesData = JSON.parse(document.getElementById('articlesData').textContent || '[]');
let articleRowIndex = 1;

// Fonction pour générer les options du select
function generateArticleOptions() {
    if (articlesData.length === 0) {
        return '<option value="" disabled>Aucun article - Créez-en un !</option>';
    }
    
    let options = '<option value="">Sélectionner...</option>';
    articlesData.forEach(article => {
        options += `<option value="${article.nom}">${article.nom}</option>`;
    });
    return options;
}

function ajouterLigneArticle() {
    const container = document.getElementById('articlesContainer');
    
    // Masquer le bouton + de la première ligne
    const firstAddBtn = document.querySelector('.btn-add-first');
    if(firstAddBtn) {
        firstAddBtn.style.display = 'none';
    }
    
    // Créer la nouvelle ligne
    const newRow = document.createElement('div');
    newRow.className = 'article-row mb-3';
    newRow.setAttribute('data-row-index', articleRowIndex);
    
    newRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Article <span class="text-danger">*</span></label>
                <select class="form-select article-select" name="articles[${articleRowIndex}][type]" required>
                    ${generateArticleOptions()}
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
            <div class="col-md-3 d-flex gap-2">
                <button type="button" class="btn btn-primary flex-fill" onclick="ajouterLigneArticle()">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button type="button" class="btn btn-danger flex-fill" onclick="supprimerLigneArticle(this)">
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
    const container = document.getElementById('articlesContainer');
    
    // Animation de suppression
    row.classList.add('removing');
    
    // Supprimer après l'animation
    setTimeout(() => {
        row.remove();
        
        // Si il ne reste qu'une seule ligne, réafficher le bouton + de la première ligne
        const remainingRows = container.querySelectorAll('.article-row');
        if(remainingRows.length === 1) {
            const firstAddBtn = document.querySelector('.btn-add-first');
            if(firstAddBtn) {
                firstAddBtn.style.display = 'block';
            }
        }
    }, 300);
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
