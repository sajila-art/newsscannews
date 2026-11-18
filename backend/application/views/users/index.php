<div class="d-flex justify-content-between mb-3">
    <h2>Staff users</h2>
</div>

<div class="card p-4 mb-3" id="add-user">
    <h5>Create staff account</h5>
    <?= validation_errors('<div class="alert alert-danger">', '</div>') ?>

    <form method="post" action="<?= site_url('users/create') ?>">
        <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
        <div class="row g-2">
            <div class="col-md-3">
                <input name="name" class="form-control" placeholder="Full name" required>
            </div>
            <div class="col-md-3">
                <input name="email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-3">
                <input
                    name="password"
                    type="password"
                    minlength="12"
                    class="form-control"
                    placeholder="Temporary password (12+ chars)"
                    required
                >
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="staff">Staff</option>
                    <option value="manager">Manager</option>
                    <option value="accounts">Accounts</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary">Add</button>
            </div>
        </div>
    </form>
</div>

<div class="card p-3 table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Active</th>
                <th>Last login</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= html_escape($r['name']) ?></td>
                    <td><?= html_escape($r['email']) ?></td>
                    <td><?= html_escape($r['role']) ?></td>
                    <td><?= $r['is_active'] ? 'Yes' : 'No' ?></td>
                    <td><?= html_escape($r['last_login_at'] ?: 'Never') ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('users/edit/' . $r['id']) ?>">
                            Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
