    </div>
    
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="text-muted mb-0">
                <small>Version <?php echo APP_VERSION; ?></small>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Alerts JS -->
    <script src="assets/js/custom-alerts.js"></script>
    
    <script>
        // Confirmation de suppression
        function confirmerSuppression(id) {
            if(confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
                document.getElementById('deleteForm-' + id).submit();
            }
        }
    </script>
</body>
</html>
