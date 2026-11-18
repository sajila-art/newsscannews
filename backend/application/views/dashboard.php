<div class="news-dashboard">
    <section class="news-dashboard-hero">
        <div>
            <p class="news-dashboard-kicker">Newsroom overview</p>
            <h1>Welcome<?= !empty($current_user['name']) ? ', ' . html_escape($current_user['name']) : '' ?></h1>
            <p class="news-dashboard-intro">Manage the latest stories and organize your publication.</p>
        </div>
    </section>

    <section class="news-dashboard-stats" aria-label="News statistics">
        <article class="news-dashboard-stat">
            <span class="news-dashboard-stat-mark">N</span>
            <div>
                <p>Total stories</p>
                <strong><?= number_format($stats['articles']) ?></strong>
            </div>
        </article>
        <article class="news-dashboard-stat">
            <span class="news-dashboard-stat-mark is-published">P</span>
            <div>
                <p>Published stories</p>
                <strong><?= number_format($stats['published']) ?></strong>
            </div>
        </article>
        <article class="news-dashboard-stat">
            <span class="news-dashboard-stat-mark is-category">C</span>
            <div>
                <p>Categories</p>
                <strong><?= number_format($stats['categories']) ?></strong>
            </div>
        </article>
        <article class="news-dashboard-stat">
            <span class="news-dashboard-stat-mark is-active">A</span>
            <div>
                <p>Active categories</p>
                <strong><?= number_format($stats['active_categories']) ?></strong>
            </div>
        </article>
    </section>

    <div class="news-dashboard-columns">
        <section class="news-dashboard-panel" aria-labelledby="latest-news-heading">
            <div class="news-dashboard-panel-heading">
                <div>
                    <h2 id="latest-news-heading">Latest news</h2>
                    <p>Recently published stories</p>
                </div>
                <?php if (isset($current_user) && $current_user['role'] === 'admin'): ?>
                    <a class="news-dashboard-text-link" href="<?= site_url('news') ?>">View all</a>
                <?php endif; ?>
            </div>

            <?php if (!$articles_available): ?>
                <div class="news-dashboard-empty">
                    <strong>News articles are not set up yet.</strong>
                    <p>Create the <code>articles</code> table to show the latest stories here.</p>
                </div>
            <?php elseif (!$latest_news): ?>
                <div class="news-dashboard-empty">
                    <strong>No stories found.</strong>
                    <p>New articles will appear here after they are added.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table news-dashboard-table">
                        <thead>
                            <tr>
                                <th>Headline</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latest_news as $article): ?>
                                <tr>
                                    <td class="news-dashboard-headline"><?= html_escape($article['title']) ?></td>
                                    <td><?= html_escape(isset($article['category_name']) ? $article['category_name'] : '—') ?></td>
                                    <td>
                                        <?php if (!empty($article['status'])): ?>
                                            <span class="news-dashboard-status"><?= html_escape($article['status']) ?></span>
                                        <?php else: ?>
                                            <span class="text-secondary">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="news-dashboard-date">
                                        <?= !empty($article['published_at']) ? html_escape($article['published_at']) : '—' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <aside class="news-dashboard-panel" aria-labelledby="category-overview-heading">
            <div class="news-dashboard-panel-heading">
                <div>
                    <h2 id="category-overview-heading">Categories</h2>
                    <p>Your publication sections</p>
                </div>
                <?php if (isset($current_user) && $current_user['role'] === 'admin'): ?>
                    <a class="news-dashboard-text-link" href="<?= site_url('category') ?>">Manage</a>
                <?php endif; ?>
            </div>
            <?php if (!$categories): ?>
                <div class="news-dashboard-empty">
                    <strong>No categories yet.</strong>
                    <p>Add categories to organize news stories.</p>
                </div>
            <?php else: ?>
                <ul class="news-dashboard-category-list">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <span class="news-dashboard-category-name">
                                <strong>
                                    <?= html_escape(
                                        !empty($category['name_malayalam'])
                                            ? $category['name_malayalam']
                                            : (isset($category['name_english']) ? $category['name_english'] : $category['slug'])
                                    ) ?>
                                </strong>
                                <?php if (!empty($category['name_english']) && !empty($category['name_malayalam'])): ?>
                                    <small><?= html_escape($category['name_english']) ?></small>
                                <?php endif; ?>
                            </span>
                            <?php if (!empty($category['status'])): ?>
                                <span class="news-dashboard-category-status <?= $category['status'] === 'active' ? 'is-active' : '' ?>">
                                    <?= html_escape($category['status']) ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </aside>
    </div>
</div>
