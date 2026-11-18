<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — N-NEWS</title>
    <link rel="icon" href="<?= base_url('assets/logo.jpg') ?>">
    <link href="<?= base_url('assets/style.css') ?>" rel="stylesheet">
</head>
<body class="login-page">

<div class="login">
    <div class="text-center mb-4">
        <img src="<?= base_url('assets/logo.jpg') ?>" alt="N-NEWS Logo" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; margin: 0 auto 8px; display: block; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
        <h2 style="font-weight: 900; letter-spacing: -1px; margin: 0; color: #b91c1c;">N-NEWS</h2>
        <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: #64748b;">Newsroom Administration</span>
    </div>

    <h3 class="fw-bold login-title">Welcome back</h3>

    <p class="login-subtitle">
        Sign in to your administration workspace.
    </p>

    <?php if ($this->session->flashdata('error')): ?>
        <p class="login-error">
            <?= html_escape($this->session->flashdata('error')) ?>
        </p>
    <?php endif; ?>

    <?= validation_errors('<p class="login-error">', '</p>') ?>

    <form method="post" action="<?= site_url('login/authenticate') ?>">
        <input
            type="hidden"
            name="<?= $this->security->get_csrf_token_name() ?>"
            value="<?= $this->security->get_csrf_hash() ?>">

        <div class="form-group">
            <label for="login_id">Username or Email</label>
            <input
                type="text"
                id="login_id"
                name="login_id"
                required
                autocomplete="username"
                placeholder="admin or admin@gmail.com"
                value="<?= html_escape($this->input->post('login_id')) ?>">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter password">
        </div>

        <button type="submit">Sign in</button>
    </form>

    <div style="margin-top: 20px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; text-align: center; color: #475569;">
        <strong>Default Admin Login:</strong><br>
        Username: <code style="color: #b91c1c; font-weight: bold;">admin</code> &nbsp;|&nbsp; 
        Password: <code style="color: #b91c1c; font-weight: bold;">admin123</code>
    </div>
</div>

</body>
</html>
