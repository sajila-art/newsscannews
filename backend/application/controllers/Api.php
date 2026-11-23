<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Accept, Content-Type');

        if ($this->input->method(TRUE) === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    public function categories() {
        if (!$this->require_get()) {
            return;
        }

        if (!$this->db->table_exists('categories')) {
            $this->json_response(array());
            return;
        }

        $has_home_page = $this->db->field_exists('home_page', 'categories');
        $has_slug = $this->db->field_exists('slug', 'categories');

        $select = 'id, name_malayalam, name_english, description';
        if ($has_slug) {
            $select .= ', slug';
        }
        if ($has_home_page) {
            $select .= ', home_page';
        }

        $query = $this->db
            ->select($select)
            ->where('status', 'active');

        $home_filter = $this->input->get('home_page');
        if ($has_home_page && $home_filter !== NULL && $home_filter !== '') {
            $query->where('home_page', (int) $home_filter);
        }

        $home_not = $this->input->get('home_page_not');
        if ($has_home_page && $home_not !== NULL && $home_not !== '') {
            $query->where('home_page !=', (int) $home_not);
        }

        $categories = $query
            ->order_by('display_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get('categories')
            ->result();

        $data = array_map(function ($category) {
            $slug = !empty($category->slug) ? $category->slug : (string) $category->id;
            return array(
                'id' => (string) $category->id,
                'slug' => $slug,
                'nameMalayalam' => $category->name_malayalam,
                'nameEnglish' => $category->name_english,
                'description' => $category->description ? $category->description : '',
                'homePage' => isset($category->home_page) ? (int) $category->home_page : 1
            );
        }, $categories);

        $this->json_response($data);
    }

    public function home() {
        if (!$this->require_get()) {
            return;
        }

        $this->ensure_home_banner_table();

        $row = $this->db->where('status', 'active')->limit(1)->get('home_banner')->row();
        if (!$row) {
            $row = $this->db->limit(1)->get('home_banner')->row();
        }

        if (!$row) {
            $this->json_response(null);
            return;
        }

        $data = array(
            'id' => (int) $row->id,
            'title' => $row->title,
            'subtitle' => $row->subtitle,
            'description' => $row->description,
            'author' => array(
                'name' => $row->author,
                'role' => $row->author_role
            ),
            'bannerImage' => $row->banner_image,
            'badge' => $row->badge,
            'linkUrl' => $row->link_url,
            'status' => $row->status,
            'updatedAt' => $row->updated_at
        );

        $this->json_response($data);
    }

    public function todays_audio() {
        if (!$this->require_get()) {
            return;
        }

        try {
            $this->load->model('todays_audio_model');
            $row = $this->todays_audio_model->get_today();

            if (!$row) {
                $this->json_response(null);
                return;
            }

            $data = array(
                'id' => isset($row['id']) ? (int) $row['id'] : 1,
                'audioDate' => isset($row['audio_date']) ? $row['audio_date'] : date('Y-m-d'),
                'title' => isset($row['title']) ? $row['title'] : '',
                'scriptText' => isset($row['script_text']) ? $row['script_text'] : '',
                'audioUrl' => !empty($row['audio_url']) ? $row['audio_url'] : null,
                'articleCount' => isset($row['article_count']) ? (int) $row['article_count'] : 0,
                'durationLabel' => isset($row['duration_label']) ? $row['duration_label'] : '00:00',
                'status' => isset($row['status']) ? $row['status'] : 'active',
                'createdAt' => isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s')
            );

            $this->json_response($data);
        } catch (\Throwable $e) {
            $this->json_response(array('error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => basename($e->getFile())), 500);
        }
    }

    public function search() {
        if (!$this->require_get()) {
            return;
        }

        $q = trim((string) $this->input->get('q', TRUE));
        $matched_categories = array();

        if ($this->db->table_exists('categories')) {
            $has_slug = $this->db->field_exists('slug', 'categories');
            $has_home_page = $this->db->field_exists('home_page', 'categories');
            $select = 'id, name_malayalam, name_english, description';
            if ($has_slug) {
                $select .= ', slug';
            }
            if ($has_home_page) {
                $select .= ', home_page';
            }

            $cat_query = $this->db
                ->select($select)
                ->from('categories')
                ->where('status', 'active');

            if ($q !== '') {
                $cat_query->group_start()
                    ->like('name_malayalam', $q)
                    ->or_like('name_english', $q)
                    ->or_like('description', $q);
                if ($has_slug) {
                    $cat_query->or_like('slug', $q);
                }
                $cat_query->group_end();
            }

            $cats = $cat_query
                ->order_by('display_order', 'ASC')
                ->order_by('id', 'ASC')
                ->limit(12)
                ->get()
                ->result();

            $matched_categories = array_map(function ($category) {
                $slug = !empty($category->slug) ? $category->slug : (string) $category->id;
                return array(
                    'id' => (string) $category->id,
                    'slug' => $slug,
                    'nameMalayalam' => $category->name_malayalam,
                    'nameEnglish' => $category->name_english,
                    'description' => $category->description ? $category->description : '',
                    'homePage' => isset($category->home_page) ? (int) $category->home_page : 1
                );
            }, $cats);
        }

        $art_query = $this->published_articles();
        if ($q !== '') {
            $art_query->group_start()
                ->like('a.title', $q)
                ->or_like('a.subtitle', $q)
                ->or_like('a.excerpt', $q)
                ->or_like('a.tags_json', $q)
                ->or_like('c.name_malayalam', $q)
                ->or_like('c.name_english', $q)
                ->group_end();
        }

        $articles = $art_query
            ->order_by('a.published_at', 'DESC')
            ->limit(20)
            ->get()
            ->result();

        $this->json_response(array(
            'categories' => $matched_categories,
            'articles' => array_map(array($this, 'format_article'), $articles),
        ));
    }

    public function articles() {
        if (!$this->require_get()) {
            return;
        }

        $query = $this->published_articles();
        $category_id = $this->input->get('category', TRUE);
        if ($category_id !== NULL && $category_id !== '') {
            if (!ctype_digit((string) $category_id) || (int) $category_id < 1) {
                $this->json_response(array('error' => 'Category must be a positive numeric ID.'), 400);
                return;
            }
            $query->where('a.category_id', (int) $category_id);
        }

        $articles = $query
            ->order_by('a.published_at', 'DESC')
            ->get()
            ->result();

        $this->json_response(array_map(array($this, 'format_article'), $articles));
    }

    public function article($id) {
        if (!$this->require_get()) {
            return;
        }

        $article = $this->published_articles()
            ->where('a.id', $id)
            ->get()
            ->row();

        if (!$article) {
            $this->json_response(array('error' => 'Article not found.'), 404);
            return;
        }

        $this->json_response($this->format_article($article));
    }

    private function published_articles() {
        return $this->db
            ->select('a.id, a.title, a.subtitle, a.excerpt, a.content_json, a.category_id, a.author_name, a.author_role, a.author_avatar, a.published_at, a.read_time, a.image_url, a.image_caption, a.tags_json, a.is_breaking, a.is_featured, a.is_trending, a.scanned_page_ref_json, a.views, c.name_malayalam AS category_name')
            ->from('articles AS a')
            ->join('categories AS c', 'c.id = a.category_id', 'left')
            ->where('a.status', 'published');
    }

    private function format_article($article) {
        return array(
            'id' => (string) $article->id,
            'title' => $article->title,
            'subtitle' => $article->subtitle,
            'excerpt' => $article->excerpt,
            'content' => $this->decode_array($article->content_json),
            'category' => $article->category_name ? $article->category_name : '',
            'categorySlug' => (string) $article->category_id,
            'author' => array(
                'name' => $article->author_name,
                'role' => $article->author_role,
                'avatar' => $article->author_avatar
            ),
            'publishedAt' => $article->published_at,
            'readTime' => $article->read_time ? $article->read_time : '3 min read',
            'imageUrl' => $article->image_url ? $article->image_url : '',
            'imageCaption' => $article->image_caption,
            'tags' => $this->decode_array($article->tags_json),
            'isBreaking' => (bool) $article->is_breaking,
            'isFeatured' => (bool) $article->is_featured,
            'isTrending' => (bool) $article->is_trending,
            'scannedPageRef' => $this->decode_object($article->scanned_page_ref_json),
            'views' => (int) $article->views
        );
    }

    private function decode_array($json) {
        $decoded = json_decode((string) $json, TRUE);
        return is_array($decoded) ? array_values($decoded) : array();
    }

    private function decode_object($json) {
        $decoded = json_decode((string) $json, TRUE);
        return is_array($decoded) ? $decoded : NULL;
    }

    private function ensure_home_banner_table() {
        if (!$this->db->table_exists('home_banner')) {
            $this->load->dbforge();
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE),
                'banner_image' => array('type' => 'VARCHAR', 'constraint' => 1000, 'null' => TRUE),
                'title' => array('type' => 'VARCHAR', 'constraint' => 300),
                'subtitle' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
                'description' => array('type' => 'TEXT'),
                'author' => array('type' => 'VARCHAR', 'constraint' => 160, 'default' => 'Chief Editor'),
                'author_role' => array('type' => 'VARCHAR', 'constraint' => 160, 'default' => 'Special Correspondent'),
                'badge' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE, 'default' => 'പ്രധാന വാർത്ത'),
                'link_url' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
                'status' => array('type' => 'ENUM("active","inactive")', 'default' => 'active'),
                'created_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'),
                'updated_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP')
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('home_banner', TRUE);

            // Seed default lead banner
            $this->db->insert('home_banner', array(
                'id' => 1,
                'banner_image' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1600&q=80',
                'title' => 'കേരളത്തിൽ ഡിജിറ്റൽ വിപ്ലവം: മുഴുവൻ തദ്ദേശ സ്ഥാപനങ്ങളും ഓൺലൈൻ സേവനത്തിലേക്ക്',
                'subtitle' => 'പൊതുജനങ്ങൾക്ക് സുതാര്യമായ സേവനം ഉറപ്പാക്കുന്ന സമഗ്ര ഭരണ പരിഷ്കാരം',
                'description' => 'സംസ്ഥാനത്തെ മുഴുവൻ തദ്ദേശ സ്വയംഭരണ സ്ഥാപനങ്ങളിലെയും സേവനങ്ങൾ സമ്പൂർണ്ണമായി ഡിജിറ്റൈസ് ചെയ്തു. ജനങ്ങൾക്ക് സർക്കാർ ഓഫീസുകൾ കയറിയിറങ്ങാതെ സർട്ടിഫിക്കറ്റുകളും അനുമതികളും വീട്ടിലിരുന്ന് ലഭ്യമാകും.',
                'author' => 'കെ. ആർ. നാരായണൻ',
                'author_role' => 'ചീഫ് റിപ്പോർട്ടർ (Chief Reporter)',
                'badge' => 'പ്രധാന വാർത്ത (Lead Story)',
                'link_url' => '#',
                'status' => 'active'
            ));
        }
    }

    public function subscribe() {
        if ($this->input->method(TRUE) !== 'POST') {
            header('Allow: POST, OPTIONS');
            $this->json_error('Method not allowed.', 405);
            return;
        }

        $this->ensure_newsletter_subscriptions_table();

        $rawInput = file_get_contents('php://input');
        $jsonBody = json_decode((string) $rawInput, TRUE);
        $rawEmail = is_array($jsonBody) && isset($jsonBody['email'])
            ? $jsonBody['email']
            : $this->input->post('email', TRUE);

        $email = strtolower(trim((string) $rawEmail));
        if ($email === '' || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json_error('Please enter a valid email address.', 400);
            return;
        }

        $forwardedFor = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? (string) $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        $ipAddress = '';
        if ($forwardedFor !== '') {
            $parts = explode(',', $forwardedFor);
            $ipAddress = trim((string) $parts[0]);
        }
        if ($ipAddress === '') {
            $ipAddress = (string) $this->input->ip_address();
        }
        $ipAddress = substr($ipAddress, 0, 45);
        if ($ipAddress === '') {
            $ipAddress = NULL;
        }

        $existing = $this->db
            ->where('email', $email)
            ->limit(1)
            ->get('newsletter_subscriptions')
            ->row_array();

        if ($existing) {
            if (isset($existing['status']) && $existing['status'] === 'subscribed') {
                $this->json_response(array(
                    'id' => (int) $existing['id'],
                    'email' => $existing['email'],
                    'status' => 'subscribed',
                    'alreadySubscribed' => TRUE,
                ), 200);
                return;
            }

            $this->db->where('id', (int) $existing['id'])->update('newsletter_subscriptions', array(
                'status' => 'subscribed',
                'subscribed_at' => date('Y-m-d H:i:s'),
                'unsubscribed_at' => NULL,
                'ip_address' => $ipAddress,
            ));

            $this->json_response(array(
                'id' => (int) $existing['id'],
                'email' => $email,
                'status' => 'subscribed',
                'resubscribed' => TRUE,
            ), 200);
            return;
        }

        $inserted = $this->db->insert('newsletter_subscriptions', array(
            'email' => $email,
            'status' => 'subscribed',
            'ip_address' => $ipAddress,
        ));

        if (!$inserted) {
            $this->json_error('Unable to save subscription right now.', 500);
            return;
        }

        $this->json_response(array(
            'id' => (int) $this->db->insert_id(),
            'email' => $email,
            'status' => 'subscribed',
        ), 201);
    }

    private function ensure_newsletter_subscriptions_table() {
        if ($this->db->table_exists('newsletter_subscriptions')) {
            return;
        }
        if ($this->db->dbdriver === 'postgre') {
            $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
                id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                status VARCHAR(20) DEFAULT 'subscribed' CHECK (status IN ('subscribed', 'unsubscribed')),
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                unsubscribed_at TIMESTAMP NULL,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->db->query($sql);
            return;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `status` ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed',
            `subscribed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `unsubscribed_at` DATETIME NULL,
            `ip_address` VARCHAR(45) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->db->query($sql);
    }

    private function json_error($message, $status = 400) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(
                array('error' => $message),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ));
    }

    private function require_get() {
        if ($this->input->method(TRUE) === 'GET') {
            return TRUE;
        }

        header('Allow: GET, OPTIONS');
        $this->json_response(array('error' => 'Method not allowed.'), 405);
        return FALSE;
    }

    private function json_response($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(
                array('data' => $data),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ));
    }
}
