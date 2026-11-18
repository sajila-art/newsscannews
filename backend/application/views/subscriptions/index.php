<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h2 class="mb-0">Newsletter Subscriptions</h2>
        <p class="text-muted small mb-0">E-paper email subscribers from <code>newsletter_subscriptions</code></p>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Total Subscribers</div>
            <div class="fs-4 fw-bold"><?= (int) $total_count ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Active (Subscribed)</div>
            <div class="fs-4 fw-bold text-success"><?= (int) $subscribed_count ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <div class="text-muted small">Unsubscribed</div>
            <div class="fs-4 fw-bold text-secondary"><?= (int) $unsubscribed_count ?></div>
        </div>
    </div>
</div>

<div class="card p-3 mb-3">
    <form method="get" action="<?= site_url('subscriptions') ?>" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input
                type="text"
                name="q"
                value="<?= html_escape($search) ?>"
                class="form-control"
                placeholder="Search by email..."
            >
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="subscribed" <?= $status_filter === 'subscribed' ? 'selected' : '' ?>>Subscribed</option>
                <option value="unsubscribed" <?= $status_filter === 'unsubscribed' ? 'selected' : '' ?>>Unsubscribed</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($search !== '' || $status_filter !== ''): ?>
                <a href="<?= site_url('subscriptions') ?>" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card p-3 table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Status</th>
                <th>Subscribed At</th>
                <th>Unsubscribed At</th>
                <th>IP Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No newsletter subscriptions found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td class="fw-semibold"><?= html_escape($row['email']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'subscribed'): ?>
                                <span class="badge bg-success">Subscribed</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Unsubscribed</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= html_escape($row['subscribed_at'] ?: '—') ?></td>
                        <td class="small"><?= html_escape($row['unsubscribed_at'] ?: '—') ?></td>
                        <td class="small font-monospace"><?= html_escape($row['ip_address'] ?: '—') ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <form method="post" action="<?= site_url('subscriptions/toggle/' . (int) $row['id']) ?>" class="d-inline">
                                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                                    <?php if ($row['status'] === 'subscribed'): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning">Unsubscribe</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success">Resubscribe</button>
                                    <?php endif; ?>
                                </form>
                                <form
                                    method="post"
                                    action="<?= site_url('subscriptions/delete/' . (int) $row['id']) ?>"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete subscription for <?= html_escape($row['email']) ?>?');"
                                >
                                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
