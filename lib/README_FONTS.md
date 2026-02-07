# Polices utilisées dans le générateur PDF

## Roboto via Helvetica

L'application utilise **Helvetica** comme substitut de **Roboto** pour les raisons suivantes :

1. **Compatibilité FPDF** : FPDF ne supporte nativement que les 14 polices standard PDF (Core Fonts), dont Helvetica
2. **Similarité visuelle** : Helvetica et Roboto sont toutes deux des polices sans-serif avec des proportions similaires
3. **Aucune dépendance externe** : Pas besoin de fichiers de police supplémentaires
4. **Rendu universel** : Fonctionne sur tous les systèmes sans installation

### Mapping des polices

- **Roboto Regular** → `Helvetica` (Regular)
- **Roboto Bold** → `Helvetica-Bold` (Bold)

## Icône d'usine

L'icône d'usine est une image PNG (16x16 pixels) située dans :
```
lib/fpdf/images/factory.png
```

Cette icône est affichée avant la date de production en bleu (RGB: 41, 128, 185).

## Alternative : Vraies polices TTF

Si vous souhaitez utiliser les vraies polices Roboto TTF, vous devrez :

1. Télécharger les fichiers TTF de Roboto
2. Les convertir au format compatible FPDF avec un outil comme `makefont`
3. Mettre à jour le code pour charger ces polices personnalisées

Pour l'instant, Helvetica offre un excellent compromis entre qualité et simplicité.
