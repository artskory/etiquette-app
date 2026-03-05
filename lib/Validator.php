<?php
/**
 * Classe de validation et nettoyage des entrées utilisateur
 * Version 1.0.11
 * 
 * Protection contre XSS, injections SQL, et données malformées
 */

class Validator {
    
    /**
     * Nettoie une chaîne de caractères
     * Supprime les espaces en début/fin et convertit les caractères spéciaux
     * 
     * @param string $value Valeur à nettoyer
     * @return string Valeur nettoyée
     */
    public static function clean(string $value): string {
        $value = trim($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return $value;
    }
    
    /**
     * Valide et nettoie une référence produit
     * Autorise : lettres, chiffres, tirets, underscores, points
     * 
     * @param string $reference Référence à valider
     * @param int $minLength Longueur minimale (défaut: 1)
     * @param int $maxLength Longueur maximale (défaut: 50)
     * @return string|false Référence nettoyée ou false si invalide
     */
    public static function reference(string $reference, int $minLength = 1, int $maxLength = 50) {
        $reference = trim($reference);
        
        // Vérifier la longueur
        $length = mb_strlen($reference);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        // Autoriser uniquement : lettres, chiffres, tirets, underscores, points
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $reference)) {
            return false;
        }
        
        return htmlspecialchars($reference, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valide et nettoie une désignation ou un nom
     * Autorise : texte libre mais sécurisé
     * 
     * @param string $text Texte à valider
     * @param int $minLength Longueur minimale (défaut: 1)
     * @param int $maxLength Longueur maximale (défaut: 255)
     * @return string|false Texte nettoyé ou false si invalide
     */
    public static function text(string $text, int $minLength = 1, int $maxLength = 255) {
        $text = trim($text);
        
        // Vérifier la longueur
        $length = mb_strlen($text);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        // Supprimer les balises HTML
        $text = strip_tags($text);
        
        // Échapper les caractères spéciaux
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valide un numéro de commande
     * Format flexible : lettres, chiffres, tirets, underscores, points, slash
     * 
     * @param string $numeroCommande Numéro à valider
     * @param int $minLength Longueur minimale (défaut: 1)
     * @param int $maxLength Longueur maximale (défaut: 50)
     * @return string|false Numéro nettoyé ou false si invalide
     */
    public static function numeroCommande(string $numeroCommande, int $minLength = 1, int $maxLength = 50) {
        $numeroCommande = trim($numeroCommande);
        
        // Vérifier la longueur
        $length = mb_strlen($numeroCommande);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        // Autoriser : lettres, chiffres, tirets, underscores, points, slash
        if (!preg_match('/^[a-zA-Z0-9._\/-]+$/', $numeroCommande)) {
            return false;
        }
        
        return htmlspecialchars($numeroCommande, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valide un numéro de lot
     * Format flexible : lettres, chiffres, tirets, underscores, points
     * 
     * @param string $numeroLot Numéro de lot à valider
     * @param int $minLength Longueur minimale (défaut: 1)
     * @param int $maxLength Longueur maximale (défaut: 50)
     * @return string|false Numéro nettoyé ou false si invalide
     */
    public static function numeroLot(string $numeroLot, int $minLength = 1, int $maxLength = 50) {
        $numeroLot = trim($numeroLot);
        
        // Vérifier la longueur
        $length = mb_strlen($numeroLot);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        // Autoriser : lettres, chiffres, tirets, underscores, points
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $numeroLot)) {
            return false;
        }
        
        return htmlspecialchars($numeroLot, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valide un entier positif
     * 
     * @param mixed $value Valeur à valider
     * @param int $min Valeur minimale (défaut: 0)
     * @param int $max Valeur maximale (défaut: PHP_INT_MAX)
     * @return int|false Entier ou false si invalide
     */
    public static function integer($value, int $min = 0, int $max = PHP_INT_MAX) {
        // Convertir en entier
        $int = filter_var($value, FILTER_VALIDATE_INT);
        
        // Vérifier que c'est un entier valide
        if ($int === false) {
            return false;
        }
        
        // Vérifier les limites
        if ($int < $min || $int > $max) {
            return false;
        }
        
        return $int;
    }
    
    /**
     * Valide une date au format MM/YYYY (Sartorius)
     * 
     * @param string $date Date à valider
     * @return string|false Date validée ou false si invalide
     */
    public static function dateMoisAnnee(string $date) {
        // Vérifier le format MM/YYYY
        if (!preg_match('/^\d{2}\/\d{4}$/', $date)) {
            return false;
        }
        
        // Vérifier que la date est valide
        $parts = explode('/', $date);
        $mois = (int)$parts[0];
        $annee = (int)$parts[1];
        
        // Vérifier le mois (1-12)
        if ($mois < 1 || $mois > 12) {
            return false;
        }
        
        // Vérifier l'année (raisonnable)
        if ($annee < 2000 || $annee > 2100) {
            return false;
        }
        
        return $date;
    }
    
    /**
     * Valide un tableau d'IDs
     * Utile pour les suppressions par sélection
     * 
     * @param array $ids Tableau d'IDs à valider
     * @return array|false Tableau d'entiers ou false si invalide
     */
    public static function arrayOfIds(array $ids) {
        if (empty($ids)) {
            return false;
        }
        
        $validIds = [];
        foreach ($ids as $id) {
            $validId = self::integer($id, 1);
            if ($validId === false) {
                return false;
            }
            $validIds[] = $validId;
        }
        
        return $validIds;
    }
    
    /**
     * Valide un tableau de quantités
     * Structure attendue : [{quantite_par_carton, quantite_etiquettes}, ...]
     * 
     * @param array $quantites Tableau de quantités à valider
     * @return array|false Tableau validé ou false si invalide
     */
    public static function quantitesSartorius(array $quantites) {
        if (empty($quantites)) {
            return false;
        }
        
        $validated = [];
        foreach ($quantites as $qty) {
            // Vérifier la structure
            if (!isset($qty['quantite_par_carton']) || !isset($qty['quantite_etiquettes'])) {
                return false;
            }
            
            // Valider les valeurs
            $qtyParCarton = self::integer($qty['quantite_par_carton'], 1, 10000);
            $qtyEtiquettes = self::integer($qty['quantite_etiquettes'], 1, 1000000);
            
            if ($qtyParCarton === false || $qtyEtiquettes === false) {
                return false;
            }
            
            $validated[] = [
                'quantite_par_carton' => $qtyParCarton,
                'quantite_etiquettes' => $qtyEtiquettes
            ];
        }
        
        return $validated;
    }
    
    /**
     * Valide un tableau d'articles Latitude
     * Structure attendue : [{type, quantite, nombre_cartons}, ...]
     * 
     * @param array $articles Tableau d'articles à valider
     * @return array|false Tableau validé ou false si invalide
     */
    public static function articlesLatitude(array $articles) {
        if (empty($articles)) {
            return false;
        }
        
        $validated = [];
        foreach ($articles as $article) {
            // Vérifier la structure
            if (!isset($article['type']) || !isset($article['quantite']) || !isset($article['nombre_cartons'])) {
                return false;
            }
            
            // Valider les valeurs
            $type = self::text($article['type'], 1, 100);
            $quantite = self::integer($article['quantite'], 1, 1000000);
            $nombreCartons = self::integer($article['nombre_cartons'], 1, 10000);
            
            if ($type === false || $quantite === false || $nombreCartons === false) {
                return false;
            }
            
            $validated[] = [
                'type' => $type,
                'quantite' => $quantite,
                'nombre_cartons' => $nombreCartons
            ];
        }
        
        return $validated;
    }
    
    /**
     * Vérifie si une chaîne est vide après nettoyage
     * 
     * @param mixed $value Valeur à vérifier
     * @return bool True si vide, False sinon
     */
    public static function isEmpty($value): bool {
        if ($value === null || $value === '') {
            return true;
        }
        
        if (is_string($value)) {
            return trim($value) === '';
        }
        
        if (is_array($value)) {
            return empty($value);
        }
        
        return false;
    }
    
    /**
     * Valide un ID depuis GET/POST
     * 
     * @param mixed $id ID à valider
     * @return int|false ID validé ou false si invalide
     */
    public static function id($id) {
        return self::integer($id, 1);
    }
}
