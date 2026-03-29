<!-- Admin JavaScript -->
<script src="<?= $base_url ?>/assets/js/admin.js"></script>

<!-- Tambahan script khusus halaman -->
<?php if (isset($custom_js)): ?>
    <?= $custom_js ?>
<?php endif; ?>

</main>
</div> <!-- Tutup admin-wrapper -->
</body>
</html>
<?php ob_end_flush(); ?>