# Application Étiquettes v1.0.11 🏷️

Application web professionnelle de gestion d'étiquettes Sartorius et Latitude développée en PHP POO MVC avec Bootstrap 5.

[![Version](https://img.shields.io/badge/version-1.0.11-blue.svg)](https://github.com)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 🚀 Installation rapide (3 minutes)

```bash
# 1. Extraire l'archive dans htdocs/
# 2. Démarrer Apache + MySQL (XAMPP)
# 3. Ouvrir dans le navigateur
http://localhost/etiquette-app/install.php

# 4. Choisir le nom de votre base de données
# 5. Suivre l'assistant d'installation
# 6. C'est prêt !
```

📖 **[Guide démarrage rapide 2 min](QUICKSTART.md)**

---

## ✨ Fonctionnalités v1.0.11

### Module Sartorius 🏷️
- ✅ **Gestion références** — CRUD complet avec protection doublons avancée
- ✅ **Commandes multi-quantités** — Format JSON flexible
- ✅ **Génération PDF** — 8 étiquettes/page (105×74mm, A4 Portrait)
- ✅ **Édition dynamique** — Modification commandes existantes
- ✅ **Suppression par sélection** — Cases à cocher + modal confirmation
- ✅ **UX optimisée** — Reste sur page après création, valeurs conservées en erreur

### Module Latitude 🌍
- ✅ **Gestion articles** — CRUD avec validation doublons
- ✅ **Commandes multi-articles** — Quantités par type
- ✅ **Génération PDF** — 8 étiquettes/page avec numérotation séquentielle
- ✅ **Suppression par sélection** — Workflow moderne
- ✅ **Articles réutilisables** — Dropdown dynamiques

### Interface & UX 🎨
- ✅ **Custom Alerts** — Notifications modernes (position fixe, animations fluides)
- ✅ **Responsive Design** — Desktop, tablette, mobile
- ✅ **Protection doublons** — 3 niveaux (référence, désignation, les deux)
- ✅ **Conservation valeurs** — En cas d'erreur (pas de re-saisie)
- ✅ **Architecture MVC** — Code propre et maintenable
- ✅ **Sessions PHP** — Gestion état utilisateur

### Installation & Sécurité 🔒
- ✅ **Nom BDD personnalisable** — Formulaire configuration
- ✅ **Protection réinstallation** — Fichier `.installation_complete`
- ✅ **Validation entrées** — Sécurité formulaires
- ✅ **Compatible PHP 8.2+** — Code moderne

---

## 🗂️ Structure de la base de données

```sql
[nom_choisi]              # Nom personnalisable à l'installation
├── references           # Références Sartorius (référence, désignation)
├── commandes            # Commandes Sartorius (quantités JSON)
├── articles_latitude    # Articles Latitude réutilisables
└── commandes_latitude   # Commandes Latitude (articles JSON)
```

**Format JSON moderne** pour flexibilité maximale :
- Sartorius : `[{quantite_par_carton, quantite_etiquettes}, ...]`
- Latitude : `[{type, quantite, nombre_cartons}, ...]`

**Index optimisés** pour performance :
- Clés primaires auto-incrémentées
- Index sur référence, désignation, numéro_commande
- Foreign keys avec CASCADE

---

## 📋 Prérequis

- **Serveur** : Apache 2.4+
- **PHP** : 7.4+ (testé jusqu'à 8.2)
- **MySQL** : 5.7+ ou MariaDB 10.3+
- **Extensions PHP** : PDO, PDO_MySQL, mbstring, GD
- **XAMPP/WAMP/MAMP** recommandé pour développement

---

## 🛠️ Stack technique

| Technologie | Version | Usage |
|------------|---------|-------|
| PHP | 7.4 - 8.2 | Backend (POO, MVC) |
| MySQL | 5.7+ | Base de données |
| Bootstrap | 5.3.0 | Interface UI responsive |
| FPDF | 1.84 | Génération PDF |
| JavaScript | ES6+ | Custom alerts, interactions |
| JSON | - | Stockage données flexibles |
| Sessions PHP | - | Gestion état formulaires |

---

## 📦 Installation complète

### Étape 1 : Préparer l'environnement

**Windows (XAMPP) :**
```bash
1. Télécharger XAMPP : https://www.apachefriends.org
2. Installer et démarrer Apache + MySQL
3. Extraire l'archive dans : C:\xampp\htdocs\
```

**Mac (MAMP) :**
```bash
1. Télécharger MAMP : https://www.mamp.info
2. Installer et démarrer les serveurs
3. Extraire l'archive dans : /Applications/MAMP/htdocs/
```

**Linux :**
```bash
sudo apt install apache2 php php-mysql php-mbstring php-gd
sudo systemctl start apache2 mysql
# Extraire dans : /var/www/html/
```

### Étape 2 : Installation automatique

1. **Accéder à l'installateur**
   ```
   http://localhost/etiquette-app/install.php
   ```

2. **Remplir le formulaire de configuration**
   - Hôte MySQL : `localhost`
   - Utilisateur : `root` (par défaut)
   - Mot de passe : *(vide pour XAMPP)*
   - **Nom de la base** : `etiquette_db` (ou votre choix)

3. **Lancer l'installation** (6 étapes automatiques)
   - ✅ Connexion serveur MySQL
   - ✅ Création base de données
   - ✅ Connexion à la base
   - ✅ Création des 4 tables
   - ✅ Mise à jour configuration
   - ✅ Vérification finale

4. **Protection activée**
   - Fichier `.installation_complete` créé automatiquement
   - Empêche les réinstallations accidentelles

5. **C'est prêt !**
   ```
   http://localhost/etiquette-app/
   ```

### Étape 3 : Nettoyage (optionnel)

**Supprimer fichiers temporaires :**
```bash
# Windows
cleanup_projet.bat

# Linux/Mac
chmod +x cleanup_projet.sh
./cleanup_projet.sh
```

**Économie :** ~40 Ko (fichiers obsolètes, doublons CSS, docs anciennes)

---

## 🎯 Guide d'utilisation

### Workflow Sartorius

**1. Créer une référence**
```
Accueil → Étiquettes Sartorius → Référence (header)
Remplir : Référence + Désignation
Valider → ✅ Alerte succès (reste sur page)
```

**2. Créer une commande**
```
Étiquettes Sartorius → Nouveau (header)
Sélectionner : Référence existante
Remplir : N° commande, date, lot
Ajouter : Quantités par carton (lignes dynamiques)
Valider → PDF généré automatiquement
```

**3. Éditer/Supprimer**
```
Liste commandes → ✏️ Éditer ou ☑ Sélection + 🗑️ Supprimer
```

### Workflow Latitude

**1. Créer un article**
```
Accueil → Étiquettes Latitude → Article (header)
Remplir : Nom de l'article
Valider → ✅ Alerte succès (reste sur page)
```

**2. Créer une commande**
```
Étiquettes Latitude → Nouveau (header)
Remplir : N° commande
Ajouter : Articles avec quantités (lignes dynamiques)
Valider → PDF généré automatiquement
```

**3. Éditer/Supprimer**
```
Liste commandes → ✏️ Éditer ou ☑ Sélection + 🗑️ Supprimer
```

### Système d'alertes

**Custom Alerts (v1.0.11) :**
- 🟢 **Succès** — Position fixe coin droit, auto-close 5s
- 🔴 **Erreur** — Position fixe, fermeture manuelle
- 🟡 **Warning** — Pour avertissements
- 🔵 **Info** — Pour informations

**Gestion intelligente :**
- ✅ Nettoyage URL automatique (pas de `?success=...` visible)
- ✅ Animations fluides (slide + fade)
- ✅ Empilage multiple alertes
- ✅ Responsive mobile/desktop

---

## 🔧 Configuration avancée

### Changer le nom de la base après installation

**Méthode 1 — Réinstallation :**
```bash
1. Supprimer .installation_complete
2. Supprimer ancienne base (phpMyAdmin)
3. Relancer install.php avec nouveau nom
```

**Méthode 2 — Configuration manuelle :**
```php
// Éditer config/database.php
private $db_name = "nouveau_nom";
```

### Permissions dossiers PDF

**Linux/Mac :**
```bash
chmod 755 pdfs/
chmod 755 pdfs_latitude/
```

**Windows :**
```
Clic droit dossiers → Propriétés → Sécurité
Donner contrôle total à "Utilisateurs"
```

### Mode debug

**Activer les erreurs PHP :**
```php
// Dans index.php (uniquement développement)
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📊 Schéma d'architecture

```
etiquette-app/
├── 📄 index.php                 # Point d'entrée MVC
├── 📄 install.php               # Installation automatique
├── 📄 .installation_complete    # Protection réinstallation
├── 📄 .htaccess                 # Réécriture URL + sécurité
│
├── 📁 config/
│   └── database.php             # Configuration BDD
│
├── 📁 controllers/              # Logique métier
│   ├── CommandeController.php
│   ├── LatitudeController.php
│   ├── ReferenceController.php
│   └── ArticleLatitudeController.php
│
├── 📁 models/                   # Accès données
│   ├── Commande.php
│   ├── CommandeLatitude.php
│   ├── Reference.php
│   └── ArticleLatitude.php
│
├── 📁 views/                    # Interface utilisateur
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── sartorius/
│   ├── latitude/
│   ├── references/
│   └── articles_latitude/
│
├── 📁 lib/                      # Bibliothèques
│   ├── fpdf/                    # Génération PDF
│   ├── SartoriusPdfGenerator.php
│   └── LatitudePdfGenerator.php
│
├── 📁 assets/                   # Ressources
│   ├── css/
│   │   └── custom-alerts.css   # Styles alertes
│   ├── js/
│   │   └── custom-alerts.js    # Système alertes
│   └── image/
│
├── 📁 css/
│   └── style.css                # Styles principaux
│
├── 📁 database/
│   └── schema_complete.sql      # Schéma installation
│
├── 📁 pdfs/                     # PDFs Sartorius générés
└── 📁 pdfs_latitude/            # PDFs Latitude générés
```

---

## 🆘 Dépannage

### Erreur : Table doesn't exist

**Cause :** Installation incomplète ou nom de base incorrect

**Solution :**
```bash
1. Vérifier dans phpMyAdmin : quelle base contient les tables ?
2. Éditer config/database.php avec le bon nom
3. OU réinstaller avec le bon nom
```

### Erreur : Connection failed

**Cause :** MySQL non démarré ou identifiants incorrects

**Solution :**
```bash
1. XAMPP : Vérifier que MySQL est vert
2. Vérifier config/database.php (user/pass)
```

### PDF non générés

**Cause :** Permissions dossiers

**Solution :**
```bash
chmod 755 pdfs/
chmod 755 pdfs_latitude/
```

### Custom alerts ne s'affichent pas

**Cause :** JavaScript désactivé ou fichier manquant

**Solution :**
```bash
1. Vérifier que JavaScript est activé dans le navigateur
2. Vérifier présence de assets/js/custom-alerts.js
3. F12 → Console pour voir erreurs JS
```

### Doublon non détecté

**Cause :** Vérifications PHP correctes, problème d'affichage

**Solution :**
```bash
Vider cache navigateur (Ctrl+F5)
Vérifier présence fichier assets/js/custom-alerts.js
```

---

## 🔄 Changelog

### v1.0.11 (Mars 2026) — Custom Alerts & UX
- ✨ Remplacement alertes Bootstrap par custom alerts modernes
- ✨ Conservation valeurs formulaire en cas d'erreur
- ✨ Workflow création références optimisé (reste sur page)
- ✨ Nouveau code erreur "Les deux existent" (référence + désignation)
- 🔧 Protection doublons améliorée (3 niveaux)
- 📦 Script nettoyage automatique projet

### v1.0.10 (Mars 2026) — Améliorations UX
- ✨ Reste sur page après création référence/article
- ✨ Valeurs conservées en erreur (pas de re-saisie)
- 🔧 Protection doublons références séparée (référence vs désignation)

### v1.0.9 (Février 2026) — Installation corrigée
- ✨ Nom de base de données personnalisable
- 🔒 Protection contre double installation
- 🐛 Fix : Tables créées dans mauvaise base
- 🔧 Script réparation automatique
- 📄 Mise à jour config/database.php automatique

### v1.0.8 (Février 2026) — Protection doublons
- 🔒 Vérifications séparées référence/désignation
- ✨ Alertes d'erreur spécifiques
- 🔧 Méthodes exists() dans modèles

### v1.0.0 à v1.0.7
- Développement initial modules Sartorius + Latitude
- Architecture MVC complète
- Génération PDF 8 étiquettes/page
- Suppression par sélection (cases à cocher)
- Modals Bootstrap
- Base données unifiée

---

## 📞 Support

### Problèmes techniques
- 📧 Email : support@exemple.com
- 💬 Issues : GitHub repository
- 📖 Documentation : [QUICKSTART.md](QUICKSTART.md)

### Contribuer
Les pull requests sont bienvenues ! Pour des changements majeurs :
1. Fork le projet
2. Créer une branche feature
3. Commit les changements
4. Push vers la branche
5. Ouvrir une Pull Request

---

## 📝 License

MIT License - Voir [LICENSE](LICENSE) pour détails

---

## 🙏 Remerciements

- **Bootstrap** — Framework UI
- **FPDF** — Génération PDF
- **PHP Community** — Documentation et support
- **Anthropic Claude** — Assistance développement

---

## 🚀 Roadmap future

### v1.1.0 (À venir)
- [ ] Export Excel des commandes
- [ ] Recherche avancée avec filtres
- [ ] Statistiques et graphiques
- [ ] Mode sombre
- [ ] Multi-utilisateurs avec authentification

### v1.2.0 (Futur)
- [ ] API REST pour intégrations
- [ ] Templates PDF personnalisables
- [ ] Historique des modifications
- [ ] Backup automatique BDD

---

**Version actuelle : 1.0.11**  
**Dernière mise à jour : Mars 2026**  
**Statut : Production stable** ✅
