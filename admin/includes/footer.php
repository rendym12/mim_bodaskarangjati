<?php
// TUTUP MAIN CONTENT
?>
        </main> <!-- TUTUP ADMIN-MAIN -->
    </div> <!-- TUTUP ADMIN-WRAPPER -->

    <!-- Floating Hamburger untuk Mobile (Alternatif) -->
    <button class="mobile-hamburger" id="floatingMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- JavaScript -->
    <script src="<?= $base_url ?>/assets/js/admin.js"></script>
    
    <!-- Chart.js (untuk dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>

<?php
// Flush output buffer
ob_end_flush();
?>