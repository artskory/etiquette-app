<?php
/**
 * Protection CSRF (Cross-Site Request Forgery)
 * Version 1.0.11
 * 
 * Génère et valide des tokens CSRF pour protéger les formulaires
 * contre les attaques de falsification de requêtes inter-sites.
 */

class CsrfToken {
    
    /**
     * Nom de la clé de session pour le token
     */
    private const SESSION_KEY = 'csrf_token';
    
    /**
     * Nom du champ de formulaire pour le token
     */
    public const FIELD_NAME = 'csrf_token';
    
    /**
     * Génère un nouveau token CSRF
     * 
     * @return string Le token généré
     */
    public static function generate(): string {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Générer un token aléatoire cryptographiquement sûr
        $token = bin2hex(random_bytes(32)); // 64 caractères
        
        // Stocker en session
        $_SESSION[self::SESSION_KEY] = $token;
        
        return $token;
    }
    
    /**
     * Obtient le token actuel (ou en génère un nouveau si absent)
     * 
     * @return string Le token actuel
     */
    public static function get(): string {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si pas de token en session, en générer un
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return self::generate();
        }
        
        return $_SESSION[self::SESSION_KEY];
    }
    
    /**
     * Valide un token CSRF
     * 
     * @param string|null $token Le token à valider
     * @return bool True si valide, False sinon
     */
    public static function validate(?string $token): bool {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier que le token existe en session
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        
        // Vérifier que le token fourni n'est pas vide
        if (empty($token)) {
            return false;
        }
        
        // Comparer les tokens de manière sécurisée (timing-safe)
        $sessionToken = $_SESSION[self::SESSION_KEY];
        $isValid = hash_equals($sessionToken, $token);
        
        // Si valide, régénérer un nouveau token (protection contre réutilisation)
        if ($isValid) {
            self::generate();
        }
        
        return $isValid;
    }
    
    /**
     * Génère le champ HTML hidden pour le formulaire
     * 
     * @return string Le code HTML du champ hidden
     */
    public static function field(): string {
        $token = self::get();
        $fieldName = self::FIELD_NAME;
        
        return '<input type="hidden" name="' . htmlspecialchars($fieldName) . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Détruit le token actuel (logout, par exemple)
     */
    public static function destroy(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION[self::SESSION_KEY]);
    }
}
