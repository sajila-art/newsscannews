<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Edit user</h2>
        <p class="text-secondary mb-0">Update account details and access.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('users') ?>">Back to users</a>
</div>

<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>

<form method="post" action="<?= site_url('users/update/' . $user['id']) ?>" class="card p-4">
    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Full name</label>
            <input
                id="name"
                name="name"
                class="form-control"
                maxlength="150"
                value="<?= html_escape(set_value('name', $user['name'])) ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label class="form-label" for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control"
                value="<?= html_escape(set_value('email', $user['email'])) ?>"
                required
            >
        </div>

        <div class="col-md-6">
            <label class="form-label" for="role">Role</label>
            <?php $selected_role = set_value('role', $user['role']); ?>
            <select id="role" name="role" class="form-select" required>
                <option value="staff" <?= $selected_role === 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="manager" <?= $selected_role === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="accounts" <?= $selected_role === 'accounts' ? 'selected' : '' ?>>Accounts</option>
                <option value="admin" <?= $selected_role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="password">New password</label>
            <input
                id="password"
                name="password"
                type="password"
                class="form-control"
                minlength="12"
                autocomplete="new-password"
                placeholder="Leave blank to keep the current password"
            >
            <div class="form-text">If changing the password, use at least 12 characters.</div>
        </div>

        <div class="col-12">
            <div class="form-check">
                <input
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    class="form-check-input"
                    value="1"
                    <?= set_checkbox('is_active', '1', (bool) $user['is_active']) ?>
                >
                <label class="form-check-label" for="is_active">Account is active</label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('users') ?>">Cancel</a>
    </div>
</form>
