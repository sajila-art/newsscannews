<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="mb-1 fw-bold">Home Page Banner & Lead Story</h2>
        <p class="text-secondary mb-0">Update the primary front-page banner, lead title, description, and author byline.</p>
    </div>
    <div>
        <a class="btn btn-outline-dark fw-bold shadow-sm" href="http://localhost:3000" target="_blank" rel="noopener noreferrer">
            🌐 View Live Website ↗
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

<?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>') ?>

<div class="row g-4">
    <!-- Form Column -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold">Edit Home Page Settings</h5>
            </div>
            <div class="card-body p-4">
                <form method="post" action="<?= site_url('home/update') ?>" id="homeBannerForm">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <div class="row g-3">
                        <!-- Headline / Title -->
                        <div class="col-12">
                            <label class="form-label fw-bold" for="title">
                                Home Lead Headline / Title <span class="text-danger">*</span>
                            </label>
                            <input
                                id="title"
                                name="title"
                                class="form-control form-control-lg fs-6"
                                maxlength="300"
                                value="<?= html_escape(set_value('title', $banner->title)) ?>"
                                placeholder="e.g. കേരളത്തിൽ ഡിജിറ്റൽ വിപ്ലവം..."
                                required
                            >
                            <div class="form-text small text-muted">Primary headline displayed prominently on the front page hero section.</div>
                        </div>

                        <!-- Subtitle -->
                        <div class="col-12">
                            <label class="form-label fw-bold" for="subtitle">
                                Subtitle / Secondary Lead
                            </label>
                            <input
                                id="subtitle"
                                name="subtitle"
                                class="form-control"
                                maxlength="500"
                                value="<?= html_escape(set_value('subtitle', $banner->subtitle)) ?>"
                                placeholder="Brief secondary kicker line"
                            >
                        </div>

                        <!-- Banner Image URL -->
                        <div class="col-12">
                            <label class="form-label fw-bold" for="banner_image">
                                Banner Image URL
                            </label>
                            <input
                                id="banner_image"
                                name="banner_image"
                                type="text"
                                class="form-control"
                                maxlength="1000"
                                value="<?= html_escape(set_value('banner_image', $banner->banner_image)) ?>"
                                placeholder="https://images.unsplash.com/... or /uploads/banner.jpg"
                            >
                            <div class="form-text small text-muted">Direct image URL for the large front-page hero banner.</div>
                        </div>

                        <!-- Description / Excerpt -->
                        <div class="col-12">
                            <label class="form-label fw-bold" for="description">
                                Story Description / Summary <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="5"
                                placeholder="A comprehensive summary or introductory overview of the lead news story..."
                                required
                            ><?= html_escape(set_value('description', $banner->description)) ?></textarea>
                            <div class="form-text small text-muted">Displayed directly beneath the headline on the home page.</div>
                        </div>

                        <!-- Author Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="author">
                                Author / Reporter Name <span class="text-danger">*</span>
                            </label>
                            <input
                                id="author"
                                name="author"
                                class="form-control"
                                maxlength="160"
                                value="<?= html_escape(set_value('author', $banner->author)) ?>"
                                placeholder="e.g. കെ. ആർ. നാരായണൻ"
                                required
                            >
                        </div>

                        <!-- Author Role -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="author_role">
                                Author Role
                            </label>
                            <input
                                id="author_role"
                                name="author_role"
                                class="form-control"
                                maxlength="160"
                                value="<?= html_escape(set_value('author_role', $banner->author_role)) ?>"
                                placeholder="e.g. ചീഫ് റിപ്പോർട്ടർ (Chief Reporter)"
                            >
                        </div>

                        <!-- Badge / Kicker Tag -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="badge">
                                Badge / Tag Label
                            </label>
                            <input
                                id="badge"
                                name="badge"
                                class="form-control"
                                maxlength="100"
                                value="<?= html_escape(set_value('badge', $banner->badge)) ?>"
                                placeholder="e.g. പ്രധാന വാർത്ത"
                            >
                        </div>

                        <!-- Link URL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="link_url">
                                Read More Link URL
                            </label>
                            <input
                                id="link_url"
                                name="link_url"
                                class="form-control"
                                maxlength="500"
                                value="<?= html_escape(set_value('link_url', $banner->link_url)) ?>"
                                placeholder="# or /article/..."
                            >
                        </div>

                        <!-- Action Button -->
                        <div class="col-12 pt-3 border-top d-flex gap-3">
                            <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm">
                                💾 Save Changes
                            </button>
                            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary px-4 py-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Preview Column -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 sticky-top" style="top: 1.5rem;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Live Front-Page Preview</h5>
                <span class="badge bg-danger text-uppercase px-2 py-1">Hero Lead</span>
            </div>
            <div class="card-body p-4">
                <!-- Preview Image -->
                <div class="position-relative rounded-3 overflow-hidden bg-light mb-3 border" style="aspect-ratio: 16/9;">
                    <img
                        id="preview_image"
                        src="<?= html_escape($banner->banner_image) ?>"
                        alt="Banner Preview"
                        class="w-100 h-100 object-fit-cover"
                        onerror="this.src='https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1600&q=80'"
                    >
                    <span 
                        id="preview_badge" 
                        class="position-absolute top-0 start-0 m-2 px-2.5 py-1 badge bg-danger fw-bold"
                    >
                        <?= html_escape($banner->badge ?: 'പ്രധാന വാർത്ത') ?>
                    </span>
                </div>

                <!-- Byline info -->
                <div class="small text-muted mb-2 d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark" id="preview_author">
                        റിപ്പോർട്ട്: <?= html_escape($banner->author) ?>
                    </span>
                    <span>•</span>
                    <span id="preview_role" class="text-secondary">
                        <?= html_escape($banner->author_role) ?>
                    </span>
                </div>

                <!-- Preview Headline -->
                <h4 class="fw-bold text-dark mb-2" id="preview_title" style="line-height: 1.35;">
                    <?= html_escape($banner->title) ?>
                </h4>

                <!-- Preview Subtitle -->
                <p class="text-secondary small fw-semibold mb-2" id="preview_subtitle">
                    <?= html_escape($banner->subtitle) ?>
                </p>

                <!-- Preview Description -->
                <p class="text-secondary small mb-3" id="preview_description" style="line-height: 1.6;">
                    <?= nl2br(html_escape($banner->description)) ?>
                </p>

                <div class="pt-2 border-top">
                    <span class="btn btn-sm btn-outline-danger fw-bold disabled">
                        മുഴുവൻ വാർത്ത വായിക്കുക →
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Input Sync Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('title');
    const subtitleInput = document.getElementById('subtitle');
    const bannerInput = document.getElementById('banner_image');
    const descInput = document.getElementById('description');
    const authorInput = document.getElementById('author');
    const roleInput = document.getElementById('author_role');
    const badgeInput = document.getElementById('badge');

    const previewTitle = document.getElementById('preview_title');
    const previewSubtitle = document.getElementById('preview_subtitle');
    const previewImage = document.getElementById('preview_image');
    const previewDesc = document.getElementById('preview_description');
    const previewAuthor = document.getElementById('preview_author');
    const previewRole = document.getElementById('preview_role');
    const previewBadge = document.getElementById('preview_badge');

    if (titleInput) {
        titleInput.addEventListener('input', () => {
            previewTitle.textContent = titleInput.value || 'Headline Title Preview';
        });
    }
    if (subtitleInput) {
        subtitleInput.addEventListener('input', () => {
            previewSubtitle.textContent = subtitleInput.value || '';
        });
    }
    if (bannerInput) {
        bannerInput.addEventListener('input', () => {
            if (bannerInput.value) {
                previewImage.src = bannerInput.value;
            }
        });
    }
    if (descInput) {
        descInput.addEventListener('input', () => {
            previewDesc.textContent = descInput.value || 'Description preview...';
        });
    }
    if (authorInput) {
        authorInput.addEventListener('input', () => {
            previewAuthor.textContent = 'റിപ്പോർട്ട്: ' + (authorInput.value || 'Author Name');
        });
    }
    if (roleInput) {
        roleInput.addEventListener('input', () => {
            previewRole.textContent = roleInput.value || '';
        });
    }
    if (badgeInput) {
        badgeInput.addEventListener('input', () => {
            previewBadge.textContent = badgeInput.value || 'പ്രധാന വാർത്ത';
        });
    }
});
</script>
