# 🚀 DÉMARRAGE RAPIDE v1.0.11

Guide ultra-rapide pour installer et utiliser l'application Étiquettes en **2 minutes chrono** ! ⏱️

---

## ⚡ Installation express (4 étapes)

### 1️⃣ Extraire l'archive
```
Décompresser dans :
📁 C:\xampp\htdocs\              (Windows)
📁 /Applications/MAMP/htdocs/    (Mac)
📁 /var/www/html/                (Linux)

Résultat : htdocs/etiquette-app/
```

### 2️⃣ Démarrer les services
```
✅ Ouvrir XAMPP/MAMP Control Panel
✅ Démarrer Apache (port 80)
✅ Démarrer MySQL (port 3306)
```

### 3️⃣ Installer (automatique)
```
🌐 Navigateur → http://localhost/etiquette-app/install.php

📋 Formulaire configuration :
   Hôte MySQL :     localhost
   Utilisateur :    root
   Mot de passe :   (vide)
   Nom base :       etiquette_db  ← Personnalisable !

✨ Cliquer "Lancer l'installation"
⏳ Patienter 10 secondes (6 étapes automatiques)
✅ "Installation réussie !"
```

### 4️⃣ Utiliser !
```
🏠 http://localhost/etiquette-app/
🎯 Choisir : Sartorius ou Latitude
🏷️ Créer vos étiquettes !
```

**C'est tout ! Temps total : ~2 minutes** ⚡

---

## 🎯 Première utilisation

### 🏷️ Module Sartorius (exemple complet)

**1. Créer une référence**
```
Page accueil → Étiquettes Sartorius
Header → Bouton "Référence" (à droite du titre)

Formulaire :
  Référence :    REF-001
  Désignation :  Produit Test

Soumettre → ✅ Alerte verte "Créée avec succès"
(Reste sur page, champs vidés, prêt pour suivante)
```

**2. Créer une commande**
```
Header → Bouton "Nouveau"

Formulaire :
  Référence :      [Sélectionner "REF-001 - Produit Test"]
  N° Commande :    CMD-2024-001
  Date :           [Dropdown : jour / mois / année]
  N° Lot :         LOT-123

Quantités (lignes dynamiques) :
  Ligne 1 :  24 par carton  /  100 étiquettes
  Ligne 2 :  12 par carton  /   50 étiquettes
  (Bouton + pour ajouter, - pour supprimer)

Soumettre → PDF généré automatiquement (8 étiquettes/page)
```

**3. Consulter/Modifier**
```
Liste commandes :
  ✏️  Éditer → Modifier quantités, date, etc.
  ⬇️  Télécharger → PDF
  ☑️  Sélection + 🗑️ → Supprimer plusieurs
```

### 🌍 Module Latitude (exemple complet)

**1. Créer des articles**
```
Page accueil → Étiquettes Latitude
Header → Bouton "Article"

Créer plusieurs articles (reste sur page après chaque création) :
  - Carte postale     → ✅
  - Flyer A5          → ✅
  - Brochure A4       → ✅
```

**2. Créer une commande**
```
Header → Bouton "Nouveau"

Formulaire :
  N° Commande :  LAT-2024-001

Articles (lignes dynamiques) :
  Ligne 1 :  Carte postale   / 50 unités  / 5 cartons
  Ligne 2 :  Flyer A5        / 100 unités / 10 cartons
  (Bouton + pour ajouter, - pour supprimer)

Soumettre → PDF avec numérotation séquentielle automatique
```

**3. Consulter/Modifier**
```
Liste commandes :
  ✏️  Éditer → Modifier articles, quantités
  ⬇️  Télécharger → PDF
  ☑️  Sélection + 🗑️ → Supprimer plusieurs
```

---

## ✨ Nouveautés v1.0.11

### 🎨 Custom Alerts (modernes)
```
✅ Succès :    Alerte verte, coin droit, auto-close 5s
❌ Erreur :    Alerte rouge, coin droit, fermeture manuelle
⚠️  Warning :  Alerte orange
ℹ️  Info :     Alerte bleue

Animations fluides + nettoyage URL automatique
```

### 🔄 UX améliorée
```
✅ Reste sur page après création (pas de navigation)
✅ Valeurs conservées en cas d'erreur (pas de re-saisie)
✅ Alertes spécifiques doublons :
   - "Cette référence existe déjà"
   - "Cette désignation existe déjà"
   - "Cette référence et cette désignation existent déjà"
```

### 🔒 Protection doublons avancée
```
Sartorius :
  ❌ Référence déjà utilisée → Erreur
  ❌ Désignation déjà utilisée → Erreur
  ❌ Les deux existent → Erreur spécifique

Latitude :
  ❌ Article existe → Erreur + valeur conservée
```

---

## 🆘 Problèmes courants (solutions rapides)

### ❌ Erreur "Connection failed"
```
Cause :    MySQL pas démarré
Solution : XAMPP → Start MySQL (bouton vert)
```

### ❌ Page blanche
```
Cause :    Apache pas démarré
Solution : XAMPP → Start Apache (bouton vert)
```

