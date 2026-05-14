    <?php if (isset($_SESSION['user_id'])): ?>
            </main>
            <footer class="text-center py-3 mt-auto text-secondary small border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="container-fluid">
                    &copy; <?php echo date('Y'); ?> VENTO-corp Inventory System. All rights reserved.
                </div>
            </footer>
        </div> <!-- End Main Content Wrapper -->
    </div> <!-- End d-flex wrapper -->
    <?php else: ?>
            </main>
            <footer class="text-center py-3 mt-auto text-secondary small border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <div class="container-fluid">
                    &copy; <?php echo date('Y'); ?> VENTO-corp Inventory System. All rights reserved.
                </div>
            </footer>
    <?php endif; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
