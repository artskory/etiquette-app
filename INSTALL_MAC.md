# Installation sur Mac avec XAMPP

## Problème de génération PDF sur Mac

Si la génération de PDF ne fonctionne pas sur Mac avec XAMPP, suivez ces étapes :

### 1. Vérifier les permissions du dossier pdfs

Ouvrez Terminal et naviguez vers votre dossier de l'application :

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/etiquette-app
```

Donnez les permissions complètes au dossier pdfs :

```bash
chmod -R 777 pdfs
```

### 2. Exécuter le diagnostic

Ouvrez votre navigateur et accédez à :

```
http://localhost/etiquette-app/diagnostic_pdf.php
```

Ce script va vérifier :
- Version de PHP
- Permissions du dossier pdfs
- Présence de FPDF
- Présence de l'icône factory
- Création d'un PDF de test

### 3. Corrections courantes

#### Problème : Dossier pdfs n'existe pas
```bash
mkdir pdfs
chmod 777 pdfs
```

#### Problème : Permissions insuffisantes
```bash
sudo chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/etiquette-app/pdfs
```

#### Problème : Utilisateur Apache n'a pas les droits
```bash
sudo chown -R daemon:daemon pdfs
chmod -R 777 pdfs
```

### 4. Vérifier les logs XAMPP

Les logs d'erreur PHP se trouvent ici :
```
/Applications/XAMPP/xamppfiles/logs/php_error_log
```

Ouvrez-les pour voir les erreurs détaillées :
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
```

### 5. Configuration PHP (php.ini)

Assurez-vous que ces paramètres sont activés dans `/Applications/XAMPP/xamppfiles/etc/php.ini` :

```ini
display_errors = On
error_reporting = E_ALL
log_errors = On
error_log = /Applications/XAMPP/xamppfiles/logs/php_error_log
```

Redémarrez Apache après modification :
```bash
/Applications/XAMPP/xamppfiles/xampp restart
```

### 6. Test manuel de création de PDF

Créez un fichier `test_pdf.php` à la racine :

```php
<?php
require_once 'lib/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Test PDF Mac');

// Créer le dossier si nécessaire
if(!is_dir('pdfs')) {
    mkdir('pdfs', 0777, true);
}

$pdf->Output('F', 'pdfs/test_mac.pdf');

if(file_exists('pdfs/test_mac.pdf')) {
    echo "PDF créé avec succès !";
    echo "<br><a href='pdfs/test_mac.pdf'>Télécharger le PDF</a>";
} else {
    echo "Erreur : PDF non créé";
}
?>
```

Accédez à `http://localhost/etiquette-app/test_pdf.php`

### 7. Problèmes spécifiques Mac

#### SELinux / SIP (System Integrity Protection)
Sur certains Mac, SIP peut bloquer l'écriture. Vérifiez :
```bash
csrutil status
```

#### Permissions du dossier XAMPP
```bash
sudo chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/etiquette-app
```

### 8. Alternative : Utiliser MAMP

Si XAMPP continue à poser problème, essayez MAMP :
1. Téléchargez MAMP : https://www.mamp.info/
2. Placez l'application dans `/Applications/MAMP/htdocs/etiquette-app`
3. Les permissions sont généralement mieux gérées avec MAMP

### 9. Vérification finale

Une fois les corrections appliquées :

1. Accédez à `http://localhost/etiquette-app/diagnostic_pdf.php`
2. Tous les tests doivent être verts (✓)
3. Créez une commande test dans l'application
4. Vérifiez que le PDF est généré dans le dossier `pdfs/`

### Support

Si le problème persiste après toutes ces étapes, vérifiez :
- La version de PHP (doit être >= 7.4)
- Les logs d'erreur XAMPP
- Que GD library est installée : `php -m | grep gd`

## Permissions recommandées

```bash
# Structure de permissions
etiquette-app/          755
├── pdfs/              777  ← IMPORTANT
├── assets/            755
├── config/            755
├── controllers/       755
├── lib/               755
├── models/            755
├── views/             755
└── *.php              644
```
