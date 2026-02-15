# TODO - Migration Sartorius vers JSON (v0.4.0)

## ✅ Fait
- [x] Script de migration SQL (migrate_to_json.sql)
- [x] Script de migration PHP (migrate_sartorius.php)
- [x] Modèle Commande modifié (propriété quantites)
- [x] Méthode create() adaptée pour JSON
- [x] Méthode update() adaptée pour JSON
- [x] Méthode getTotalEtiquettes() ajoutée
- [x] Contrôleur creer() modifié (création unique avec JSON)

## ⏳ À faire

### 1. Générateur PDF Sartorius
- [ ] Modifier SartoriusPdfGenerator pour lire quantites JSON
- [ ] Générer plusieurs étiquettes par commande (boucle sur quantites)
- [ ] Numérotation séquentielle comme Latitude

### 2. Contrôleur modifier()
- [ ] Adapter pour gérer quantites JSON
- [ ] Formulaire édition avec lignes dynamiques

### 3. Vues
- [ ] Page liste.php : afficher nombre de lignes/étiquettes
- [ ] Page edition.php : formulaire avec lignes dynamiques comme nouvelle.php
- [ ] Convertir quantites JSON → tableau pour affichage

### 4. Tests
- [ ] Tester création avec 1 ligne
- [ ] Tester création avec 3+ lignes
- [ ] Tester édition
- [ ] Tester suppression
- [ ] Vérifier génération PDF

### 5. Documentation
- [ ] README.md : expliquer nouvelle structure
- [ ] Instructions migration pour utilisateurs existants
- [ ] COMMIT_MESSAGE complet

## Notes importantes
- Les anciennes colonnes quantite_par_carton et quantite_etiquettes sont conservées
- Permet compatibilité ascendante
- Peuvent être supprimées après validation complète
