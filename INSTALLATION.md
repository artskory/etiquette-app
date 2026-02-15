# 📦 GUIDE D'INSTALLATION - APPLICATION ÉTIQUETTES
**Version 1.0.0**

---

## 🚀 Installation rapide (Recommandée)

### Prérequis
- XAMPP, WAMP, MAMP ou serveur Apache/MySQL
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur

### Étapes

1. **Décompresser l'archive**
   ```
   Extraire etiquette-app-v1.0.0.zip dans :
   - Windows : C:\xampp\htdocs\
   - Mac : /Applications/MAMP/htdocs/
   - Linux : /var/www/html/
   ```

2. **Démarrer MySQL**
   - Ouvrir XAMPP Control Panel
   - Démarrer Apache et MySQL

3. **Lancer l'installation**
   ```
   Navigateur : http://localhost/etiquette-app/install.php
   ```

4. **Suivre l'assistant**
   - L'installation se fait automatiquement
   - Toutes les tables sont créées
   - Des données d'exemple sont ajoutées

5. **Supprimer le fichier d'installation**
   ```
   Supprimer : etiquette-app/install.php
   ```

6. **Accéder à l'application**
   ```
   http://localhost/etiquette-app/
   ```

✅ **C'est terminé !** L'application est prête à l'emploi.

---

## 🛠️ Installation manuelle (Alternative)

Si vous préférez installer manuellement via phpMyAdmin :

### Étape 1 : Créer la base de données

1. Ouvrir phpMyAdmin : `http://localhost/phpmyadmin`
2. Cliquer sur "Nouvelle base de données"
3. Nom : `etiquette_db`
4. Interclassement : `utf8mb4_unicode_ci`
5. Cliquer "Créer"

### Étape 2 : Importer le schéma

1. Sélectionner la base `etiquette_db`
2. Cliquer sur l'onglet "Importer"
3. Choisir le fichier : `database/schema_complete.sql`
4. Cliquer "Exécuter"

### Étape 3 : Vérification

Vérifier que ces tables existent :
- ✓ `references` (Références Sartorius)
- ✓ `commandes` (Commandes Sartorius)
- ✓ `articles_latitude` (Articles Latitude)
- ✓ `commandes_latitude` (Commandes Latitude)

### Étape 4 : Accéder à l'application

```
http://localhost/etiquette-app/
```

---

## 📊 Structure de la base de données

### Table : `references`
Stocke les références Sartorius (produits)
- `id` : Identifiant unique
- `reference` : Code référence
- `designation` : Description du produit
- `created_at`, `updated_at` : Horodatage

### Table : `commandes`
Commandes Sartorius avec quantités multiples en JSON
- `id` : Identifiant unique
- `numero_commande` : Numéro de commande
- `reference_id` : Lien vers référence
- `quantites` : JSON `[{quantite_par_carton, quantite_etiquettes}]`
- `date_production` : Format MM/YYYY
- `numero_lot` : Numéro de lot
- `created_at`, `updated_at` : Horodatage

### Table : `articles_latitude`
Articles réutilisables pour Latitude
- `id` : Identifiant unique
- `nom` : Nom de l'article (ex: "Carte postale")
- `created_at`, `updated_at` : Horodatage

### Table : `commandes_latitude`
Commandes Latitude avec articles multiples en JSON
- `id` : Identifiant unique
- `numero_commande` : Numéro de commande
- `articles` : JSON `[{type, quantite, nombre_cartons}]`
- `created_at`, `updated_at` : Horodatage

---

## 🔧 Configuration

### Fichier : `config/database.php`

Par défaut :
```php
private $host = "localhost";
private $db_name = "etiquette_db";
private $username = "root";
private $password = "";
```

**Modifier si nécessaire** selon votre configuration MySQL.

---

## ✅ Vérification de l'installation

### Test 1 : Page d'accueil
```
http://localhost/etiquette-app/
```
Doit afficher : Menu avec Sartorius et Latitude

### Test 2 : Base de données
```
http://localhost/etiquette-app/check_database.php
```
Doit afficher : ✓ Toutes les tables existent

### Test 3 : Créer une référence
1. Cliquer "Étiquettes Sartorius"
2. Cliquer "Référence"
3. Ajouter une référence test
4. Succès = Installation OK !

