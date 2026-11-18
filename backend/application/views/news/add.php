<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="mb-1 fw-bold">Create News Article</h2>
        <p class="text-secondary mb-0">Publish a new story across regional categories, N-News e-paper, and web portal.</p>
    </div>
    <div>
        <a class="btn btn-outline-secondary" href="<?= site_url('news') ?>">
            ← Back to News List
        </a>
    </div>
</div>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?= validation_errors('<div class="alert alert-danger alert-dismissible fade show" role="alert">', '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>') ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4 p-md-5">
        <form method="post" action="<?= site_url('news/create') ?>" enctype="multipart/form-data">
            <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
            <?php $selected_category = set_value('category_id'); ?>

            <div class="row g-4">
                <!-- Headline -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="title">
                        Headline <span class="text-danger">*</span>
                    </label>
                    <input 
                        id="title" 
                        name="title" 
                        class="form-control form-control-lg fs-6" 
                        maxlength="300" 
                        value="<?= html_escape(set_value('title')) ?>" 
                        placeholder="e.g., തിരുവനന്തപുരത്ത് പുതിയ ഐടി പാർക്ക് പ്രവർത്തനമാരംഭിച്ചു" 
                        required
                    >
                </div>

                <!-- Subtitle -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="subtitle">Subtitle / Secondary Heading</label>
                    <input 
                        id="subtitle" 
                        name="subtitle" 
                        class="form-control" 
                        maxlength="500" 
                        value="<?= html_escape(set_value('subtitle')) ?>" 
                        placeholder="Brief secondary line or lead-in statement"
                    >
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="category_id">
                        News Category <span class="text-danger">*</span>
                    </label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Select a category</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <?php 
                                    $cat_id = is_array($category) ? $category['id'] : $category->id;
                                    $cat_ml = is_array($category) ? $category['name_malayalam'] : $category->name_malayalam;
                                    $cat_en = is_array($category) ? $category['name_english'] : $category->name_english;
                                    $cat_st = is_array($category) ? $category['status'] : $category->status;
                                    $category_label = $cat_ml . ' — ' . $cat_en; 
                                ?>
                                <option value="<?= (int) $cat_id ?>" <?= (string) $selected_category === (string) $cat_id ? 'selected' : '' ?>>
                                    <?= html_escape($category_label . ($cat_st === 'inactive' ? ' (inactive)' : '')) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($categories)): ?>
                        <div class="form-text text-danger mt-1">
                            No categories found in database. 
                            <a href="<?= site_url('category/add') ?>" class="fw-bold">Create a category first</a>.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="content">
                        Article Content <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        id="content" 
                        name="content" 
                        class="form-control" 
                        rows="9" 
                        placeholder="Full body of the news article. Separate paragraphs with a blank line." 
                        required
                    ><?= html_escape(set_value('content')) ?></textarea>
                    <div class="form-text text-muted">Separate multiple paragraphs by leaving a blank line between them.</div>
                </div>

                <!-- Featured Image Upload -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="image_file">Featured Image</label>
                    <div class="input-group">
                        <input 
                            id="image_file" 
                            name="image_file" 
                            type="file" 
                            class="form-control" 
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/gif"
                        >
                        <label class="input-group-text btn btn-outline-secondary" for="image_file">
                            Upload
                        </label>
                    </div>
                    <div class="form-text text-muted">Upload article image (JPG, PNG, WEBP, GIF — max 5MB).</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" for="image_caption">Image Caption & Credits</label>
                    <input 
                        id="image_caption" 
                        name="image_caption" 
                        class="form-control" 
                        maxlength="500" 
                        value="<?= html_escape(set_value('image_caption')) ?>" 
                        placeholder="Photo: N-NEWS Bureau / Special Correspondent"
                    >
                </div>

                <!-- Author Info -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="author_name">
                        Author Name <span class="text-danger">*</span>
                    </label>
                    <input 
                        id="author_name" 
                        name="author_name" 
                        class="form-control" 
                        maxlength="160" 
                        value="<?= html_escape(set_value('author_name', !empty($current_user['name']) ? $current_user['name'] : 'Staff Reporter')) ?>" 
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" for="author_role">
                        Author Role <span class="text-danger">*</span>
                    </label>
                    <input 
                        id="author_role" 
                        name="author_role" 
                        class="form-control" 
                        maxlength="160" 
                        value="<?= html_escape(set_value('author_role', 'Staff Reporter')) ?>" 
                        required
                    >
                </div>

                <!-- Author Avatar -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="author_avatar">Author Avatar URL</label>
                    <input 
                        id="author_avatar" 
                        name="author_avatar" 
                        type="text" 
                        class="form-control" 
                        maxlength="500" 
                        value="<?= html_escape(set_value('author_avatar')) ?>" 
                        placeholder="https://... avatar image"
                    >
                </div>

                <!-- Publication Time -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="published_at">
                        Publication Date & Time <span class="text-danger">*</span>
                    </label>
                    <input 
                        id="published_at" 
                        name="published_at" 
                        type="datetime-local" 
                        class="form-control" 
                        value="<?= html_escape(set_value('published_at', date('Y-m-d\TH:i'))) ?>" 
                        required
                    >
                </div>

                <!-- Read Time -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="read_time">
                        Read Time <span class="text-danger">*</span>
                    </label>
                    <input 
                        id="read_time" 
                        name="read_time" 
                        class="form-control" 
                        maxlength="40" 
                        value="<?= html_escape(set_value('read_time', '3 min read')) ?>" 
                        required
                    >
                </div>

                <!-- Tags -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="tags">Tags / Keywords</label>
                    <input 
                        id="tags" 
                        name="tags" 
                        class="form-control" 
                        value="<?= html_escape(set_value('tags')) ?>" 
                        placeholder="e.g. Kerala, Politics, Development, IT (separated by commas)"
                    >
                </div>

                <!-- Editorial Flags -->
                <div class="col-12">
                    <div class="p-3 bg-light rounded border">
                        <label class="form-label fw-bold d-block mb-2">Editorial Flags & Display Placement</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input id="is_breaking" name="is_breaking" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_breaking', '1') ?>>
                                <label class="form-check-label fw-semibold text-danger" for="is_breaking">
                                    🚨 Breaking News Ticker
                                </label>
                            </div>
                            <div class="form-check">
                                <input id="is_featured" name="is_featured" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_featured', '1') ?>>
                                <label class="form-check-label fw-semibold text-primary" for="is_featured">
                                    ★ Top Hero / Lead Story
                                </label>
                            </div>
                            <div class="form-check">
                                <input id="is_trending" name="is_trending" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_trending', '1') ?>>
                                <label class="form-check-label fw-semibold text-warning-emphasis" for="is_trending">
                                    🔥 Trending Sidebar
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Publish & Add News side-by-side -->
                <div class="col-12 pt-3 d-flex flex-wrap align-items-center gap-3">
                    <button type="submit" name="submit_action" value="publish" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" <?= empty($categories) ? 'disabled' : '' ?>>
                        🚀 Publish News
                    </button>
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline-dark fw-bold px-4 py-2" <?= empty($categories) ? 'disabled' : '' ?>>
                        📝 Add News (Save as Draft)
                    </button>
                    <a href="<?= site_url('news') ?>" class="btn btn-outline-secondary px-4 py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
