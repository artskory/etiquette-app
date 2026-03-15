<?php
/**
 * SCRIPT D'INSTALLATION - APPLICATION ÉTIQUETTES
 * La version est gérée automatiquement via version.php
 */

require_once __DIR__ . '/version.php';

// ── Protection contre double installation ────────────────────────────────────
if (file_exists('.installation_complete')) {
    $installDate = file_get_contents('.installation_complete');
    $appUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
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
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
            .card { border:none; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,.1); max-width:600px; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="card-body p-5 text-center">
                <i class="bi bi-shield-fill-check text-success" style="font-size:4rem;"></i>
                <h2 class="mt-4">Installation déjà effectuée</h2>
                <p class="text-muted">Installée le <strong><?= htmlspecialchars($installDate) ?></strong></p>
                <div class="alert alert-info text-start mt-4">
                    <h6><i class="bi bi-info-circle me-2"></i>Pour réinstaller :</h6>
                    <ol class="mb-0 small">
                        <li>Supprimez <code>.installation_complete</code></li>
                        <li>Supprimez la base de données existante</li>
                        <li>Rechargez cette page</li>
                    </ol>
                </div>
                <a href="<?= htmlspecialchars($appUrl) ?>" class="btn btn-primary btn-lg mt-4">
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

// ── Formulaire ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation — Application Étiquettes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/style.css">
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh; padding:40px 0; }
            .install-container { max-width:720px; margin:0 auto; }
            .card { border:none; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,.1); }
            .logo { text-align:center; margin-bottom:30px; }
            .section-title { font-weight:700; color:#444; border-bottom:2px solid #e9ecef; padding-bottom:6px; margin:28px 0 18px; font-size:.95rem; text-transform:uppercase; letter-spacing:.05em; }
            .passphrase-box { font-family:monospace; font-size:1.1rem; background:#f0f4ff; border:2px solid #b0c4ff; border-radius:8px; padding:10px 14px; cursor:pointer; user-select:all; }
            .strength-bar { height:6px; border-radius:3px; transition:all .3s; }
            #strengthLabel { font-size:.8rem; font-weight:600; }
        </style>
    </head>
    <body>
        <div class="install-container">
            <div class="logo">
                <img src="image/logo.svg" alt="logo" id="logo" style="max-height:80px;">
            </div>

            <div class="card">
                <div class="card-body p-5">
                    <h1 class="text-center mb-1"><i class="bi bi-gear text-primary"></i> Installation</h1>
                    <p class="text-center text-muted mb-4">Application Étiquettes v<?= APP_VERSION ?></p>

                    <form method="POST" action="install.php" id="installForm">

                        <!-- ── Base de données ── -->
                        <div class="section-title"><i class="bi bi-database me-2"></i>Base de données</div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Hôte MySQL</label>
                                <input type="text" class="form-control" name="db_host" value="localhost" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utilisateur</label>
                                <input type="text" class="form-control" name="db_user" value="root" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mot de passe MySQL</label>
                                <input type="password" class="form-control" name="db_pass" placeholder="Vide si XAMPP">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nom de la base de données <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="db_name" value="etiquettes_app" required
                                   pattern="[a-zA-Z0-9_]+" title="Lettres, chiffres et underscores uniquement">
                            <div class="form-text"><i class="bi bi-lightbulb text-warning me-1"></i>Nom unique, ex: <code>etiquettes_prod</code></div>
                        </div>

                        <!-- ── Application ── -->
                        <div class="section-title"><i class="bi bi-folder me-2"></i>Application</div>

                        <div class="mb-3">
                            <label class="form-label">Nom du dossier <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">/</span>
                                <input type="text" class="form-control" name="app_folder" value="etiquette-app" required
                                       pattern="[a-zA-Z0-9_\-]+" title="Lettres, chiffres, tirets et underscores">
                            </div>
                            <div class="form-text">Nom du dossier sur le serveur — mis à jour dans <code>.htaccess</code></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dépôt GitHub <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">github.com/</span>
                                <input type="text" class="form-control" name="github_repo"
                                       value="votre-compte/etiquette-app" required
                                       placeholder="compte/depot">
                            </div>
                            <div class="form-text">Utilisé par le système de mise à jour</div>
                        </div>

                        <!-- ── Mot de passe update ── -->
                        <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Mot de passe de mise à jour</div>

                        <div class="mb-2">
                            <p class="text-muted small mb-2">
                                Un mot de passe fort a été généré automatiquement sous forme de passphrase (facile à mémoriser, difficile à cracker).
                                Vous pouvez le modifier.
                            </p>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="passphrase-box flex-grow-1" id="generatedPassphrase" title="Cliquez pour copier"></div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="generatePassphrase()" title="Générer un nouveau mot de passe">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="copyPassphrase()" title="Copier">
                                    <i class="bi bi-clipboard" id="copyIcon"></i>
                                </button>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="useCustom" onchange="toggleCustom()">
                                <label class="form-check-label" for="useCustom">Utiliser mon propre mot de passe</label>
                            </div>
                        </div>

                        <div id="customPasswordBlock" style="display:none;" class="mb-3">
                            <label class="form-label">Mon mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="customPassword" name="custom_password"
                                       placeholder="Entrez votre mot de passe" oninput="checkStrength(this.value)">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('customPassword', 'eyeIcon')">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="progress mb-1" style="height:6px;">
                                    <div class="strength-bar progress-bar" id="strengthBar" style="width:0%"></div>
                                </div>
                                <span id="strengthLabel" class="text-muted">—</span>
                            </div>
                            <div class="form-text">Recommandé : plusieurs mots séparés par des tirets, ex: <code>soleil-camion-fenetre-bleu</code></div>
                        </div>

                        <!-- Champ caché qui contiendra le mot de passe final -->
                        <input type="hidden" name="update_password" id="finalPassword">

                        <div class="alert alert-info small mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            Ce mot de passe sera demandé lors des mises à jour. Conservez-le précieusement.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" onclick="setFinalPassword()">
                                <i class="bi bi-rocket-takeoff me-2"></i>Lancer l'installation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-white">
                <small>Application Étiquettes v<?= APP_VERSION ?></small>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // ── Dictionnaire de mots français ────────────────────────────────────
        const WORDS = [
            'soleil','nuage','riviere','montagne','foret','jardin','maison','fenetre',
            'bureau','cahier','stylo','crayon','tableau','chaise','table','lampe',
            'camion','voiture','bateau','avion','train','velo','fusee','rocket',
            'cerise','pomme','fraise','citron','poire','mangue','raisin','orange',
            'tigre','lapin','renard','mouton','cheval','baleine','aigle','loutre',
            'chemin','sentier','village','prairie','colline','vallee','plaine','ocean',
            'marbre','granit','ardoise','sable','argile','brique','pierre','metal',
            'lundi','mardi','avril','juillet','hiver','printemps','aurore','crepuscule'
        ];

        function generatePassphrase() {
            const words = [];
            const pool = [...WORDS];
            for (let i = 0; i < 4; i++) {
                const idx = Math.floor(Math.random() * pool.length);
                words.push(pool.splice(idx, 1)[0]);
            }
            const phrase = words.join('-');
            document.getElementById('generatedPassphrase').textContent = phrase;
            return phrase;
        }

        function copyPassphrase() {
            const text = document.getElementById('generatedPassphrase').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const icon = document.getElementById('copyIcon');
                icon.className = 'bi bi-clipboard-check';
                setTimeout(() => icon.className = 'bi bi-clipboard', 2000);
            });
        }

        function toggleCustom() {
            const block = document.getElementById('customPasswordBlock');
            block.style.display = document.getElementById('useCustom').checked ? 'block' : 'none';
        }

        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function checkStrength(val) {
            let score = 0;
            if (val.length >= 12) score++;
            if (val.length >= 20) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[-_!@#$%^&*]/.test(val)) score++;
            if ((val.match(/-/g) || []).length >= 2) score++; // passphrase bonus

            const bar   = document.getElementById('strengthBar');
            const label = document.getElementById('strengthLabel');
            const configs = [
                { pct:10, color:'bg-danger',  text:'Très faible',  cls:'text-danger'  },
                { pct:30, color:'bg-danger',  text:'Faible',        cls:'text-danger'  },
                { pct:50, color:'bg-warning', text:'Moyen',         cls:'text-warning' },
                { pct:75, color:'bg-info',    text:'Fort',          cls:'text-info'    },
                { pct:90, color:'bg-success', text:'Très fort',     cls:'text-success' },
                { pct:100,color:'bg-success', text:'Excellent',     cls:'text-success' },
            ];
            const c = configs[Math.min(score, configs.length - 1)];
            bar.style.width = c.pct + '%';
            bar.className = 'strength-bar progress-bar ' + c.color;
            label.textContent = val ? c.text : '—';
            label.className   = val ? c.cls  : 'text-muted';
        }

        function setFinalPassword() {
            const useCustom = document.getElementById('useCustom').checked;
            const custom    = document.getElementById('customPassword').value.trim();
            const generated = document.getElementById('generatedPassphrase').textContent.trim();
            document.getElementById('finalPassword').value = useCustom && custom ? custom : generated;
        }

        // Clic sur la passphrase pour copier
        document.getElementById('generatedPassphrase').addEventListener('click', copyPassphrase);

        // Générer au chargement
        generatePassphrase();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// ── Traitement POST ───────────────────────────────────────────────────────────
$DB_HOST        = trim($_POST['db_host']        ?? '');
$DB_USER        = trim($_POST['db_user']        ?? '');
$DB_PASS        = $_POST['db_pass']             ?? '';
$DB_NAME        = trim($_POST['db_name']        ?? '');
$APP_FOLDER     = trim($_POST['app_folder']     ?? '', '/');
$GITHUB_REPO    = trim($_POST['github_repo']    ?? '');
$UPDATE_PASS    = trim($_POST['update_password'] ?? '');

if (empty($DB_HOST) || empty($DB_USER) || empty($DB_NAME) || empty($APP_FOLDER) || empty($UPDATE_PASS)) {
    die('Erreur : Tous les champs obligatoires doivent être remplis.');
}
if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $APP_FOLDER)) {
    die('Erreur : Nom de dossier invalide.');
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $DB_NAME)) {
    die('Erreur : Nom de base de données invalide.');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation en cours…</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh; padding:40px 0; }
        .install-container { max-width:900px; margin:0 auto; }
        .card { border:none; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,.1); }
        .step { padding:20px; margin-bottom:15px; border-radius:10px; background:#f8f9fa; }
        .step.processing { background:#fff3cd; border-left:4px solid #ffc107; }
        .step.success    { background:#d1e7dd; border-left:4px solid #198754; }
        .step.error      { background:#f8d7da; border-left:4px solid #dc3545; }
    </style>
</head>
<body>
<div class="install-container">
    <div class="card">
        <div class="card-body p-5">
            <h1 class="text-center mb-4"><i class="bi bi-hourglass-split text-primary"></i> Installation en cours</h1>
            <div id="progress">
            <?php
            $hasError = false;

            // ── Étape 1 : Connexion MySQL ────────────────────────────────────
            echo '<div class="step processing" id="step1"><h5><i class="bi bi-server me-2"></i>Étape 1 : Connexion au serveur MySQL</h5>';
            try {
                $conn = new PDO("mysql:host=$DB_HOST;charset=utf8mb4", $DB_USER, $DB_PASS);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Connexion établie</p></div>';
                echo '<script>document.getElementById("step1").className="step success";</script>';
            } catch (PDOException $e) {
                echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                echo '<script>document.getElementById("step1").className="step error";</script>';
                $hasError = true;
            }

            if (!$hasError) {
                // ── Étape 2 : Création BDD ───────────────────────────────────
                echo '<div class="step processing" id="step2"><h5><i class="bi bi-database me-2"></i>Étape 2 : Création de la base de données</h5>';
                try {
                    $conn->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $conn->exec("USE `$DB_NAME`");
                    echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Base <strong>' . htmlspecialchars($DB_NAME) . '</strong> créée/vérifiée</p></div>';
                    echo '<script>document.getElementById("step2").className="step success";</script>';
                } catch (PDOException $e) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                    echo '<script>document.getElementById("step2").className="step error";</script>';
                    $hasError = true;
                }
            }

            if (!$hasError) {
                // ── Étape 3 : Import SQL ─────────────────────────────────────
                echo '<div class="step processing" id="step3"><h5><i class="bi bi-file-earmark-code me-2"></i>Étape 3 : Création des tables</h5>';
                $sqlFile = 'database/schema_complete.sql';
                if (!file_exists($sqlFile)) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Fichier ' . htmlspecialchars($sqlFile) . ' introuvable</p></div>';
                    echo '<script>document.getElementById("step3").className="step error";</script>';
                    $hasError = true;
                } else {
                    try {
                        $sql = preg_replace('/USE\s+[`]?[\w]+[`]?\s*;/i', '', file_get_contents($sqlFile));
                        $conn->exec($sql);
                        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Tables créées :</p><ul class="mb-0">';
                        foreach ($tables as $t) echo '<li>' . htmlspecialchars($t) . '</li>';
                        echo '</ul></div>';
                        echo '<script>document.getElementById("step3").className="step success";</script>';
                    } catch (Exception $e) {
                        echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                        echo '<script>document.getElementById("step3").className="step error";</script>';
                        $hasError = true;
                    }
                }
            }

            if (!$hasError) {
                // ── Étape 4 : .htaccess ──────────────────────────────────────
                echo '<div class="step processing" id="step4"><h5><i class="bi bi-signpost-split me-2"></i>Étape 4 : Mise à jour du .htaccess</h5>';
                try {
                    $ht = file_get_contents('.htaccess');
                    if (preg_match('/RewriteBase\s+\S+/i', $ht)) {
                        $ht = preg_replace('/RewriteBase\s+\S+/i', 'RewriteBase /' . $APP_FOLDER . '/', $ht);
                    } else {
                        $ht = preg_replace('/RewriteEngine On/i', "RewriteEngine On\nRewriteBase /" . $APP_FOLDER . "/", $ht);
                    }
                    file_put_contents('.htaccess', $ht);
                    echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>RewriteBase → <code>/' . htmlspecialchars($APP_FOLDER) . '/</code></p></div>';
                    echo '<script>document.getElementById("step4").className="step success";</script>';
                } catch (Exception $e) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                    echo '<script>document.getElementById("step4").className="step error";</script>';
                    $hasError = true;
                }
            }

            if (!$hasError) {
                // ── Étape 5 : config/database.php ───────────────────────────
                echo '<div class="step processing" id="step5"><h5><i class="bi bi-file-code me-2"></i>Étape 5 : Configuration de la base de données</h5>';
                try {
                    $dbConfig = '<?php' . "\n/**\n * Configuration de la base de données\n * Généré automatiquement par l'installateur\n */\nclass Database {\n    private \$host     = \"" . addslashes($DB_HOST) . "\";\n    private \$db_name  = \"" . addslashes($DB_NAME) . "\";\n    private \$username = \"" . addslashes($DB_USER) . "\";\n    private \$password = \"" . addslashes($DB_PASS) . "\";\n    private \$conn;\n\n    public function getConnection() {\n        \$this->conn = null;\n        try {\n            \$this->conn = new PDO(\n                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name,\n                \$this->username,\n                \$this->password\n            );\n            \$this->conn->exec(\"set names utf8\");\n            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n        } catch(PDOException \$exception) {\n            echo \"Erreur de connexion : \" . \$exception->getMessage();\n        }\n        return \$this->conn;\n    }\n}\n";
                    file_put_contents('config/database.php', $dbConfig);
                    echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>config/database.php mis à jour</p></div>';
                    echo '<script>document.getElementById("step5").className="step success";</script>';
                } catch (Exception $e) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                    echo '<script>document.getElementById("step5").className="step error";</script>';
                    $hasError = true;
                }
            }

            if (!$hasError) {
                // ── Étape 6 : .app_config ────────────────────────────────────
                echo '<div class="step processing" id="step6"><h5><i class="bi bi-shield-lock me-2"></i>Étape 6 : Création de la configuration applicative</h5>';
                try {
                    $appConfig = [
                        'app_folder'      => $APP_FOLDER,
                        'github_repo'     => $GITHUB_REPO,
                        'db_host'         => $DB_HOST,
                        'db_user'         => $DB_USER,
                        'db_pass'         => $DB_PASS,
                        'db_name'         => $DB_NAME,
                        'update_password' => password_hash($UPDATE_PASS, PASSWORD_BCRYPT),
                        'installed_at'    => date('Y-m-d H:i:s'),
                        'version'         => APP_VERSION,
                    ];
                    file_put_contents('.app_config', json_encode($appConfig, JSON_PRETTY_PRINT));
                    echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Fichier .app_config créé (mot de passe hashé en bcrypt)</p></div>';
                    echo '<script>document.getElementById("step6").className="step success";</script>';
                } catch (Exception $e) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                    echo '<script>document.getElementById("step6").className="step error";</script>';
                    $hasError = true;
                }
            }

            if (!$hasError) {
                // ── Étape 7 : Index de performance ──────────────────────────
                echo '<div class="step processing" id="step7"><h5><i class="bi bi-lightning me-2"></i>Étape 7 : Optimisation des performances</h5>';
                $indexes = [
                    "CREATE INDEX idx_date_production ON commandes(date_production)",
                    "CREATE INDEX idx_numero_lot ON commandes(numero_lot)",
                    "CREATE INDEX idx_numero_commande ON commandes(numero_commande)",
                    "CREATE INDEX idx_reference_id ON commandes(reference_id)",
                    "CREATE INDEX idx_latitude_numero_commande ON commandes_latitude(numero_commande)",
                    "CREATE INDEX idx_reference ON `references`(reference)",
                    "CREATE INDEX idx_designation ON `references`(designation)",
                    "CREATE INDEX idx_article_nom ON articles_latitude(nom)",
                ];
                foreach ($indexes as $sql) {
                    try { $conn->exec($sql); } catch (PDOException $e) {
                        if (strpos($e->getMessage(), 'Duplicate key name') === false) { /* ignorer */ }
                    }
                }
                echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Index créés</p></div>';
                echo '<script>document.getElementById("step7").className="step success";</script>';
            }

            if (!$hasError) {
                // ── Étape 8 : Vérification finale ────────────────────────────
                echo '<div class="step processing" id="step8"><h5><i class="bi bi-check2-all me-2"></i>Étape 8 : Vérification finale</h5>';
                try {
                    $refs   = $conn->query("SELECT COUNT(*) FROM `references`")->fetchColumn();
                    $cmds   = $conn->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
                    $arts   = $conn->query("SELECT COUNT(*) FROM articles_latitude")->fetchColumn();
                    $cmdLat = $conn->query("SELECT COUNT(*) FROM commandes_latitude")->fetchColumn();
                    echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Installation terminée !</p>';
                    echo '<div class="alert alert-info mt-2 mb-0 small"><strong>Statistiques :</strong><br>';
                    echo "• Références Sartorius : $refs<br>• Commandes Sartorius : $cmds<br>";
                    echo "• Articles Latitude : $arts<br>• Commandes Latitude : $cmdLat</div>";
                    echo '</div>';
                    echo '<script>document.getElementById("step8").className="step success";</script>';
                } catch (Exception $e) {
                    echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                    echo '<script>document.getElementById("step8").className="step error";</script>';
                    $hasError = true;
                }

                // Créer .installation_complete
                file_put_contents('.installation_complete', date('Y-m-d H:i:s'));
                echo '<div class="alert alert-warning mt-3"><i class="bi bi-shield-check me-2"></i><strong>Protection activée :</strong> Fichier <code>.installation_complete</code> créé.</div>';
            }
            ?>
            </div>

            <hr class="my-4">

            <?php if (!$hasError): ?>
            <div class="alert alert-success">
                <h5 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Installation réussie !</h5>
                <p>Base de données <strong><?= htmlspecialchars($DB_NAME) ?></strong> prête.</p>
                <hr>
                <p class="mb-0 small"><strong>À faire :</strong> Notez votre mot de passe de mise à jour et supprimez <code>install.php</code> une fois connecté.</p>
            </div>
            <div class="text-center">
                <a href="/<?= htmlspecialchars($APP_FOLDER) ?>/" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-door me-2"></i>Accéder à l'application
                </a>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur d'installation</h5>
                <p class="mb-0">Corrigez les erreurs ci-dessus et réessayez.</p>
            </div>
            <div class="text-center">
                <a href="install.php" class="btn btn-warning btn-lg"><i class="bi bi-arrow-left me-2"></i>Retour</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-center mt-4 text-white"><small>Application Étiquettes v<?= APP_VERSION ?></small></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
