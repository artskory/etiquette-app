<?php
/**
 * SCRIPT D'INSTALLATION - APPLICATION ÉTIQUETTES
 * Version 1.0.8
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
                        Application Étiquettes v1.0.8
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
                                   value="etiquette_db" required 
                                   pattern="[a-zA-Z0-9_]+" 
                                   placeholder="etiquette_db">
                            <div class="form-text">Uniquement lettres, chiffres et underscores</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Important :</strong> Une nouvelle base de données sera créée avec ce nom. 
                            Assurez-vous que ce nom n'existe pas déjà.
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>Lancer l'installation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-white">
                <small>Application Étiquettes - Installation automatique</small>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// ========================================
// TRAITEMENT DU FORMULAIRE
// ========================================
$DB_HOST = $_POST['db_host'] ?? 'localhost';
$DB_USER = $_POST['db_user'] ?? 'root';
$DB_PASS = $_POST['db_pass'] ?? '';
$DB_NAME = $_POST['db_name'] ?? 'etiquette_db';

// Validation du nom de base
if (!preg_match('/^[a-zA-Z0-9_]+$/', $DB_NAME)) {
    die('Erreur : Nom de base de données invalide');
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Application Étiquettes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .install-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .step {
            padding: 20px;
            border-left: 4px solid #e9ecef;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .step.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .step.processing {
            border-left-color: #0061f2;
            background: #cfe2ff;
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
                    <i class="bi bi-download text-primary"></i>
                    Installation en cours...
                </h1>
                
                <p class="text-center text-muted mb-4">
                    Base de données : <strong><?php echo htmlspecialchars($DB_NAME); ?></strong>
                </p>

                <hr class="my-4">

                <div id="installation-steps">
                    <?php
                    $hasError = false;

                    // ========================================
                    // ÉTAPE 1 : Connexion au serveur MySQL
                    // ========================================
                    echo '<div class="step processing" id="step1">';
                    echo '<h5><i class="bi bi-database me-2"></i>Étape 1 : Connexion au serveur MySQL</h5>';
                    
                    try {
                        $conn = new PDO("mysql:host=$DB_HOST", $DB_USER, $DB_PASS);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Connexion réussie au serveur MySQL</p>';
                        echo '</div>';
                        
                        echo '<script>document.getElementById("step1").classList.remove("processing"); document.getElementById("step1").classList.add("success");</script>';
                        
                    } catch(PDOException $e) {
                        echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur de connexion : ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '<div class="alert alert-warning mt-3">';
                        echo '<strong>Solution :</strong><br>';
                        echo '1. Vérifiez que MySQL/XAMPP est démarré<br>';
                        echo '2. Vérifiez les identifiants saisis<br>';
                        echo '3. <a href="install.php">Retour au formulaire</a>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<script>document.getElementById("step1").classList.remove("processing"); document.getElementById("step1").classList.add("error");</script>';
                        $hasError = true;
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 2 : Création de la base de données
                        // ========================================
                        echo '<div class="step processing" id="step2">';
                        echo '<h5><i class="bi bi-folder-plus me-2"></i>Étape 2 : Création de la base de données</h5>';
                        
                        try {
                            $conn->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Base de données "' . htmlspecialchars($DB_NAME) . '" créée</p>';
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
                        // ÉTAPE 3 : Connexion à la base de données
                        // ========================================
                        echo '<div class="step processing" id="step3">';
                        echo '<h5><i class="bi bi-plug me-2"></i>Étape 3 : Connexion à la base de données</h5>';
                        
                        try {
                            $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Connecté à la base de données</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step3").classList.remove("processing"); document.getElementById("step3").classList.add("success");</script>';
                            
                        } catch(PDOException $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
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
                            $sqlFile = 'database/schema_complete.sql';
                            
                            if(!file_exists($sqlFile)) {
                                throw new Exception("Fichier SQL introuvable : $sqlFile");
                            }
                            
                            $sql = file_get_contents($sqlFile);
                            
                            // ========================================
                            // NETTOYER LE SQL : Retirer CREATE DATABASE et USE
                            // ========================================
                            // Problème : Le fichier SQL contient CREATE DATABASE et USE qui peuvent pointer vers une autre base
                            // Solution : On retire ces lignes car on a déjà créé et sélectionné la bonne base
                            
                            // Supprimer les lignes CREATE DATABASE
                            $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
                            
                            // Supprimer les lignes USE
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
                        // ÉTAPE 5 : Mise à jour config/database.php
                        // ========================================
                        echo '<div class="step processing" id="step5">';
                        echo '<h5><i class="bi bi-file-code me-2"></i>Étape 5 : Configuration de l\'application</h5>';
                        
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
                        // ÉTAPE 6 : Vérification finale
                        // ========================================
                        echo '<div class="step processing" id="step6">';
                        echo '<h5><i class="bi bi-check2-all me-2"></i>Étape 6 : Vérification finale</h5>';
                        
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
                            
                            echo '<script>document.getElementById("step6").classList.remove("processing"); document.getElementById("step6").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step6").classList.remove("processing"); document.getElementById("step6").classList.add("error");</script>';
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
            <small>Application Étiquettes v1.0.8 - Installation automatique</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
