<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="mb-1 fw-bold">Today's Audio News</h2>
        <p class="text-secondary mb-0">
            Compile all today's (<code><?= html_escape($today_date) ?></code>) published <strong>Breaking News</strong> &amp; <strong>live-news-broadcast</strong> stories into one audio bulletin in <code>todays_audio</code>.
        </p>
    </div>
    <div>
        <form method="post" action="<?= site_url('audio/generate') ?>" class="d-inline">
            <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
            <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                🔊 Generate & Save Today's Audio
            </button>
        </form>
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

<div class="row g-4">
    <!-- Left Column: Source Articles for Today -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Today's Live News Broadcast</h5>
                <span class="badge bg-danger rounded-pill"><?= count($broadcast_articles) ?> stories</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($broadcast_articles)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($broadcast_articles as $art): ?>
                            <li class="list-group-item py-3">
                                <div class="fw-bold text-dark small mb-1"><?= html_escape($art['title']) ?></div>
                                <div class="text-muted" style="font-size: 12px;">
                                    Published: <?= html_escape($art['published_at']) ?>
                                    <?php if (!empty($art['author_name'])): ?>
                                        • <?= html_escape($art['author_name']) ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <p class="mb-2">No published articles found in <code>live-news-broadcast</code> for <strong><?= html_escape($today_date) ?></strong>.</p>
                        <a href="<?= site_url('news/add') ?>" class="btn btn-sm btn-outline-danger">Add Live Broadcast Story</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit & Preview Today's Audio -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Audio Bulletin Record (<code>todays_audio</code>)</h5>
                <?php if (!empty($todays_audio['duration_label'])): ?>
                    <span class="badge bg-dark"><?= html_escape($todays_audio['duration_label']) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <form method="post" action="<?= site_url('audio/save') ?>" enctype="multipart/form-data">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="audio_title">Bulletin Title</label>
                        <input
                            id="audio_title"
                            name="title"
                            type="text"
                            class="form-control"
                            maxlength="300"
                            value="<?= html_escape(!empty($todays_audio['title']) ? $todays_audio['title'] : 'തത്സമയ വാർത്താ ബുള്ളറ്റിൻ (' . date('d M Y') . ')') ?>"
                            required
                        >
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" for="audio_file">Upload Audio File (Optional MP3/WAV)</label>
                            <input
                                id="audio_file"
                                name="audio_file"
                                type="file"
                                class="form-control"
                                accept="audio/*"
                            >
                            <div class="form-text">Optional: upload a recorded MP3/WAV, or let the portal synthesize speech from the script below.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="duration_label">Duration</label>
                            <input
                                id="duration_label"
                                name="duration_label"
                                type="text"
                                class="form-control"
                                value="<?= html_escape(!empty($todays_audio['duration_label']) ? $todays_audio['duration_label'] : '02:00') ?>"
                                placeholder="02:00"
                            >
                        </div>
                    </div>

                    <?php if (!empty($todays_audio['audio_url'])): ?>
                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="small fw-semibold text-secondary mb-2">Current Audio Playback:</div>
                            <audio controls class="w-100" src="<?= html_escape($todays_audio['audio_url']) ?>"></audio>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="script_text">Broadcast Script</label>
                        <textarea
                            id="script_text"
                            name="script_text"
                            class="form-control"
                            rows="8"
                            required
                            placeholder="Broadcast script generated from today's live-news-broadcast articles..."
                        ><?= html_escape(!empty($todays_audio['script_text']) ? $todays_audio['script_text'] : '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            Save Audio Bulletin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
