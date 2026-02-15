# Application Étiquettes - Version 1.0.0 🎉

Application web professionnelle de gestion d'étiquettes Sartorius et Latitude développée en PHP POO MVC avec Bootstrap 5.

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 🚀 Installation rapide

```bash
# 1. Extraire l'archive dans htdocs/
# 2. Démarrer Apache + MySQL (XAMPP)
# 3. Ouvrir dans le navigateur
http://localhost/etiquette-app/install.php

# 4. Suivre l'assistant d'installation
# 5. Supprimer install.php
# 6. C'est prêt !
```

📖 **[Guide d'installation complet](INSTALLATION.md)**

---

## ✨ Fonctionnalités

### Module Sartorius 🏷️
- ✅ Gestion des références produits (CRUD)
- ✅ Commandes avec quantités multiples (JSON)
- ✅ Génération PDF automatique (4 étiquettes/page)
- ✅ Édition et suppression
- ✅ Dropdown de sélection des dates

### Module Latitude 🌍
- ✅ Gestion des articles réutilisables (CRUD)
- ✅ Commandes multi-articles avec quantités
- ✅ Génération PDF automatique (4 étiquettes/page)
- ✅ Numérotation séquentielle des cartons
- ✅ Articles personnalisables

### Général 🎨
- ✅ Interface moderne et responsive (Bootstrap 5)
- ✅ Architecture MVC propre
- ✅ Base de données unifiée
- ✅ Validation des formulaires
- ✅ Alertes et notifications
- ✅ Protection contre doublons
- ✅ Compatible PHP 8.2+

---

## 🗂️ Structure de la base de données

```sql
etiquette_db
├── references              # Références Sartorius
├── commandes              # Commandes Sartorius (quantités JSON)
├── articles_latitude      # Articles Latitude réutilisables
└── commandes_latitude     # Commandes Latitude (articles JSON)
```

**Format JSON moderne** pour flexibilité maximale :
- Sartorius : `[{quantite_par_carton, quantite_etiquettes}]`
- Latitude : `[{type, quantite, nombre_cartons}]`

---

## 📋 Prérequis

- **Serveur** : Apache 2.4+
- **PHP** : 7.4+ (testé jusqu'à 8.2)
- **MySQL** : 5.7+ ou MariaDB 10.3+
- **Extensions PHP** : PDO, mbstring, GD
- **XAMPP/WAMP/MAMP** recommandé

---

## 🛠️ Technologies utilisées

| Technologie | Usage |
|------------|-------|
| PHP 7.4+ | Backend (POO, MVC) |
| MySQL | Base de données |
| Bootstrap 5.3 | Interface UI |
| FPDF 1.84 | Génération PDF |
| JavaScript ES6 | Interactions dynamiques |
| JSON | Stockage données flexibles |

---

## 📦 Installation

### Méthode 1 : Assistant d'installation (Recommandé)

1. **Extraire** l'archive dans `htdocs/`
2. **Naviguer** vers `http://localhost/etiquette-app/install.php`
3. **Suivre** l'assistant automatique
4. **Supprimer** `install.php` après installation

### Méthode 2 : Installation manuelle

1. **Créer** la base de données `etiquette_db`
2. **Importer** `database/schema_complete.sql` via phpMyAdmin
3. **Configurer** `config/database.php` si nécessaire
4. **Accéder** à `http://localhost/etiquette-app/`

📖 **[Documentation complète](INSTALLATION.md)**

---

## 🎯 Utilisation

### Sartorius - Créer des étiquettes

1. **Ajouter une référence** : Cliquer "Référence" → Renseigner code et désignation
2. **Créer une commande** : Cliquer "Nouveau" → Sélectionner référence, date, etc.
3. **Quantités multiples** : Cliquer "+" pour ajouter des lignes de quantités
4. **Générer** : Le PDF est créé automatiquement

### Latitude - Créer des étiquettes

1. **Ajouter un article** : Cliquer "Article" → Renseigner nom (ex: "Flyer A5")
2. **Créer une commande** : Cliquer "Nouveau" → N° commande
3. **Articles multiples** : Cliquer "+" pour ajouter des types d'articles
4. **Générer** : Le PDF est créé avec numérotation séquentielle

---

## 🏗️ Architecture

```
etiquette-app/
├── config/              # Configuration (database.php)
├── controllers/         # Contrôleurs MVC
├── models/             # Modèles (PDO)
├── views/              # Vues (HTML/PHP)
│   ├── layouts/        # Header, Footer
│   ├── commandes/      # Vues Sartorius
│   ├── latitude/       # Vues Latitude
│   ├── references/     # Gestion références
│   └── articles_latitude/ # Gestion articles
├── lib/                # Bibliothèques
│   ├── fpdf/          # Générateur PDF
│   ├── PdfGenerator.php
│   └── LatitudePdfGenerator.php
├── assets/             # Ressources
│   ├── css/           # Styles personnalisés
│   ├── js/            # Scripts JS
│   └── images/        # Images, icônes
├── database/           # Scripts SQL
│   └── schema_complete.sql
├── pdfs/              # PDFs Sartorius générés
└── pdfs_latitude/     # PDFs Latitude générés
```

---

## 🎨 Captures d'écran

### Page d'accueil
Interface moderne avec choix Sartorius / Latitude

### Liste des commandes
Tableau avec actions (Éditer, Télécharger, Supprimer)

### Formulaire de création
Lignes dynamiques avec boutons +/-

### PDF généré
4 étiquettes par page, format A4 paysage

---

## 📝 Changelog

### Version 1.0.0 (Février 2026) - RELEASE STABLE ✨
- ✅ Base de données unifiée (`etiquette_db`)
- ✅ Installation automatique via assistant web
- ✅ Documentation complète (INSTALLATION.md)
- ✅ Structure JSON pour flexibilité maximale
- ✅ Gestion articles Latitude
- ✅ Interface polie et cohérente
- ✅ Bouton "Supprimer tout" conditionnel
- ✅ Fix page vide PDF (multiples de 4)
- ✅ Compatible PHP 8.2+ (mb_convert_encoding)

### Versions antérieures (0.x.x)
Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique complet.

---

## 🔐 Sécurité

### À faire après installation :
- ✅ Supprimer `install.php`
- ✅ Supprimer `check_database.php`
- ✅ Supprimer tous les `test_*.php`
- ✅ Supprimer `migrate_*.php`

### Bonnes pratiques :
- Utiliser HTTPS en production
- Configurer un mot de passe MySQL
- Limiter les permissions des dossiers
- Sauvegarder régulièrement la base de données

---

## 🐛 Dépannage

### PDF non générés
```bash
chmod 777 pdfs/
chmod 777 pdfs_latitude/
```

### Erreur MySQL
Vérifier dans `config/database.php` :
- Host : `localhost`
- User : `root`
- Password : `""` (vide par défaut XAMPP)

### Page blanche
Activer l'affichage des erreurs dans `index.php` :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

📖 **[Guide complet](INSTALLATION.md#-dépannage)**

---

## 🤝 Contribution

Les contributions sont les bienvenues !

1. Fork le projet
2. Créer une branche (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit (`git commit -m 'Ajout nouvelle fonctionnalité'`)
4. Push (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrir une Pull Request

---

## 📄 License

Ce projet est sous licence MIT - voir [LICENSE](LICENSE) pour détails.

---

## 👤 Auteur

Développé avec ❤️ pour la gestion d'étiquettes professionnelle

---

## 🙏 Remerciements

- **Bootstrap** pour le framework UI
- **FPDF** pour la génération PDF
- **PHP** pour le backend robuste
- Tous les utilisateurs pour leurs retours !

---

**Version actuelle** : 1.0.0  
**Statut** : ✅ Stable - Production Ready  
**Support PHP** : 7.4 → 8.2+
**CHANGEMENT MAJEUR** : Sartorius utilise maintenant le format JSON comme Latitude

- **Structure unifiée** :
  - Une commande = plusieurs lignes de quantités en JSON
  - Comme Latitude : `quantites: [{quantite_par_carton, quantite_etiquettes}]`
  - Plus de commandes séparées pour chaque variation

- **Page création** :
  - Ligne fixe : Référence, Date, N° Commande, N° Lot
  - Lignes dynamiques : Quantités (bouton + pour ajouter)
  - Une seule commande créée avec toutes les lignes

- **Page édition** :
  - Formulaire dynamique avec toutes les lignes
  - Ajout/suppression de lignes
  - Régénération PDF automatique

- **Page liste** :
  - Colonne "Quantités" ajoutée
  - Badge nombre de lignes
  - Badge total d'étiquettes

- **Générateur PDF** :
  - Lit le JSON quantites
  - Génère toutes les étiquettes de toutes les lignes
  - Un seul PDF par commande

- **Migration automatique** :
  - Script `migrate_sartorius.php`
  - Convertit anciennes données vers JSON
  - Sans perte de données
  - Rapport détaillé

- **Avantages** :
  - ✓ Structure cohérente entre Sartorius et Latitude
  - ✓ Moins de lignes en base de données
  - ✓ Meilleure organisation
  - ✓ Édition plus flexible
  - ✓ Un seul PDF par commande

### Version 0.3.5 - FIX ERREUR 500
- **Correction critique** : Toutes les lignes de quantités sont maintenant enregistrées
  - Problème : Seule la dernière ligne était sauvegardée
  - Cause : Réutilisation de la même instance `Commande` avec `bindParam` par référence
  - Solution : Création d'une nouvelle instance `Commande` pour chaque ligne
- **Script de test** : `test_create_commande.php`
  - Teste la création de 3 commandes simultanées
  - Vérifie que toutes sont bien en base de données
  - Nettoie automatiquement les données de test
  - Accès : `http://localhost/etiquette-app/test_create_commande.php`

### Version 0.3.3 - VÉRIFICATION BASE DE DONNÉES
- **Script de diagnostic** : `check_database.php`
  - Vérifie la structure complète de la base de données
  - Affiche les colonnes de chaque table
  - Compte les enregistrements
  - Vérifie les contraintes de clé étrangère
  - Interface web conviviale avec tableaux
- **Script de mise à jour** : `database/update_schema.sql`
  - Assure la cohérence de la structure
  - Modification sans perte de données

### Version 0.3.2 - CORRECTIONS ÉDITION
- **Correction erreur JavaScript** : Fix conflit variable `count` dans custom-alerts.js
- **Page Édition commande Sartorius** : Nouveau layout cohérent avec création
  - Ligne fixe : Référence, Date, N° Commande, N° Lot
  - Ligne quantités : Qty par carton, Qty étiquettes
  - Date picker avec mois en lettres
- **Correction édition référence** : Fix erreur "array offset on bool"
  - `Reference::readOne()` retourne maintenant le tableau de données

### Version 0.3.0 - GESTION RÉFÉRENCES ET FORMULAIRE SARTORIUS
- **Gestion complète des références** :
  - Tableau des références sur page Ajout Référence
  - Boutons Éditer et Supprimer pour chaque référence
  - Page d'édition dédiée
  - Protection contre les doublons (référence + désignation)
  - Routes: editer-reference, modifier-reference, supprimer-reference

- **Nouveau formulaire Sartorius** :
  - **Ligne fixe** : Référence, Date production, N° Commande, N° Lot
  - **Lignes dynamiques** : Quantité par carton + Quantité d'étiquettes
  - Date picker avec mois en lettres (Janvier 2026, etc.)
  - Bouton + ajoute uniquement lignes de quantités
  - Permet création de variantes (même produit, différentes quantités)

### Version 0.2.5 - FIX JSON LATITUDE
- **Correction critique** : PDF Latitude vides
  - Suppression de `htmlspecialchars()` sur champs JSON
  - Script de réparation : `fix_latitude_json.php`
  - JSON stocké correctement en base

### Version 0.2.4 - OUTILS DIAGNOSTIC
- **Scripts de test** :
  - `test_pdf.php` : Diagnostic Sartorius
  - `test_pdf_latitude.php` : Diagnostic Latitude
- Logs de débogage ajoutés

### Version 0.2.3 - FIX GÉNÉRATION PDF
- **Correction PDF Sartorius et Latitude** :
  - Requête SQL avec JOIN pour récupérer données complètes
  - Vérification existence fichiers
  - Messages d'erreur utilisateur
  - Gestion d'erreurs améliorée

### Version 0.2.2
- **Liste Latitude améliorée** : Suppression colonne "Articles"
- **Édition Latitude** : Nouveau bouton "Éditer"
- **Page d'édition Latitude** : Modification complète avec régénération PDF
- **Boutons + intelligents** : Logique hide/show automatique

### Version 0.2.1
- **Script d'installation** : `install_latitude.php`
- **Correction** : require_once manquants pour module Latitude

### Version 0.2.0 - MODULE LATITUDE COMPLET 🎉
- **Bouton Latitude activé** : Page d'accueil avec bouton Latitude fonctionnel
- **Page Liste Latitude** : Liste des commandes sans bouton Référence
- **Formulaire dynamique** : Ajout de lignes d'articles avec animation
  - N° Commande
  - Article : Carte postale, Carte stickers, Set de table, Livre
  - Quantité d'article
  - Nombre d'exemplaire (cartons)
  - Bouton + pour ajouter des lignes
  - Animation slide-down et opacité
- **Génération PDF** : Étiquettes Latitude avec numérotation continue
  - Format A4 paysage, 4 étiquettes par page
  - Champs : Carton n°, Fournisseur, Commande n°, Article, Quantité
  - Numérotation séquentielle : 1-25 (Carte postale), 26-39 (Set de table), etc.
- **Base de données** : Table `commandes_latitude` avec stockage JSON des articles
- **Gestion complète** : Créer, lister, télécharger, supprimer
- **Dossier dédié** : pdfs_latitude/ pour les PDF générés

### Version 0.1.8
- **Bouton Vider PDF supprimé** : Le bouton "Vider PDF" a été retiré de l'interface
- **Suppression améliorée** : Le bouton Supprimer supprime maintenant aussi le fichier PDF associé
- **Validation renforcée** : Impossible de créer une référence + désignation identique
- **Ordre des boutons** : Boutons Sauvegarder et Annuler intervertis dans la page Édition
- **Code d'erreur** : Nouveau message `duplicate_combination` pour référence + désignation identiques
- **Nettoyage du code** : Suppression de la méthode `viderPdf()` et de sa route

### Version 0.1.7
- **Compatibilité Mac améliorée** : Correction de la génération PDF sur Mac avec XAMPP
- **Chemins absolus** : Utilisation de chemins absolus pour la création du dossier pdfs
- **Permissions renforcées** : Création automatique avec chmod 777 pour Mac
- **Gestion d'erreurs** : Try-catch complet dans PdfGenerator avec messages détaillés
- **Nettoyage des noms** : Les caractères spéciaux dans les références sont remplacés par "_"
- **Diagnostic PDF** : Nouveau fichier `diagnostic_pdf.php` pour identifier les problèmes
- **Guide Mac** : Nouveau fichier `INSTALL_MAC.md` avec instructions détaillées
- **Logs améliorés** : error_log() pour tracer les erreurs de génération PDF

### Version 0.1.6
- **Navbar avec dégradé** : Ajout d'une navbar bleue avec dégradé (#0061f2 → rgba(105, 0, 199, 0.8))
- **Organisation des boutons** : Réorganisation des boutons dans la page liste (Référence et Nouveau en premier)
- **Icône factory agrandie** : Taille doublée de l'icône factory dans le PDF (4mm → 8mm)
- **Alertes unifiées** : Suppression de toutes les alertes Bootstrap, utilisation exclusive du système d'alertes personnalisé
- **Gestion d'erreurs améliorée** : Les erreurs de création/modification redirigent maintenant avec des paramètres URL
- **Nouveaux codes d'erreur** :
  - `duplicate_reference` : Référence déjà existante
  - `create_failed` : Erreur de création
  - `update_failed` : Erreur de modification
- **Alertes sur toutes les pages** : Le système d'alertes fonctionne maintenant sur les pages Ajout Référence, Nouvelle étiquette et Édition

### Version 0.1.5
- **Nouveau titre** : "Étiquettes de colisages" au lieu de "Application Étiquettes"
- **Favicons** : Ajout de favicons complets (favicon.ico, apple-touch-icon, android-chrome, etc.)
- **Fond coloré** : Couleur de fond #eff5f7 pour un look plus moderne
- **Footer simplifié** : Retrait du fond gris, footer plus épuré
- **Page d'accueil améliorée** : Carte Sartorius cliquable avec effet hover
- **Style CSS centralisé** : Nouveau fichier css/style.css avec styles globaux
- **PDF amélioré** : Dimensions d'étiquettes ajustées (148,5mm x 105mm) et taille de police augmentée (18pt)
- **Icône d'usine mise à jour** : Nouvelles versions factory.png et factory.svg

### Version 0.1.4
- **Alertes personnalisées** : Nouveau système d'alertes avec animation depuis la gauche
- **Design moderne** : Alertes avec dégradés de couleurs et ombres portées
- **Animation fluide** : Apparition depuis la gauche (slideIn) avec transition douce
- **Couleurs par type** :
  - 🟢 Succès : Vert (#10b981 → #059669)
  - 🔴 Erreur : Rouge (#ef4444 → #dc2626)
  - 🟠 Warning : Orange (#f97316 → #ea580c)
  - 🔵 Info : Bleu (#3b82f6 → #2563eb)
- **Fermeture automatique** : Les alertes disparaissent après 5 secondes
- **Bouton de fermeture** : Possibilité de fermer manuellement
- **URL nettoyage** : Les paramètres success/error sont supprimés de l'URL après affichage
- **Responsive** : Adaptation aux petits écrans

### Version 0.1.3
- **Bouton "Vider PDF"** : Supprime tous les fichiers PDF du dossier pdfs (conserve les commandes)
- **Bouton "Supprimer tout"** : Supprime toutes les commandes de la base de données et leurs PDF associés
- **Modales de confirmation** avec Bootstrap pour confirmer les actions destructives
- **Messages de retour** : Affichage du nombre de PDF supprimés et messages d'erreur appropriés
- **Sécurité** : Confirmations obligatoires avant suppression pour éviter les erreurs

### Version 0.1.2
- **Police Roboto** : Utilisation de Helvetica (police standard PDF) qui ressemble beaucoup à Roboto
  - Roboto Regular → Helvetica
  - Roboto Bold → Helvetica-Bold
- **Icône d'usine PNG** : Remplacement de la police ZapfDingbats par une véritable icône d'usine (16x16 px)
- **Meilleure lisibilité** : Icône plus claire et professionnelle

### Version 0.1.1
- **Format A4 paysage** pour les PDFs d'étiquettes
- **4 étiquettes par page** (2 colonnes x 2 lignes)
- **Suppression des contours** autour des étiquettes
- Remplacement de "e" par "**ex**" dans "1 CARTON DE XX ex"
- **Icône d'usine** (⚙) avant la date de production
- **Nom de fichier amélioré** : format `REFERENCE-MM_AAAA.pdf` (ex: IU114789-02_2026.pdf)

### Version 0.1.0
- **Génération de PDF d'étiquettes** avec FPDF
- Téléchargement automatique du PDF lors de la création d'une commande
- Bouton de téléchargement PDF dans la liste des commandes
- Format d'étiquette personnalisé (4 étiquettes par page A4)
- Dossier pdfs protégé pour stocker les fichiers générés

### Version 0.0.3
- Amélioration de la gestion des erreurs (try-catch pour PDOException)
- Messages de succès après création/modification/suppression
- Message d'erreur convivial pour les doublons de référence

### Version 0.0.2
- Correction du schéma de base de données (mot réservé SQL 'references')

### Version 0.0.1
- Page d'accueil avec navigation vers Sartorius et Latitude
- Gestion des références (ajout, liste)
- Gestion des commandes d'étiquettes Sartorius (création, édition, suppression, liste)
- Interface responsive avec Bootstrap 5
- Architecture MVC en PHP orienté objet

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)
- Extension PHP PDO et PDO_MySQL

## Installation

1. **Cloner ou extraire l'application dans votre serveur web**
   ```
   etiquette-app/
   ```

2. **Créer la base de données**
   - Ouvrir phpMyAdmin ou votre client MySQL
   - Exécuter le script SQL situé dans `database/schema.sql`

3. **Configurer la connexion à la base de données**
   - Ouvrir le fichier `config/database.php`
   - Modifier les paramètres de connexion si nécessaire :
     ```php
     private $host = "localhost";
     private $db_name = "etiquette_db";
     private $username = "root";
     private $password = "";
     ```

4. **Accéder à l'application**
   - Ouvrir votre navigateur
   - Accéder à l'URL : `http://localhost/etiquette-app/`

## Structure du projet

```
etiquette-app/
├── config/
│   └── database.php          # Configuration BDD
├── controllers/
│   ├── CommandeController.php
│   └── ReferenceController.php
├── models/
│   ├── Commande.php
│   └── Reference.php
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── commandes/
│   │   ├── liste.php
│   │   ├── nouvelle.php
│   │   └── edition.php
│   ├── references/
│   │   └── ajout.php
│   └── home.php
├── database/
│   └── schema.sql            # Schéma de la base de données
├── index.php                  # Point d'entrée
├── .htaccess                  # Configuration Apache
└── README.md
```

## Utilisation

### Page d'accueil
Deux boutons permettent d'accéder aux modules :
- **Sartorius** : Gestion des étiquettes Sartorius (fonctionnel)
- **Latitude** : À venir

### Gestion Sartorius

#### Ajouter une référence
1. Cliquer sur le bouton "Référence" dans la page Étiquettes Sartorius
2. Remplir le formulaire (Référence et Désignation)
3. Cliquer sur "Sauvegarder"

#### Créer une nouvelle commande
1. Cliquer sur le bouton "Nouveau" dans la page Étiquettes Sartorius
2. Remplir tous les champs du formulaire :
   - Référence (liste déroulante)
   - Quantité par carton
   - Date de production (format MM/AAAA)
   - N° Commande
   - N° Lot
   - Quantité d'étiquettes
3. Cliquer sur "Sauvegarder"

#### Éditer une commande
1. Cliquer sur le bouton "Éditer" (crayon) dans la liste
2. Modifier les informations
3. Cliquer sur "Sauvegarder"

#### Supprimer une commande
1. Cliquer sur le bouton "Supprimer" (poubelle) dans la liste
2. Confirmer la suppression

#### Télécharger le PDF
*Fonctionnalité à venir dans une prochaine version*

## Technologies utilisées

- **Backend** : PHP 7.4+ (POO)
- **Architecture** : MVC (Model-View-Controller)
- **Base de données** : MySQL 5.7+
- **Frontend** : Bootstrap 5.3, Bootstrap Icons
- **JavaScript** : Vanilla JS pour les interactions

## À venir

- Génération de PDF pour les étiquettes
- Module Latitude
- Export des données
- Gestion avancée des références

## Support

Pour toute question ou problème, veuillez créer une issue dans le dépôt du projet.

## Licence

Tous droits réservés.
