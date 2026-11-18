<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? html_escape($title) . ' — ' : '' ?>N-NEWS Admin</title>
    <link rel="icon" href="<?= base_url('assets/logo.jpg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/style.css') ?>" rel="stylesheet">
</head>
<?php
$active_section = $this->uri->segment(1) ?: 'dashboard';
$is_admin = isset($current_user) && $current_user['role'] === 'admin';
?>
<body class="<?= $is_admin ? 'admin-body' : '' ?>">
<?php if ($is_admin): ?>
    <nav
        class="admin-sidebar offcanvas-md offcanvas-start"
        tabindex="-1"
        id="adminSidebar"
        aria-labelledby="adminSidebarLabel"
    >
        <div class="admin-sidebar-brand">
            <a class="d-flex align-items-center gap-2 text-decoration-none" href="<?= site_url('dashboard') ?>">
                <img src="<?= base_url('assets/logo.jpg') ?>" alt="N-NEWS Logo" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                <span>
                    <span id="adminSidebarLabel" class="admin-sidebar-brand-name">N-NEWS</span>
                    <span class="admin-sidebar-brand-badge">Newsroom Admin</span>
                </span>
            </a>
            <button type="button" class="btn-close d-md-none" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
        </div>

        <div class="offcanvas-body admin-sidebar-body">
            <div class="admin-sidebar-section-label">Workspace</div>
            <ul class="nav flex-column admin-sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $active_section === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
                        <span class="admin-sidebar-icon">▦</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_section === 'home' ? 'active' : '' ?>" href="<?= site_url('home') ?>">
                        <span class="admin-sidebar-icon">🏠</span>
                        <span>Home Banner</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_section === 'audio' ? 'active' : '' ?>" href="<?= site_url('audio') ?>">
                        <span class="admin-sidebar-icon">🔊</span>
                        <span>Today's Audio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_section === 'subscriptions' ? 'active' : '' ?>" href="<?= site_url('subscriptions') ?>">
                        <span class="admin-sidebar-icon">✉</span>
                        <span>Subscriptions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link admin-sidebar-toggle <?= $active_section === 'news' ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#adminNewsMenu"
                        aria-expanded="<?= $active_section === 'news' ? 'true' : 'false' ?>"
                        aria-controls="adminNewsMenu"
                    >
                        <span class="admin-sidebar-icon">▤</span>
                        <span>News</span>
                        <span class="admin-sidebar-chevron">⌄</span>
                    </button>
                    <ul
                        class="nav flex-column admin-sidebar-submenu collapse <?= $active_section === 'news' ? 'show' : '' ?>"
                        id="adminNewsMenu"
                    >
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('news') ?>">News list</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('news/add') ?>">Add news</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link admin-sidebar-toggle <?= $active_section === 'category' ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#adminCategoryMenu"
                        aria-expanded="<?= $active_section === 'category' ? 'true' : 'false' ?>"
                        aria-controls="adminCategoryMenu"
                    >
                        <span class="admin-sidebar-icon">◇</span>
                        <span>Categories</span>
                        <span class="admin-sidebar-chevron">⌄</span>
                    </button>
                    <ul
                        class="nav flex-column admin-sidebar-submenu collapse <?= $active_section === 'category' ? 'show' : '' ?>"
                        id="adminCategoryMenu"
                    >
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('category') ?>">Category list</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('category/add') ?>">Add category</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link admin-sidebar-toggle <?= $active_section === 'users' ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#adminUsersMenu"
                        aria-expanded="<?= $active_section === 'users' ? 'true' : 'false' ?>"
                        aria-controls="adminUsersMenu"
                    >
                        <span class="admin-sidebar-icon">♙</span>
                        <span>Users</span>
                        <span class="admin-sidebar-chevron">⌄</span>
                    </button>
                    <ul
                        class="nav flex-column admin-sidebar-submenu collapse <?= $active_section === 'users' ? 'show' : '' ?>"
                        id="adminUsersMenu"
                    >
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('users') ?>">Users list</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('users#add-user') ?>">Add user</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="admin-sidebar-footer mt-auto">
                <div class="admin-sidebar-user mb-2">
                    <span class="account-avatar"><?= html_escape(strtoupper(substr($current_user['name'], 0, 1))) ?></span>
                    <span class="admin-sidebar-user-details">
                        <span class="admin-sidebar-user-name"><?= html_escape($current_user['name']) ?></span>
                        <span class="admin-sidebar-user-role">Administrator</span>
                    </span>
                </div>
                <a class="btn btn-outline-danger btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm mt-2"
                   href="<?= site_url('login/logout') ?>"
                   role="button"
                   title="Sign Out">
                    <span>🚪</span>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="admin-main">
        <div class="admin-mobile-bar d-md-none">
            <button
                class="btn admin-mobile-menu-button"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#adminSidebar"
                aria-controls="adminSidebar"
                aria-label="Open admin menu"
            >☰</button>
            <a class="admin-mobile-brand" href="<?= site_url('dashboard') ?>">N-NEWS</a>
            <a class="btn btn-sm btn-outline-danger fw-semibold" href="<?= site_url('login/logout') ?>">Sign Out</a>
        </div>
<?php else: ?>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= site_url('dashboard') ?>">
                <span class="brand-mark">F</span>
                <span>FYNTRIC TECH</span>
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#nav"
                aria-controls="nav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <?php foreach (array(
                        'dashboard' => 'Dashboard',
                        'clients' => 'Clients',
                        'projects' => 'Projects',
                        'documents' => 'Documents',
                        'notices' => 'Notices'
                    ) as $section => $label): ?>
                        <li class="nav-item">
                            <a
                                class="nav-link <?= $active_section === $section ? 'active' : '' ?>"
                                href="<?= site_url($section) ?>"
                                <?= $active_section === $section ? 'aria-current="page"' : '' ?>
                            >
                                <?= $label ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle <?= in_array($active_section, array('quotations', 'sows'), TRUE) ? 'active' : '' ?>"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >Quotations</a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?= $active_section === 'quotations' ? 'active' : '' ?>" href="<?= site_url('quotations') ?>">
                                    Quotations
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $active_section === 'sows' ? 'active' : '' ?>" href="<?= site_url('sows') ?>">
                                    Statements of Work
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php if (isset($current_user) && in_array($current_user['role'], array('admin', 'manager', 'accounts'), TRUE)): ?>
                        <li class="nav-item">
                            <a
                                class="nav-link <?= $active_section === 'payments' ? 'active' : '' ?>"
                                href="<?= site_url('payments') ?>"
                                <?= $active_section === 'payments' ? 'aria-current="page"' : '' ?>
                            >Payments</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if (isset($current_user) && $current_user): ?>
                    <div class="d-flex align-items-center flex-wrap">
                        <span class="account-chip">
                            <span class="account-avatar"><?= html_escape(strtoupper(substr($current_user['name'], 0, 1))) ?></span>
                            <span><?= html_escape($current_user['name']) ?> <span class="text-white-50">· <?= html_escape($current_user['role']) ?></span></span>
                        </span>
                        <a class="btn btn-outline-light btn-sm" href="<?= site_url('logout') ?>">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<?php endif; ?>
<main class="container py-4 <?= $is_admin ? 'admin-content' : '' ?>">
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')) ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div>
    <?php endif; ?>
