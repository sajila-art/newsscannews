</main>
<footer class="border-top bg-white">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 <?= isset($current_user) && $current_user['role'] === 'admin' ? 'admin-footer-content' : '' ?>">
        <span class="small text-secondary">© <?= date('Y') ?> N-NEWS • News Portal</span>
        <span class="small text-secondary"><?= isset($current_user) && $current_user['role'] === 'admin' ? 'Newsroom administration' : 'Business operations workspace' ?></span>
    </div>
</footer>
<?php if (isset($current_user) && $current_user['role'] === 'admin'): ?>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