---

## 🆘 Dépannage

### Erreur : "Connection failed"
**Cause** : MySQL n'est pas démarré
**Solution** : 
1. Ouvrir XAMPP Control Panel
2. Démarrer MySQL
3. Rafraîchir install.php

### Erreur : "Access denied for user"
**Cause** : Mauvais identifiants MySQL
**Solution** :
1. Vérifier dans XAMPP que user=root, password=(vide)
2. Modifier `install.php` lignes 12-15 si différent

### Erreur : "Table already exists"
**Cause** : Base de données déjà créée
**Solution** :
1. Supprimer l'ancienne base dans phpMyAdmin
2. Relancer install.php

### Erreur : Fichier SQL introuvable
**Cause** : Dossier `database/` manquant
**Solution** :
1. Vérifier la structure complète de l'archive
2. Re-extraire l'archive

### PDFs non créés
**Cause** : Permissions du dossier `pdfs/`
**Solution** :
```bash
chmod 777 pdfs/
chmod 777 pdfs_latitude/
```

---

## 🗂️ Structure des fichiers

```
etiquette-app/
├── install.php              ← Script d'installation (À SUPPRIMER après)
├── index.php                ← Point d'entrée
├── config/
│   └── database.php         ← Configuration BDD
├── database/
│   └── schema_complete.sql  ← Schéma SQL complet
├── models/                  ← Modèles
├── controllers/             ← Contrôleurs
├── views/                   ← Vues
├── lib/                     ← Bibliothèques (FPDF, etc.)
├── assets/                  ← CSS, JS, images
├── pdfs/                    ← PDFs Sartorius générés
└── pdfs_latitude/           ← PDFs Latitude générés
```

---

## 🔐 Sécurité

### Après installation

1. **SUPPRIMER** `install.php`
2. **SUPPRIMER** `check_database.php` (si utilisé)
3. **SUPPRIMER** tous les fichiers `test_*.php`
4. **SUPPRIMER** `migrate_sartorius.php` (si présent)

Ces fichiers ne doivent PAS rester en production !

### Permissions recommandées

```
Dossiers : 755
Fichiers PHP : 644
Dossiers pdfs/ : 777 (écriture requise)
```

---

## 📝 Données d'exemple

L'installation inclut :

### Références Sartorius
- REF-001 : Étiquette standard A4
- REF-002 : Étiquette premium A5
- REF-003 : Étiquette économique A6

### Articles Latitude
- Carte postale
- Carte stickers
- Set de table
- Livre
- Flyer A5
- Brochure A4

**Ces données sont optionnelles** et peuvent être supprimées après installation.

---

## 🔄 Mise à jour depuis versions antérieures

Si vous avez une version < 1.0.0 :

### Option 1 : Nouvelle installation (Recommandée)
1. Sauvegarder vos PDFs (`pdfs/` et `pdfs_latitude/`)
2. Exporter vos données depuis phpMyAdmin
3. Installer v1.0.0 proprement
4. Ré-importer vos données

### Option 2 : Migration
1. Exécuter `migrate_sartorius.php` si vous avez des commandes Sartorius anciennes
2. Installer la table `articles_latitude` via install_articles_latitude.php
3. Vérifier avec check_database.php

---

## 📞 Support

En cas de problème :

1. **Vérifier** ce guide d'installation
2. **Consulter** les messages d'erreur détaillés
3. **Tester** avec les fichiers de diagnostic (`check_database.php`)
4. **Vérifier** les logs PHP (XAMPP: `xampp/php/logs/`)

---

## ✨ Fonctionnalités

### Module Sartorius
- ✓ Gestion des références produits
- ✓ Création de commandes avec quantités multiples
- ✓ Génération PDF automatique
- ✓ Édition et suppression

### Module Latitude
- ✓ Gestion des articles réutilisables
- ✓ Création de commandes multi-articles
- ✓ Génération PDF automatique
- ✓ Édition et suppression

### Général
- ✓ Interface moderne Bootstrap 5
- ✓ Design responsive
- ✓ Alertes et notifications
- ✓ Validation des formulaires
- ✓ Protection contre les doublons

---

**Version** : 1.0.0  
**Date** : Février 2026  
**Application** : Étiquettes Sartorius & Latitude
