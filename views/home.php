<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquettes de colisages</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <!-- custom-alerts -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom-alerts.css">
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/image/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/image/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= BASE_URL ?>/image/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= BASE_URL ?>/image/android-chrome-512x512.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/image/favicon-16x16.png">
    <link rel="manifest" href="<?= BASE_URL ?>/image/site.webmanifest"> 
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
                        <a href="<?= BASE_URL ?>/sartorius" class="card-link">    
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
                    <div class="card h-100 shadow-sm hover-shadow">
                        <a href="<?= BASE_URL ?>/latitude" class="card-link">    
                            <div class="card-body text-center p-5">
                                <div class="mb-4">
                                    <i class="bi bi-tag-fill text-primary" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="card-title mb-3">Latitude</h3>
                                <p class="card-text text-muted mb-4">Gestion des étiquettes Latitude</p>
                            </div>
                        </a>
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

</head>
<body>

<?php require_once 'views/layouts/footer.php'; ?>
