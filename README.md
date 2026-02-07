# Application Étiquettes - Version 0.1.4

Application web de gestion d'étiquettes Sartorius et Latitude développée en PHP POO MVC avec Bootstrap.

## Fonctionnalités

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
