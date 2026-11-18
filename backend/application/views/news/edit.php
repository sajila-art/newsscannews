<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h2 class="mb-1">Edit news</h2>
        <p class="text-secondary mb-0">Update the story and its category.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('news') ?>">Back to news</a>
</div>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= html_escape($this->session->flashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?= validation_errors('<div class="alert alert-danger">', '</div>') ?>

<?php
$content_paragraphs = json_decode($article['content_json'], TRUE);
$tags = json_decode($article['tags_json'], TRUE);
if (!is_array($content_paragraphs) || !is_array($tags)) {
    show_error('The saved article content or tags are invalid JSON.', 500);
}
$published_at = DateTime::createFromFormat('Y-m-d H:i:s', $article['published_at']);
$published_at_value = $published_at ? $published_at->format('Y-m-d\TH:i') : '';
$selected_category = set_value('category_id', $article['category_id']);
?>

<form method="post" action="<?= site_url('news/update/' . rawurlencode($article['id'])) ?>" enctype="multipart/form-data" class="card p-4">
    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="title">Headline</label>
            <input id="title" name="title" class="form-control" maxlength="300" value="<?= html_escape(set_value('title', $article['title'])) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="subtitle">Subtitle</label>
            <input id="subtitle" name="subtitle" class="form-control" maxlength="500" value="<?= html_escape(set_value('subtitle', $article['subtitle'])) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="category_id">Category</label>
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
        </div>
        <div class="col-12">
            <label class="form-label" for="content">Article content</label>
            <textarea id="content" name="content" class="form-control" rows="10" required><?= html_escape(
                set_value('content', implode("\n\n", $content_paragraphs))
            ) ?></textarea>
            <div class="form-text">Separate paragraphs with a blank line.</div>
        </div>
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
            <?php if (!empty($article['image_url'])): ?>
                <div class="mt-2 d-flex align-items-center gap-2 p-2 bg-light rounded border">
                    <img src="<?= html_escape($article['image_url']) ?>" alt="Current image" style="height: 44px; width: 66px; object-fit: cover; border-radius: 4px;" onerror="this.style.display='none';">
                    <div class="small text-truncate">
                        <span class="text-secondary fw-semibold">Current:</span>
                        <a href="<?= html_escape($article['image_url']) ?>" target="_blank"><?= html_escape(basename($article['image_url'])) ?></a>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-text text-muted">Upload a new image to replace current, or leave blank to keep.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="image_caption">Image caption</label>
            <input id="image_caption" name="image_caption" class="form-control" maxlength="500" value="<?= html_escape(set_value('image_caption', $article['image_caption'])) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="author_name">Author name</label>
            <input id="author_name" name="author_name" class="form-control" maxlength="160" value="<?= html_escape(set_value('author_name', $article['author_name'])) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="author_role">Author role</label>
            <input id="author_role" name="author_role" class="form-control" maxlength="160" value="<?= html_escape(set_value('author_role', $article['author_role'])) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="author_avatar">Author avatar URL</label>
            <input id="author_avatar" name="author_avatar" type="text" class="form-control" maxlength="500" value="<?= html_escape(set_value('author_avatar', $article['author_avatar'])) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="published_at">Publication date and time</label>
            <input id="published_at" name="published_at" type="datetime-local" class="form-control" value="<?= html_escape(set_value('published_at', $published_at_value)) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="read_time">Read time</label>
            <input id="read_time" name="read_time" class="form-control" maxlength="40" value="<?= html_escape(set_value('read_time', $article['read_time'])) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="tags">Tags</label>
            <input id="tags" name="tags" class="form-control" value="<?= html_escape(set_value('tags', implode(', ', $tags))) ?>" placeholder="Separate tags with commas">
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input id="is_breaking" name="is_breaking" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_breaking', '1', (bool) $article['is_breaking']) ?>>
                    <label class="form-check-label" for="is_breaking">Breaking news</label>
                </div>
                <div class="form-check">
                    <input id="is_featured" name="is_featured" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_featured', '1', (bool) $article['is_featured']) ?>>
                    <label class="form-check-label" for="is_featured">Featured story</label>
                </div>
                <div class="form-check">
                    <input id="is_trending" name="is_trending" type="checkbox" class="form-check-input" value="1" <?= set_checkbox('is_trending', '1', (bool) $article['is_trending']) ?>>
                    <label class="form-check-label" for="is_trending">Trending story</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons: Publish & Save Draft side-by-side -->
    <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
        <button type="submit" name="submit_action" value="publish" class="btn btn-danger fw-bold px-4 py-2 shadow-sm">
            🚀 Publish News
        </button>
        <button type="submit" name="submit_action" value="draft" class="btn btn-outline-dark fw-bold px-4 py-2">
            📝 Save as Draft
        </button>
        <a class="btn btn-outline-secondary px-4 py-2" href="<?= site_url('news') ?>">Cancel</a>
        <?php if (!empty($article['status'])): ?>
            <span class="ms-auto small text-muted">
                Current status: 
                <span class="badge <?= $article['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= html_escape(ucfirst($article['status'])) ?>
                </span>
            </span>
        <?php endif; ?>
    </div>
</form>
