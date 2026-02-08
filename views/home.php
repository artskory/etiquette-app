<?php require_once 'views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <h1 class="display-4 mb-4">Étiquettes de colisages</h1>
                <p class="lead text-muted">Gestion des étiquettes Sartorius et Latitude</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <a href="index.php?page=sartorius" class="card-link">    
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class="bi bi-tag-fill text-primary" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="card-title mb-3">Sartorius</h3>
                                <p class="card-text text-muted mb-4">Gestion des étiquettes Sartorius</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-tag text-secondary" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="card-title mb-3">Latitude</h3>
                            <p class="card-text text-muted mb-4">Gestion des étiquettes Latitude</p>
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-hourglass-split me-2"></i>Bientôt disponible
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: box-shadow 0.3s ease-in-out;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
