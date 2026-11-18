<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold">Categories</h2>
        <p class="text-secondary mb-0">Manage news channels, navigation sections, and Malayalam titles.</p>
    </div>
    <div>
        <a class="btn btn-danger fw-bold shadow-sm" href="<?= site_url('category/add') ?>">
            ➕ Add Category
        </a>
    </div>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-3" style="width: 60px;">ID</th>
                    <th>Malayalam Title</th>
                    <th>English Title</th>
                    <th>Route Slug</th>
                    <th class="text-center" style="width: 90px;">Priority</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-center" style="width: 110px;">Home Page</th>
                    <th class="text-end pe-3" style="width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="ps-3 text-muted fw-bold">#<?= (int) $row->id ?></td>
                            <td>
                                <span class="fw-bold fs-6 text-dark" style="font-family: 'Anek Malayalam', sans-serif;">
                                    <?= html_escape($row->name_malayalam) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">
                                    <?= html_escape($row->name_english) ?>
                                </span>
                            </td>
                            <td>
                                <code class="bg-light text-danger px-2 py-1 rounded border">
                                    <?= html_escape($row->slug) ?>
                                </code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    <?= (int) $row->display_order ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($row->status === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (isset($row->home_page) && (int) $row->home_page === 1): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                        ✓ Home
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">
                                        Hidden
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <a class="btn btn-sm btn-outline-primary me-1" href="<?= site_url('category/edit/' . $row->id) ?>" title="Edit Category">
                                    ✏️ Edit
                                </a>
                                <a class="btn btn-sm btn-outline-danger" 
                                   href="<?= site_url('category/delete/' . $row->id) ?>" 
                                   onclick="return confirm('Are you sure you want to delete this category?');" 
                                   title="Delete Category">
                                    🗑️
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <p class="mb-2 fs-5">No categories found in the database.</p>
                            <a href="<?= site_url('category/add') ?>" class="btn btn-sm btn-primary">Create your first category</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
