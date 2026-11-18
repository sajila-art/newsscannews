<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="mb-1 fw-bold">News Articles</h2>
        <p class="text-secondary mb-0">Manage published stories, drafts, categories, and highlights.</p>
    </div>
    <div>
        <a class="btn btn-danger fw-bold shadow-sm" href="<?= site_url('news/add') ?>">
            ➕ Add News
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
                    <th style="width: 34%;">Headline</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 14%;">Author & Date</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Highlights</th>
                    <th style="width: 14%;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($row['image_url'])): ?>
                                        <img 
                                            src="<?= html_escape($row['image_url']) ?>" 
                                            alt="Thumbnail" 
                                            class="rounded border flex-shrink-0" 
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            onerror="this.style.display='none'"
                                        >
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 380px;">
                                            <?= html_escape($row['title']) ?>
                                        </div>
                                        <?php if (!empty($row['subtitle'])): ?>
                                            <div class="text-muted small text-truncate" style="max-width: 380px;">
                                                <?= html_escape($row['subtitle']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $cat_name = !empty($row['name_malayalam']) 
                                        ? $row['name_malayalam'] . ' (' . ($row['name_english'] ?? '') . ')'
                                        : ($row['name_english'] ?? 'Unassigned');
                                ?>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <?= html_escape($cat_name) ?>
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <?= html_escape(!empty($row['author_name']) ? $row['author_name'] : 'Staff Reporter') ?>
                                </div>
                                <div class="text-muted small">
                                    <?php 
                                        $pub_time = !empty($row['published_at']) ? strtotime($row['published_at']) : false;
                                        echo $pub_time ? date('d M Y, h:i A', $pub_time) : '—';
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($row['status']) && $row['status'] === 'draft'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        Draft
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        Published
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if (!empty($row['is_breaking'])): ?>
                                        <span class="badge bg-danger">Breaking</span>
                                    <?php endif; ?>
                                    <?php if (!empty($row['is_featured'])): ?>
                                        <span class="badge bg-primary">Featured</span>
                                    <?php endif; ?>
                                    <?php if (!empty($row['is_trending'])): ?>
                                        <span class="badge bg-warning text-dark">Trending</span>
                                    <?php endif; ?>
                                    <?php if (empty($row['is_breaking']) && empty($row['is_featured']) && empty($row['is_trending'])): ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1">
                                    <?php if (!empty($row['status']) && $row['status'] === 'draft'): ?>
                                        <a 
                                            class="btn btn-sm btn-success fw-bold px-2 py-1" 
                                            href="<?= site_url('news/publish/' . rawurlencode($row['id'])) ?>"
                                            title="Publish this article immediately"
                                        >
                                            Publish
                                        </a>
                                    <?php else: ?>
                                        <a 
                                            class="btn btn-sm btn-outline-secondary px-2 py-1" 
                                            href="<?= site_url('news/unpublish/' . rawurlencode($row['id'])) ?>"
                                            title="Move back to Draft (Unpublish)"
                                        >
                                            Unpublish
                                        </a>
                                    <?php endif; ?>
                                    <a 
                                        class="btn btn-sm btn-outline-primary px-2 py-1" 
                                        href="<?= site_url('news/edit/' . rawurlencode($row['id'])) ?>"
                                        title="Edit Article"
                                    >
                                        Edit
                                    </a>
                                    <a 
                                        class="btn btn-sm btn-outline-danger px-2 py-1" 
                                        href="<?= site_url('news/delete/' . rawurlencode($row['id'])) ?>" 
                                        onclick="return confirm('Are you sure you want to delete this article: &quot;<?= html_escape(addslashes($row['title'])) ?>&quot;?');"
                                        title="Delete Article"
                                    >
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-5 text-center">
                            <div class="text-muted mb-3" style="font-size: 2.5rem;">📰</div>
                            <h5 class="fw-bold mb-1">No News Articles Found</h5>
                            <p class="text-secondary small mb-3">You have not created any articles yet.</p>
                            <a class="btn btn-danger btn-sm fw-bold px-3 py-2 shadow-sm" href="<?= site_url('news/add') ?>">
                                ➕ Add News
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($total_rows)): ?>
        <div class="card-footer bg-white border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 py-3">
            <?php
                $start = $offset + 1;
                $end = min($offset + count($rows), (int) $total_rows);
            ?>
            <div class="small text-muted">
                Showing <?= $start ?> to <?= $end ?> of <?= (int) $total_rows ?> articles
            </div>
            <?php if (!empty($pagination)): ?>
                <nav aria-label="News pagination">
                    <?= str_replace('<ul class="pagination">', '<ul class="pagination pagination-sm mb-0">', $pagination) ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
