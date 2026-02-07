# Application Étiquettes - Version 0.1.0

Application web de gestion d'étiquettes Sartorius et Latitude développée en PHP POO MVC avec Bootstrap.

## Fonctionnalités

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
