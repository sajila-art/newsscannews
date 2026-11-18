<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Edit category</h2>
        <p class="text-secondary mb-0">Update category details and display status.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('category') ?>">Back to categories</a>
</div>

<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>

<form method="post" action="<?= site_url('category/update/' . $category->id) ?>" class="card p-4">
    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name_malayalam">Malayalam name</label>
            <input
                id="name_malayalam"
                name="name_malayalam"
                class="form-control"
                maxlength="255"
                value="<?= html_escape(set_value('name_malayalam', $category->name_malayalam)) ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label class="form-label" for="name_english">English name</label>
            <input
                id="name_english"
                name="name_english"
                class="form-control"
                maxlength="255"
                value="<?= html_escape(set_value('name_english', $category->name_english)) ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label class="form-label" for="slug">Slug</label>
            <input
                id="slug"
                name="slug"
                class="form-control"
                maxlength="191"
                value="<?= html_escape(set_value('slug', $category->slug)) ?>"
                required
            >
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold" for="display_order">Order</label>
            <input
                id="display_order"
                name="display_order"
                type="number"
                class="form-control"
                value="<?= html_escape(set_value('display_order', $category->display_order)) ?>"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold" for="status">Status</label>
            <?php $selected_status = set_value('status', $category->status); ?>
            <select id="status" name="status" class="form-select" required>
                <option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $selected_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold" for="home_page">Home Page</label>
            <?php 
                $default_home_page = isset($category->home_page) ? (string) $category->home_page : '1';
                $selected_home_page = set_value('home_page', $default_home_page); 
            ?>
            <select id="home_page" name="home_page" class="form-select" required>
                <option value="1" <?= (string) $selected_home_page === '1' ? 'selected' : '' ?>>Yes (Show)</option>
                <option value="0" <?= (string) $selected_home_page === '0' ? 'selected' : '' ?>>No (Hide)</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= html_escape(
                set_value('description', $category->description)
            ) ?></textarea>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('category') ?>">Cancel</a>
    </div>
</form>
