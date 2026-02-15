# 🗑️ FICHIERS SUPPRIMÉS - Version 1.0.1

## Fichiers de développement supprimés

Ces fichiers étaient utilisés pour le développement et le débogage.
Ils ne sont plus nécessaires en version de production.

### Scripts de test (12 fichiers)
- ❌ `test_create_commande.php` - Test création commandes
- ❌ `test_pdf.php` - Test génération PDF
- ❌ `test_pdf_latitude.php` - Test PDF Latitude
- ❌ `test_pdf_sartorius.php` - Test PDF Sartorius

### Scripts de diagnostic (4 fichiers)
- ❌ `check_database.php` - Vérification BDD
- ❌ `diagnostic_pdf.php` - Diagnostic PDF
- ❌ `diagnostic_quick.php` - Diagnostic rapide
- ❌ `show_errors.php` - Affichage erreurs PHP

### Scripts de migration (4 fichiers)
- ❌ `migrate_sartorius.php` - Migration v0.3 → v0.4
- ❌ `migration_info.php` - Info migration
- ❌ `fix_latitude_json.php` - Correction JSON Latitude
- ❌ `install_articles_latitude.php` - Installation articles (obsolète)
- ❌ `install_latitude.php` - Installation Latitude (obsolète)

### Anciens schémas SQL (5 fichiers)
Dans `database/` :
- ❌ `schema.sql` - Ancien schéma Sartorius
- ❌ `latitude_schema.sql` - Ancien schéma Latitude
- ❌ `articles_latitude_schema.sql` - Ancien schéma articles
- ❌ `migrate_to_json.sql` - Script migration JSON
- ❌ `update_schema.sql` - Mise à jour schéma

## Fichiers conservés

### Installation
- ✅ `install.php` - Installation automatique (À SUPPRIMER après installation)

### Base de données
- ✅ `database/schema_complete.sql` - **Schéma complet unique**

### Documentation
- ✅ `README.md` - Documentation principale
- ✅ `INSTALLATION.md` - Guide d'installation
- ✅ `QUICKSTART.md` - Démarrage rapide
- ✅ `CLEANUP.md` - Ce fichier

### Application
- ✅ Tous les fichiers de l'application (MVC)
- ✅ Bibliothèques (FPDF, etc.)
- ✅ Assets (CSS, JS, images)

## Résultat

**AVANT le nettoyage** : 50+ fichiers  
**APRÈS le nettoyage** : ~35 fichiers essentiels

✨ Application plus propre et professionnelle !

---

**Note** : Ces suppressions sont définitives. Si vous avez besoin de ces fichiers pour le développement, consultez les versions précédentes (v0.x).
