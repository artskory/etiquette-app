<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Arrêt sécurité presse</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicons -->
    <link rel="icon" type="<?= BASE_URL ?>image/png" href="<?= BASE_URL ?>/image/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="<?= BASE_URL ?>image/png" href="<?= BASE_URL ?>/image/web-app-manifest-192x192.png" sizes="192x192" />
    <link rel="icon" type="<?= BASE_URL ?>image/png" href="<?= BASE_URL ?>/image/web-app-manifest-512x512.png" sizes="512x512" />
    <link rel="icon" type="<?= BASE_URL ?>image/svg+xml" href="<?= BASE_URL ?>/image/favicon.svg" />
    <link rel="shortcut icon" href="<?= BASE_URL ?>/image/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/image/apple-touch-icon.png" />
    <link rel="manifest" href="<?= BASE_URL ?>/image/site.webmanifest" />

    <style>
    /* Variables CMJN */
    :root {
    --cyan:    #00AEEF;
    --magenta: #EC008C;
    --yellow:  #FFF200;
    }

    body {
    min-height: 100vh;
    background: #dcdcdc;
    font-family: Arial, Helvetica, sans-serif;
    }

    /* Feuille */
    .sheet {
    background: white;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-width: 680px;
    }

    /* Trame demi-teinte */
    .sheet::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(#00000020 1px, transparent 1px);
    background-size: 6px 6px;
    opacity: 0.35;
    pointer-events: none;
    border-radius: inherit;
    }

    /* Repères de coupe */
    .error-wrapper {
    position: relative;
    display: inline-block;
    padding: 30px;
    }

    .crop {
    position: absolute;
    width: 40px;
    height: 40px;
    pointer-events: none;
    }

    .crop::before,
    .crop::after {
    content: "";
    position: absolute;
    background: black;
    }

    .crop::before { width: 28px; height: 2px; }
    .crop::after  { width: 2px;  height: 28px; }

    .crop.tl { top: -20px; left: -20px; }
    .crop.tl::before { left: 0;   bottom: 0; }
    .crop.tl::after  { right: 0;  top: 0;    }

    .crop.tr { top: -20px; right: -20px; }
    .crop.tr::before { right: 0; bottom: 0; }
    .crop.tr::after  { left: 0;  top: 0;    }

    .crop.bl { bottom: -20px; left: -20px; }
    .crop.bl::before { left: 0px;  top: 0px; }
    .crop.bl::after  { left: 38px; bottom: 0px; }

    .crop.br { bottom: -20px; right: -20px; }
    .crop.br::before { left: 12px; top: 0px; }
    .crop.br::after  { left: 0;    bottom: 0; }

    /* 404 */
    .error {
    position: relative;
    font-size: 50px;
    font-weight: 900;
    letter-spacing: 8px;
    }

    .layer {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    }

    .black    { position: relative; color: black; }
    .cyan     { color: var(--cyan);    animation: cyanShift    3s infinite ease-in-out; }
    .magenta  { color: var(--magenta); animation: magentaShift 3s infinite ease-in-out; }
    .yellow   { color: var(--yellow);  animation: yellowShift  3s infinite ease-in-out; }

    @keyframes cyanShift {
    0%   { transform: translate(0,0);     }
    40%  { transform: translate(-4px,-3px); }
    70%  { transform: translate(3px,2px);  }
    100% { transform: translate(0,0);     }
    }

    @keyframes magentaShift {
    0%   { transform: translate(0,0);    }
    40%  { transform: translate(4px,3px);  }
    70%  { transform: translate(-3px,-2px); }
    100% { transform: translate(0,0);    }
    }

    @keyframes yellowShift {
    0%   { transform: translate(0,0);    }
    40%  { transform: translate(-2px,4px); }
    70%  { transform: translate(2px,-3px); }
    100% { transform: translate(0,0);    }
    }

    /* Barre FOGRA */
    .fogra-bar {
    display: flex;
    gap: 3px;
    }

    .patch {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    }

    .patch-c  { background: var(--cyan);    }
    .patch-m  { background: var(--magenta); }
    .patch-y  { background: var(--yellow);  }
    .patch-k  { background: #000; }
    .patch-g1 { background: #eee; }
    .patch-g2 { background: #ccc; }
    .patch-g3 { background: #999; }
    .patch-g4 { background: #666; }
    .patch-g5 { background: #333; }
    .patch-g6 { background: #111; }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center min-vh-100">

  <div class="sheet rounded px-4 px-md-5 pt-5 pb-5 text-center">

    <!-- 404 avec repères -->
    <div class="error-wrapper">
      <div class="crop tl"></div>
      <div class="crop tr"></div>
      <div class="crop bl"></div>
      <div class="crop br"></div>

      <div class="error">
        <div class="layer cyan">Erreur<br>403</div>
        <div class="layer magenta">Erreur<br>403</div>
        <div class="layer yellow">Erreur<br>403</div>
        <div class="black">Erreur<br>403</div>
      </div>
    </div>

    <!-- Titre -->
    <h1 class="mt-4 fw-bold fs-3">ARRÊT SÉCURITÉ PRESSE</h1>

    <!-- Description -->
    <p class="text-secondary fs-5 mt-2">
      Cette page est protégée par le système de sécurité de l'atelier.<br>
      L'opérateur n'a pas autorisé ce tirage.
    </p>

    <!-- Bouton Bootstrap -->
    <div class="d-flex justify-content-center gap-3">
        <a href="<?= BASE_URL ?>/" class="btn btn-info">
            <i class="bi bi-house me-1"></i>Accueil
        </a>
        <a href="<?= BASE_URL ?>/sartorius" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>

    <!-- Barre FOGRA -->
    <div class="fogra-bar justify-content-center mt-4">
      <div class="patch patch-c"></div>
      <div class="patch patch-m"></div>
      <div class="patch patch-y"></div>
      <div class="patch patch-k"></div>
      <div class="patch patch-g1"></div>
      <div class="patch patch-g2"></div>
      <div class="patch patch-g3"></div>
      <div class="patch patch-g4"></div>
      <div class="patch patch-g5"></div>
      <div class="patch patch-g6"></div>
    </div>

  </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
