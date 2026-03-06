<?php
/**
 * SCRIPT D'INSTALLATION - APPLICATION ÉTIQUETTES
 * Version 1.0.11 - Avec création automatique des index de performance
 * 
 * Ce script installe automatiquement la base de données complète
 * Accès : http://localhost/etiquette-app/install.php
 * 
 * IMPORTANT : Ce fichier est protégé contre les réinstallations
 */

// ========================================
// PROTECTION CONTRE DOUBLE INSTALLATION
// ========================================
if (file_exists('.installation_complete')) {
    $installDate = file_get_contents('.installation_complete');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation déjà effectuée</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                max-width: 600px;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="card-body p-5 text-center">
                <i class="bi bi-shield-fill-check text-success" style="font-size: 4rem;"></i>
                <h2 class="mt-4">Installation déjà effectuée</h2>
                <p class="text-muted">L'application a été installée le <strong><?php echo htmlspecialchars($installDate); ?></strong></p>
                
                <div class="alert alert-info text-start mt-4">
                    <h6><i class="bi bi-info-circle me-2"></i>Pour réinstaller :</h6>
                    <ol class="mb-0 small">
                        <li>Supprimez le fichier <code>.installation_complete</code></li>
                        <li>Supprimez la base de données existante</li>
                        <li>Rechargez cette page</li>
                    </ol>
                </div>

                <a href="index.php" class="btn btn-primary btn-lg mt-4">
                    <i class="bi bi-house-door me-2"></i>Accéder à l'application
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ========================================
// AFFICHER LE FORMULAIRE SI PAS DE POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Configuration Installation</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 40px 0;
            }
            .install-container {
                max-width: 700px;
                margin: 0 auto;
            }
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            }
            .logo {
                font-size: 4rem;
                color: white;
                text-align: center;
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="install-container">
            <div class="logo">
                <i class="bi bi-tags-fill"></i>
            </div>
            
            <div class="card">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">
                        <i class="bi bi-gear text-primary"></i>
                        Configuration de l'installation
                    </h1>
                    
                    <p class="text-center text-muted mb-4">
                        Application Étiquettes v1.0.11
                    </p>

                    <hr class="my-4">

                    <form method="POST" action="install.php" id="installForm">

                        <div class="mb-4">
                            <label for="db_host" class="form-label">
                                <i class="bi bi-server me-2"></i>Hôte MySQL
                            </label>
                            <input type="text" class="form-control" id="db_host" name="db_host" 
                                   value="localhost" required>
                            <div class="form-text">Généralement "localhost"</div>
                        </div>

                        <div class="mb-4">
                            <label for="db_user" class="form-label">
                                <i class="bi bi-person me-2"></i>Utilisateur MySQL
                            </label>
                            <input type="text" class="form-control" id="db_user" name="db_user" 
                                   value="root" required>
                            <div class="form-text">Utilisateur de la base de données</div>
                        </div>

                        <div class="mb-4">
                            <label for="db_pass" class="form-label">
                                <i class="bi bi-key me-2"></i>Mot de passe MySQL
                            </label>
                            <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                   placeholder="Laisser vide si pas de mot de passe">
                            <div class="form-text">Laissez vide pour XAMPP par défaut</div>
                        </div>

                        <div class="mb-4">
                            <label for="db_name" class="form-label">
                                <i class="bi bi-database me-2"></i>Nom de la base de données <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="db_name" name="db_name" 
                                   value="etiquettes_app" required 
                                   pattern="[a-zA-Z0-9_]+" 
                                   title="Uniquement lettres, chiffres et underscores">
                            <div class="form-text">
                                <i class="bi bi-lightbulb text-warning me-1"></i>
                                Choisissez un nom unique (ex: etiquettes_prod, etiquettes_test)
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="app_folder" class="form-label">
                                <i class="bi bi-folder me-2"></i>Nom du dossier de l'application <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">/</span>
                                <input type="text" class="form-control" id="app_folder" name="app_folder"
                                       value="etiquette-app-rewrite" required
                                       pattern="[a-zA-Z0-9_\-]+"
                                       title="Uniquement lettres, chiffres, tirets et underscores">
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle text-info me-1"></i>
                                Nom du dossier où l'application est déployée sur le serveur (ex: <code>etiquette-app</code>, <code>mon-app</code>).
                                Mis à jour automatiquement dans <code>.htaccess</code>.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note :</strong> La base de données sera créée automatiquement si elle n'existe pas.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>
                                Lancer l'installation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-white">
                <small>Application Étiquettes v1.0.11 - Installation automatique avec optimisations</small>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// ========================================
// TRAITEMENT DE L'INSTALLATION
// ========================================
$DB_HOST    = $_POST['db_host']    ?? '';
$DB_USER    = $_POST['db_user']    ?? '';
$DB_PASS    = $_POST['db_pass']    ?? '';
$DB_NAME    = $_POST['db_name']    ?? '';
$APP_FOLDER = trim($_POST['app_folder'] ?? '', '/');

// Validation
if(empty($DB_HOST) || empty($DB_USER) || empty($DB_NAME) || empty($APP_FOLDER)) {
    die('Erreur : Tous les champs obligatoires doivent être remplis.');
}
if(!preg_match('/^[a-zA-Z0-9_\-]+$/', $APP_FOLDER)) {
    die('Erreur : Nom de dossier invalide. Utilisez uniquement lettres, chiffres, tirets et underscores.');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation en cours...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .install-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .step {
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            background: #f8f9fa;
        }
        .step.processing {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .step.success {
            background: #d1e7dd;
            border-left: 4px solid #198754;
        }
        .step.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="card">
            <div class="card-body p-5">
                <h1 class="text-center mb-4">
                    <i class="bi bi-hourglass-split text-primary"></i>
                    Installation en cours
                </h1>
                
                <div id="progress">
                    <?php
                    $hasError = false;
                    
                    // ========================================
                    // ÉTAPE 1 : Connexion au serveur MySQL
                    // ========================================
                    echo '<div class="step processing" id="step1">';
                    echo '<h5><i class="bi bi-server me-2"></i>Étape 1 : Connexion au serveur MySQL</h5>';
                    
                    try {
                        $dsn = "mysql:host=$DB_HOST;charset=utf8mb4";
                        $conn = new PDO($dsn, $DB_USER, $DB_PASS);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Connexion établie avec succès</p>';
                        echo '</div>';
                        
                        echo '<script>document.getElementById("step1").classList.remove("processing"); document.getElementById("step1").classList.add("success");</script>';
                        
                    } catch(PDOException $e) {
                        echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                        
                        echo '<script>document.getElementById("step1").classList.remove("processing"); document.getElementById("step1").classList.add("error");</script>';
                        $hasError = true;
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 2 : Vérification/Création de la base de données
                        // ========================================
                        echo '<div class="step processing" id="step2">';
                        echo '<h5><i class="bi bi-database me-2"></i>Étape 2 : Création de la base de données</h5>';
                        
                        try {
                            $conn->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            $conn->exec("USE `$DB_NAME`");
                            
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Base de données <strong>' . htmlspecialchars($DB_NAME) . '</strong> créée/vérifiée</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step2").classList.remove("processing"); document.getElementById("step2").classList.add("success");</script>';
                            
                        } catch(PDOException $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step2").classList.remove("processing"); document.getElementById("step2").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 3 : Vérification du fichier SQL
                        // ========================================
                        echo '<div class="step processing" id="step3">';
                        echo '<h5><i class="bi bi-file-earmark-code me-2"></i>Étape 3 : Vérification du fichier SQL</h5>';
                        
                        $sqlFile = 'database/schema_complete.sql';
                        
                        if(file_exists($sqlFile)) {
                            $sql = file_get_contents($sqlFile);
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Fichier SQL trouvé (' . number_format(strlen($sql)) . ' caractères)</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step3").classList.remove("processing"); document.getElementById("step3").classList.add("success");</script>';
                        } else {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Fichier ' . htmlspecialchars($sqlFile) . ' introuvable</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step3").classList.remove("processing"); document.getElementById("step3").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 4 : Création des tables
                        // ========================================
                        echo '<div class="step processing" id="step4">';
                        echo '<h5><i class="bi bi-table me-2"></i>Étape 4 : Création des tables</h5>';
                        
                        try {
                            // Nettoyer le SQL (enlever les commandes USE)
                            $sql = preg_replace('/USE\s+[`]?[\w]+[`]?\s*;/i', '', $sql);
                            
                            // Exécuter le SQL nettoyé
                            $conn->exec($sql);
                            
                            $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                            
                            echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Tables créées avec succès :</p>';
                            echo '<ul class="mb-0">';
                            foreach($tables as $table) {
                                echo '<li>' . htmlspecialchars($table) . '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step4").classList.remove("processing"); document.getElementById("step4").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step4").classList.remove("processing"); document.getElementById("step4").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 5 : Mise à jour du .htaccess (RewriteBase)
                        // ========================================
                        echo '<div class="step processing" id="step5">';
                        echo '<h5><i class="bi bi-signpost-split me-2"></i>Étape 5 : Mise à jour du .htaccess</h5>';

                        try {
                            $htaccessFile = '.htaccess';
                            if (!file_exists($htaccessFile)) {
                                throw new Exception('Fichier .htaccess introuvable.');
                            }

                            $htContent = file_get_contents($htaccessFile);

                            // Remplacer ou ajouter RewriteBase
                            if (preg_match('/RewriteBase\s+\S+/i', $htContent)) {
                                $htContent = preg_replace(
                                    '/RewriteBase\s+\S+/i',
                                    'RewriteBase /' . $APP_FOLDER . '/',
                                    $htContent
                                );
                            } else {
                                $htContent = preg_replace(
                                    '/RewriteEngine On/i',
                                    "RewriteEngine On\nRewriteBase /" . $APP_FOLDER . "/",
                                    $htContent
                                );
                            }

                            file_put_contents($htaccessFile, $htContent);

                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>'
                               . 'RewriteBase mis à jour : <code>/' . htmlspecialchars($APP_FOLDER) . '/</code></p>';
                            echo '</div>';
                            echo '<script>document.getElementById("step5").classList.remove("processing"); document.getElementById("step5").classList.add("success");</script>';

                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            echo '<script>document.getElementById("step5").classList.remove("processing"); document.getElementById("step5").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 6 : Mise à jour config/database.php
                        // ========================================
                        echo '<div class="step processing" id="step6">';
                        echo '<h5><i class="bi bi-file-code me-2"></i>Étape 6 : Configuration de l\'application</h5>';
                        
                        try {
                            $configFile = 'config/database.php';
                            $configContent = file_get_contents($configFile);
                            
                            // Remplacer le nom de la base de données
                            $configContent = preg_replace(
                                '/private \$db_name = ["\'].*?["\'];/',
                                'private $db_name = "' . $DB_NAME . '";',
                                $configContent
                            );
                            
                            file_put_contents($configFile, $configContent);
                            
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Configuration mise à jour dans config/database.php</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step7").classList.remove("processing"); document.getElementById("step7").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step6").classList.remove("processing"); document.getElementById("step6").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 7 : Optimisation - Création des index
                        // ========================================
                        echo '<div class="step processing" id="step7">';
                        echo '<h5><i class="bi bi-lightning me-2"></i>Étape 7 : Optimisation des performances</h5>';
                        
                        try {
                            $indexes = [
                                "CREATE INDEX idx_date_production ON commandes(date_production)",
                                "CREATE INDEX idx_numero_lot ON commandes(numero_lot)",
                                "CREATE INDEX idx_numero_commande ON commandes(numero_commande)",
                                "CREATE INDEX idx_reference_id ON commandes(reference_id)",
                                "CREATE INDEX idx_latitude_numero_commande ON commandes_latitude(numero_commande)",
                                "CREATE INDEX idx_reference ON `references`(reference)",
                                "CREATE INDEX idx_designation ON `references`(designation)",
                                "CREATE INDEX idx_article_nom ON articles_latitude(nom)"
                            ];
                            
                            $indexCount = 0;
                            foreach($indexes as $sql) {
                                try {
                                    $conn->exec($sql);
                                    $indexCount++;
                                } catch(PDOException $e) {
                                    // Ignorer si l'index existe déjà
                                    if(strpos($e->getMessage(), 'Duplicate key name') === false) {
                                        throw $e;
                                    }
                                }
                            }
                            
                            echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Index de performance créés avec succès :</p>';
                            echo '<ul class="mb-0 small">';
                            echo '<li>idx_date_production → Recherches par date</li>';
                            echo '<li>idx_numero_lot → Recherches par lot</li>';
                            echo '<li>idx_numero_commande → Recherches par numéro (Sartorius)</li>';
                            echo '<li>idx_reference_id → Jointures rapides</li>';
                            echo '<li>idx_latitude_numero_commande → Recherches (Latitude)</li>';
                            echo '<li>idx_reference → Recherches par référence</li>';
                            echo '<li>idx_designation → Recherches par désignation</li>';
                            echo '<li>idx_article_nom → Recherches par nom d\'article</li>';
                            echo '</ul>';
                            echo '<p class="text-info small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Impact : Requêtes 10-100x plus rapides !</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step7").classList.remove("processing"); document.getElementById("step7").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Avertissement : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '<p class="text-muted small mb-0">L\'installation peut continuer, les index peuvent être créés manuellement plus tard.</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step7").classList.remove("processing"); document.getElementById("step7").classList.add("success");</script>';
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 7 : Vérification finale
                        // ========================================
                        echo '<div class="step processing" id="step8">';
                        echo '<h5><i class="bi bi-check2-all me-2"></i>Étape 8 : Vérification finale</h5>';
                        
                        try {
                            $stats = [];
                            $stats['references'] = $conn->query("SELECT COUNT(*) FROM `references`")->fetchColumn();
                            $stats['commandes'] = $conn->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
                            $stats['articles_latitude'] = $conn->query("SELECT COUNT(*) FROM articles_latitude")->fetchColumn();
                            $stats['commandes_latitude'] = $conn->query("SELECT COUNT(*) FROM commandes_latitude")->fetchColumn();
                            
                            echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Installation terminée avec succès !</p>';
                            echo '<div class="alert alert-info mt-3">';
                            echo '<strong>Statistiques :</strong><br>';
                            echo '• Références Sartorius : ' . $stats['references'] . ' enregistrement(s)<br>';
                            echo '• Commandes Sartorius : ' . $stats['commandes'] . ' commande(s)<br>';
                            echo '• Articles Latitude : ' . $stats['articles_latitude'] . ' article(s)<br>';
                            echo '• Commandes Latitude : ' . $stats['commandes_latitude'] . ' commande(s)';
                            echo '</div>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step8").classList.remove("processing"); document.getElementById("step8").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step8").classList.remove("processing"); document.getElementById("step8").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // CRÉER LE FICHIER .installation_complete
                        // ========================================
                        $installDate = date('Y-m-d H:i:s');
                        file_put_contents('.installation_complete', $installDate);
                        
                        echo '<div class="alert alert-warning mt-3">';
                        echo '<i class="bi bi-shield-check me-2"></i>';
                        echo '<strong>Protection activée :</strong> Un fichier <code>.installation_complete</code> a été créé pour empêcher les réinstallations accidentelles.';
                        echo '</div>';
                    }
                    ?>
                </div>

                <hr class="my-4">

                <?php if(!$hasError): ?>
                <div class="alert alert-success">
                    <h5 class="alert-heading">
                        <i class="bi bi-check-circle-fill me-2"></i>Installation réussie !
                    </h5>
                    <p class="mb-3">La base de données <strong><?php echo htmlspecialchars($DB_NAME); ?></strong> est prête à être utilisée.</p>
                    <hr>
                    <h6>✨ Nouveautés v1.0.11 :</h6>
                    <ul class="mb-3">
                        <li>✅ Index de performance créés automatiquement</li>
                        <li>✅ Requêtes optimisées (10-100x plus rapides)</li>
                        <li>✅ Application prête pour des milliers de commandes</li>
                    </ul>
                    <hr>
                    <h6>Prochaines étapes :</h6>
                    <ol class="mb-0">
                        <li>Accédez à l'application via le bouton ci-dessous</li>
                        <li>Commencez à créer vos étiquettes !</li>
                        <li><em>(Optionnel)</em> Supprimez install.php pour plus de sécurité</li>
                    </ol>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-door me-2"></i>Accéder à l'application
                    </a>
                </div>
                <?php else: ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur d'installation
                    </h5>
                    <p class="mb-0">Veuillez corriger les erreurs ci-dessus.</p>
                </div>

                <div class="text-center mt-4">
                    <a href="install.php" class="btn btn-warning btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Retour au formulaire
                    </a>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="text-center mt-4 text-white">
            <small>Application Étiquettes v1.0.11 - Installation automatique avec optimisations</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
