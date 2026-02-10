/**
 * Système d'alertes personnalisé
 */

// Fonction pour afficher une alerte
function showCustomAlert(message, type = 'success', duration = 5000) {
    // Types disponibles: success, danger, warning, info
    
    // Créer l'élément d'alerte
    const alert = document.createElement('div');
    alert.className = `custom-alert alert-${type}`;
    
    // Déterminer l'icône selon le type
    let icon = '';
    switch(type) {
        case 'success':
            icon = '✓';
            break;
        case 'danger':
            icon = '✕';
            break;
        case 'warning':
            icon = '⚠';
            break;
        case 'info':
            icon = 'i';
            break;
        default:
            icon = '•';
    }
    
    // Construire le HTML de l'alerte
    alert.innerHTML = `
        <div class="alert-icon">${icon}</div>
        <div class="alert-content">${message}</div>
        <button class="alert-close" onclick="closeCustomAlert(this)">&times;</button>
    `;
    
    // Ajouter l'alerte au body
    document.body.appendChild(alert);
    
    // Forcer un reflow pour que l'animation fonctionne
    alert.offsetHeight;
    
    // Ajouter la classe show
    alert.classList.add('show');
    
    // Fermer automatiquement après la durée spécifiée
    if(duration > 0) {
        setTimeout(() => {
            closeCustomAlert(alert.querySelector('.alert-close'));
        }, duration);
    }
}

// Fonction pour fermer une alerte
function closeCustomAlert(button) {
    const alert = button.closest('.custom-alert');
    if(alert) {
        alert.classList.remove('show');
        alert.classList.add('hide');
        
        // Supprimer l'élément après l'animation
        setTimeout(() => {
            alert.remove();
        }, 500);
    }
}

// Fonction pour afficher les alertes depuis les paramètres URL (pour les redirections PHP)
function showAlertsFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Vérifier les messages de succès
    const success = urlParams.get('success');
    if(success) {
        let message = '';
        switch(success) {
            case 'reference_created':
                message = 'La référence a été créée avec succès.';
                break;
            case 'commande_created':
                message = 'La commande a été créée avec succès.';
                break;
            case 'commande_updated':
                message = 'La commande a été modifiée avec succès.';
                break;
            case 'commande_deleted':
                message = 'La commande a été supprimée avec succès.';
                break;
            case 'pdf_cleared':
                const count = urlParams.get('count') || 0;
                message = `${count} fichier(s) PDF supprimé(s) avec succès.`;
                break;
            case 'all_deleted':
                message = 'Toutes les commandes ont été supprimées avec succès.';
                break;
            default:
                message = 'Opération réussie.';
        }
        showCustomAlert(message, 'success');
    }
    
    // Vérifier les messages d'erreur
    const error = urlParams.get('error');
    if(error) {
        let message = '';
        switch(error) {
            case 'delete':
                message = 'Erreur lors de la suppression de la commande.';
                break;
            case 'not_found':
                message = 'Commande introuvable.';
                break;
            case 'pdf_clear_failed':
                message = 'Erreur lors de la suppression des fichiers PDF.';
                break;
            case 'delete_all_failed':
                message = 'Erreur lors de la suppression des commandes.';
                break;
            case 'duplicate_reference':
                message = 'Cette référence existe déjà dans la base de données.';
                break;
            case 'duplicate_combination':
                message = 'Une référence identique avec la même désignation existe déjà.';
                break;
            case 'create_failed':
                message = 'Erreur lors de la création.';
                break;
            case 'update_failed':
                message = 'Erreur lors de la modification.';
                break;
            case 'pdf_not_found':
                message = 'Le fichier PDF est introuvable.';
                break;
            case 'pdf_generation_failed':
                message = 'Erreur lors de la génération du PDF.';
                break;
            default:
                message = 'Une erreur est survenue.';
        }
        showCustomAlert(message, 'danger');
    }
    
    // Nettoyer l'URL après affichage des alertes
    if(success || error) {
        // Créer une nouvelle URL sans les paramètres success/error
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('success');
        newUrl.searchParams.delete('error');
        newUrl.searchParams.delete('count');
        
        // Remplacer l'URL dans l'historique sans recharger la page
        window.history.replaceState({}, document.title, newUrl);
    }
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    showAlertsFromURL();
});
