<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold">Add New Category</h2>
        <p class="text-secondary mb-0">Create a new section for news articles, e-paper clippings, and navigation menus.</p>
    </div>
    <div>
        <a class="btn btn-outline-secondary" href="<?= site_url('category') ?>">
            ← Back to Category List
        </a>
    </div>
</div>

<?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>') ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form method="post" action="<?= site_url('category/create') ?>">
            <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

            <div class="row g-4">
                <!-- Malayalam Title -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name_malayalam">
                        Category Title (Malayalam) <span class="text-danger">*</span>
                    </label>
                    <input
                        id="name_malayalam"
                        name="name_malayalam"
                        class="form-control form-control-lg fs-6"
                        maxlength="255"
                        placeholder="ഉദാഹരണം: പ്രാദേശിക വാർത്തകൾ"
                        value="<?= html_escape(set_value('name_malayalam')) ?>"
                        required
                        autofocus
                    >
                    <div class="form-text small text-muted">The Malayalam heading displayed on the website and mobile app.</div>
                </div>

                <!-- English Title -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name_english">
                        Category Title (English) <span class="text-danger">*</span>
                    </label>
                    <input
                        id="name_english"
                        name="name_english"
                        class="form-control form-control-lg fs-6"
                        maxlength="255"
                        placeholder="e.g. Local News"
                        value="<?= html_escape(set_value('name_english')) ?>"
                        required
                    >
                    <div class="form-text small text-muted">Used for administration and automatic URL slug creation.</div>
                </div>

                <!-- Slug -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="slug">
                        URL Route Slug <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted">/categories/</span>
                        <input
                            id="slug"
                            name="slug"
                            class="form-control font-monospace"
                            maxlength="191"
                            placeholder="local-news"
                            value="<?= html_escape(set_value('slug')) ?>"
                            required
                        >
                    </div>
                    <div class="form-text small text-muted">Direct web route for this section. Auto-generated from the English title.</div>
                </div>

                <!-- Priority / Display Order -->
                <div class="col-md-2">
                    <label class="form-label fw-bold" for="display_order">
                        Order
                    </label>
                    <input
                        id="display_order"
                        name="display_order"
                        type="number"
                        class="form-control"
                        placeholder="0"
                        min="0"
                        value="<?= html_escape(set_value('display_order', '0')) ?>"
                    >
                    <div class="form-text small text-muted">Lower first.</div>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label fw-bold" for="status">
                        Status <span class="text-danger">*</span>
                    </label>
                    <?php $selected_status = set_value('status', 'active'); ?>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $selected_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <div class="form-text small text-muted">Global status.</div>
                </div>

                <!-- Home Page Select Box -->
                <div class="col-md-2">
                    <label class="form-label fw-bold" for="home_page">
                        Home Page <span class="text-danger">*</span>
                    </label>
                    <?php $selected_home_page = set_value('home_page', '1'); ?>
                    <select id="home_page" name="home_page" class="form-select" required>
                        <option value="1" <?= (string) $selected_home_page === '1' ? 'selected' : '' ?>>Yes (Show)</option>
                        <option value="0" <?= (string) $selected_home_page === '0' ? 'selected' : '' ?>>No (Hide)</option>
                    </select>
                    <div class="form-text small text-muted">Show on Home.</div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="description">
                        Description (Optional)
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="3"
                        placeholder="Brief overview of the stories featured in this category..."
                    ><?= html_escape(set_value('description')) ?></textarea>
                </div>

                <div class="col-12 pt-3 border-top d-flex justify-content-between align-items-center">
                    <a href="<?= site_url('category') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-danger px-5 fw-bold shadow-sm">
                        💾 Save Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script to automatically generate clean URL slug as you type the English name -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name_english');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            if (!slugInput.dataset.touched) {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        slugInput.addEventListener('input', function () {
            slugInput.dataset.touched = 'true';
        });
    }
});
</script>