### ❌ "Table doesn't exist"
```
Cause :    Installation incomplète
Solution : 
  1. phpMyAdmin → Vérifier quelle base contient les tables
  2. Éditer config/database.php avec le bon nom
  OU
  1. Supprimer .installation_complete
  2. Relancer install.php
```

### ❌ PDF non créés
```
Cause :    Permissions dossiers
Solution : 
  Windows : Clic droit pdfs/ → Propriétés → Sécurité → Contrôle total
  Linux :   chmod 755 pdfs/ pdfs_latitude/
```

### ❌ Alertes ne s'affichent pas
```
Cause :    JavaScript désactivé ou fichier manquant
Solution : 
  1. F12 → Console → Vérifier erreurs JS
  2. Vérifier : assets/js/custom-alerts.js existe
  3. Vider cache : Ctrl+F5
```

### ❌ "Référence existe déjà" mais j'ai modifié
```
Cause :    Cache navigateur
Solution : Ctrl+F5 (rechargement forcé)
```

---

## 🧹 Nettoyage projet (optionnel)

Supprimer fichiers obsolètes pour gagner ~40 Ko :

### Windows
```cmd
1. Double-cliquer cleanup_projet.bat
2. Répondre O ou N aux questions
3. Terminé !
```

### Linux/Mac
```bash
chmod +x cleanup_projet.sh
./cleanup_projet.sh
```

**Fichiers supprimés :**
- CSS dupliqués (style.css racine, assets/css/style.css)
- Documentation obsolète (CLEANUP.md, INSTALL_MAC.md, etc.)
- Scripts one-time (create_factory_icon.php, download_fonts.php)
- .gitkeep (optionnel)

---

## 📋 Checklist complète

### Installation
- [ ] XAMPP/MAMP installé
- [ ] Apache + MySQL démarrés (boutons verts)
- [ ] Archive extraite dans htdocs/
- [ ] install.php exécuté avec succès
- [ ] Base de données créée (vérifier phpMyAdmin)
- [ ] Application accessible : http://localhost/etiquette-app/

### Première utilisation Sartorius
- [ ] Référence créée (REF-001 / Produit Test)
- [ ] Commande créée
- [ ] PDF généré et téléchargé
- [ ] Commande visible dans la liste

### Première utilisation Latitude
- [ ] Article créé (Carte postale)
- [ ] Commande créée
- [ ] PDF généré avec numérotation
- [ ] Commande visible dans la liste

### Test fonctionnalités
- [ ] Édition commande fonctionne
- [ ] Suppression par sélection (cases ☑)
- [ ] Custom alerts apparaissent (vert/rouge)
- [ ] Protection doublons active (tester création doublon)
- [ ] Valeurs conservées en erreur

---

## 🎓 Concepts clés (comprendre en 30 secondes)

### Architecture
```
MVC (Model-View-Controller)
├── Models :      Accès base de données (CRUD)
├── Views :       Interface utilisateur (HTML/Bootstrap)
└── Controllers : Logique métier (validation, traitement)

Une requête → Controller → Model → Database → Model → Controller → View
```

### Workflow création
```
1. Utilisateur remplit formulaire
2. POST vers Controller (ex: creer-reference)
3. Controller valide données
4. Si erreur → Session + Redirect avec ?error=code
5. Si succès → Create + Redirect avec ?success=code
6. Vue charge → JavaScript lit ?error ou ?success
7. Custom alert affiche message
8. URL nettoyée automatiquement
```

### Protection doublons
```
Avant création :
1. referenceExists() → Vérifie si référence existe
2. designationExists() → Vérifie si désignation existe
3. Si l'un ou l'autre → Erreur + $_SESSION['form_data']
4. Vue récupère session → Pré-remplit champs
5. Utilisateur corrige → Re-submit → Succès
```

---

## 📚 Documentation complète

- 📖 [README.md](README.md) — Documentation détaillée, architecture, API
- 🔧 Fichiers obsolètes ? → Exécuter cleanup_projet.bat/sh
- 🐛 Bug trouvé ? → Consulter section Dépannage du README

---

## ⚡ Résumé ultra-court

```bash
# Installation
1. Extraire dans htdocs/
2. Démarrer Apache + MySQL
3. http://localhost/etiquette-app/install.php
4. Choisir nom base → Installer

# Utilisation
1. Créer référence/article
2. Créer commande
3. PDF généré automatiquement
4. Éditer/Supprimer au besoin

# En cas de problème
- MySQL pas démarré ?        → Start dans XAMPP
- Table doesn't exist ?       → Vérifier nom base dans config/
- PDF pas créés ?             → Permissions dossiers pdfs/
- Alertes pas affichées ?     → Vider cache (Ctrl+F5)
```

---

## ✅ Prêt à créer des étiquettes !

L'application est **100% fonctionnelle** après ces 2 minutes d'installation.

**Prochaines étapes :**
1. Créer vos vraies références/articles
2. Générer vos commandes
3. Télécharger les PDFs
4. Imprimer sur étiquettes 105×74mm

**Bon travail !** 🏷️✨

---

**Version :** 1.0.11  
**Dernière mise à jour :** Mars 2026  
**Temps d'installation :** 2 minutes  
**Difficulté :** ★☆☆☆☆ (Très facile)
