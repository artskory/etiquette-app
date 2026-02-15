<?php
/**
 * SCRIPT D'INSTALLATION - APPLICATION ÉTIQUETTES
 * Version 1.0.0
 * 
 * Ce script installe automatiquement la base de données complète
 * Accès : http://localhost/etiquette-app/install.php
 * 
 * IMPORTANT : Supprimez ce fichier après installation !
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'etiquette_db';

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
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
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
                    Installation Application Étiquettes
                </h1>
                
                <p class="text-center text-muted mb-4">
                    Version 1.0.0 - Installation automatique de la base de données
                </p>

                <hr class="my-4">

                <div id="installation-steps">
                    <?php
                    $steps = [];
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
                        echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur de connexion : ' . $e->getMessage() . '</p>';
                        echo '<div class="alert alert-warning mt-3">';
                        echo '<strong>Solution :</strong><br>';
                        echo '1. Vérifiez que MySQL/XAMPP est démarré<br>';
                        echo '2. Vérifiez les identifiants dans le fichier install.php<br>';
                        echo '3. Par défaut : user=root, password=(vide)';
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
                            $conn->exec("CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Base de données "' . $DB_NAME . '" créée</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step2").classList.remove("processing"); document.getElementById("step2").classList.add("success");</script>';
                            
                        } catch(PDOException $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . $e->getMessage() . '</p>';
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
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . $e->getMessage() . '</p>';
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
                            // Lire le fichier SQL
                            $sqlFile = 'database/schema_complete.sql';
                            
                            if(!file_exists($sqlFile)) {
                                throw new Exception("Fichier SQL introuvable : $sqlFile");
                            }
                            
                            $sql = file_get_contents($sqlFile);
                            
                            // Exécuter le SQL
                            $conn->exec($sql);
                            
                            // Vérifier les tables créées
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
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . $e->getMessage() . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step4").classList.remove("processing"); document.getElementById("step4").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }

                    if(!$hasError) {
                        // ========================================
                        // ÉTAPE 5 : Vérification finale
                        // ========================================
                        echo '<div class="step processing" id="step5">';
                        echo '<h5><i class="bi bi-check2-all me-2"></i>Étape 5 : Vérification finale</h5>';
                        
                        try {
                            // Compter les enregistrements
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
                            
                            echo '<script>document.getElementById("step5").classList.remove("processing"); document.getElementById("step5").classList.add("success");</script>';
                            
                        } catch(Exception $e) {
                            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Erreur : ' . $e->getMessage() . '</p>';
                            echo '</div>';
                            
                            echo '<script>document.getElementById("step5").classList.remove("processing"); document.getElementById("step5").classList.add("error");</script>';
                            $hasError = true;
                        }
                    }
                    ?>
                </div>

                <hr class="my-4">

                <?php if(!$hasError): ?>
                <div class="alert alert-success">
                    <h5 class="alert-heading">
                        <i class="bi bi-check-circle-fill me-2"></i>Installation réussie !
                    </h5>
                    <p class="mb-3">La base de données est prête à être utilisée.</p>
                    <hr>
                    <h6>Prochaines étapes :</h6>
                    <ol class="mb-0">
                        <li><strong>Supprimez ce fichier</strong> (install.php) pour des raisons de sécurité</li>
                        <li>Accédez à l'application via <a href="index.php" class="alert-link">index.php</a></li>
                        <li>Commencez à créer vos étiquettes !</li>
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
                    <p class="mb-0">Veuillez corriger les erreurs ci-dessus et rafraîchir la page.</p>
                </div>

                <div class="text-center mt-4">
                    <button onclick="location.reload()" class="btn btn-warning btn-lg">
                        <i class="bi bi-arrow-clockwise me-2"></i>Réessayer
                    </button>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="text-center mt-4 text-white">
            <small>Application Étiquettes v1.0.0 - Installation automatique</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
