<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Todays_audio_model extends CI_Model {

    private $table = 'todays_audio';

    public function __construct() {
        parent::__construct();
        $this->ensure_table();
    }

    /**
     * Auto-create or migrate todays_audio table if it does not exist or is missing columns.
     */
    public function ensure_table() {
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `todays_audio` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `audio_date` DATE NOT NULL UNIQUE,
                `title` VARCHAR(300) NOT NULL,
                `script_text` LONGTEXT NOT NULL,
                `audio_url` VARCHAR(1000) NULL,
                `article_ids_json` TEXT NOT NULL,
                `article_count` INT NOT NULL DEFAULT 0,
                `duration_label` VARCHAR(40) NOT NULL DEFAULT '00:00',
                `status` ENUM('active', 'inactive') DEFAULT 'active',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $this->db->query($sql);
            return;
        }

        // Migrate missing columns if table already existed with an older schema
        $columns = array(
            'audio_date' => 'DATE NULL',
            'title' => "VARCHAR(300) NOT NULL DEFAULT ''",
            'script_text' => 'LONGTEXT NULL',
            'audio_url' => 'VARCHAR(1000) NULL',
            'article_ids_json' => 'TEXT NULL',
            'article_count' => 'INT NOT NULL DEFAULT 0',
            'duration_label' => "VARCHAR(40) NOT NULL DEFAULT '00:00'",
            'status' => "ENUM('active', 'inactive') NOT NULL DEFAULT 'active'",
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );

        foreach ($columns as $col => $def) {
            if (!$this->db->field_exists($col, $this->table)) {
                $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `{$col}` {$def}");
            }
        }
    }

    /**
     * Select published articles from live-news-broadcast where DATE(published_at) == current date.
     */
    public function get_todays_broadcast_articles($date = NULL) {
        if (!$this->db->table_exists('articles') || !$this->db->table_exists('categories')) {
            return array();
        }

        $target_date = $date ? $date : date('Y-m-d');
        $has_slug = $this->db->field_exists('slug', 'categories');

        $select = 'a.id, a.title, a.subtitle, a.excerpt, a.content_json, a.published_at, a.author_name, c.name_malayalam, c.name_english';
        if ($has_slug) {
            $select .= ', c.slug AS category_slug';
        }

        $this->db
            ->select($select)
            ->from('articles AS a')
            ->join('categories AS c', 'c.id = a.category_id', 'left')
            ->where('DATE(a.published_at) = ' . $this->db->escape($target_date), NULL, FALSE)
            ->group_start()
                ->where('a.is_breaking', 1);

        if ($has_slug) {
            $this->db->or_where('c.slug', 'breaking-news')
                     ->or_where('c.slug', 'live-news-broadcast');
        }

        $this->db->or_where('a.category_id', 17)
                 ->or_where('a.category_id', 18)
                 ->or_where('LOWER(c.name_english)', 'breaking news')
                 ->or_where('LOWER(c.name_english)', 'live news broadcast');

        $this->db->group_end();

        if ($this->db->field_exists('status', 'articles')) {
            $this->db->where('a.status', 'published');
        }

        return $this->db
            ->order_by('a.published_at', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Build Malayalam/English audio news script and estimated duration from articles.
     */
    public function build_script($articles, $date) {
        if (empty($articles)) {
            return array(
                'title' => 'തത്സമയ വാർത്താ ബുള്ളറ്റിൻ (' . date('d M Y', strtotime($date)) . ')',
                'script_text' => '',
                'duration_label' => '00:00'
            );
        }

        $lines = array();
        $lines[] = 'എൻ-ന്യൂസ് തത്സമയ വാർത്താ ബുള്ളറ്റിനിലേക്ക് സ്വാഗതം. ഇന്നത്തെ പ്രധാന വാർത്തകൾ.';

        $index = 1;
        foreach ($articles as $art) {
            $segment = $index . '. ' . trim($art['title']) . '.';
            $sub = !empty($art['subtitle']) ? trim($art['subtitle']) : '';
            $exc = !empty($art['excerpt']) ? trim(strip_tags($art['excerpt'])) : '';
            if ($sub !== '') {
                $segment .= ' ' . $sub . '.';
            } elseif ($exc !== '') {
                $segment .= ' ' . $exc . '.';
            }
            $lines[] = $segment;
            $index++;
        }

        $lines[] = 'ഇന്നത്തെ എൻ-ന്യൂസ് തത്സമയ വാർത്താ സംപ്രേഷണം ഇവിടെ പൂർണ്ണമാകുന്നു. നന്ദി.';

        $script_text = implode("\n\n", $lines);

        // Estimate speech duration (~12 chars per second)
        $char_len = mb_strlen($script_text, 'UTF-8');
        $seconds = max(15, (int) ceil($char_len / 12));
        $mins = (int) floor($seconds / 60);
        $secs = $seconds % 60;
        $duration_label = sprintf('%02d:%02d', $mins, $secs);

        return array(
            'title' => 'തത്സമയ വാർത്താ ബുള്ളറ്റിൻ (' . count($articles) . ' വാർത്തകൾ) — ' . date('d M Y', strtotime($date)),
            'script_text' => $script_text,
            'duration_label' => $duration_label
        );
    }

    /**
     * Generate or update today's audio news from live-news-broadcast articles and save into todays_audio table.
     */
    public function generate_for_today($try_tts = FALSE, $custom_audio_url = NULL) {
        $today = date('Y-m-d');
        $articles = $this->get_todays_broadcast_articles($today);
        $compiled = $this->build_script($articles, $today);
        $article_ids = array_map(function ($a) { return (string) $a['id']; }, $articles);

        $existing = $this->db->get_where($this->table, array('audio_date' => $today))->row_array();

        $audio_url = $existing ? $existing['audio_url'] : NULL;
        if ($custom_audio_url !== NULL && $custom_audio_url !== '') {
            $audio_url = $custom_audio_url;
        } elseif ($try_tts && !empty($compiled['script_text'])) {
            $generated_url = $this->synthesize_mp3($compiled['script_text'], $today);
            if ($generated_url) {
                $audio_url = $generated_url;
            }
        }

        $record = array(
            'audio_date' => $today,
            'title' => $compiled['title'],
            'script_text' => $compiled['script_text'],
            'audio_url' => $audio_url,
            'article_ids_json' => json_encode($article_ids, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'article_count' => count($articles),
            'duration_label' => $compiled['duration_label'],
            'status' => 'active'
        );
        if ($this->db->field_exists('audio', $this->table)) {
            $record['audio'] = '';
        }

        if ($existing) {
            if (isset($existing['id'])) {
                $this->db->where('id', (int) $existing['id'])->update($this->table, $record);
                $record['id'] = (int) $existing['id'];
            } else {
                $this->db->where('audio_date', $today)->update($this->table, $record);
                $record['id'] = isset($existing['audio_id']) ? (int) $existing['audio_id'] : 1;
            }
        } else {
            $this->db->insert($this->table, $record);
            $record['id'] = (int) $this->db->insert_id();
        }

        return $record;
    }

    /**
     * Get today's audio row where DATE(created_at) == current date, auto-syncing if today's live-news-broadcast articles exist.
     */
    public function get_today() {
        $today = date('Y-m-d');
        $articles = $this->get_todays_broadcast_articles($today);
        $current_ids = array_map(function ($a) { return (string) $a['id']; }, $articles);

        $order_col = $this->db->field_exists('id', $this->table) ? 'id' : 'created_at';
        $query = $this->db
            ->where('DATE(created_at) = ' . $this->db->escape($today), NULL, FALSE)
            ->where('status', 'active')
            ->order_by($order_col, 'DESC')
            ->limit(1)
            ->get($this->table);

        if (!$query) {
            $err = $this->db->error();
            throw new \RuntimeException('DB error in get_today: ' . ($err['message'] ?? 'unknown'));
        }

        $existing = $query->row_array();

        $needs_sync = !$existing;
        if ($existing) {
            $saved_ids = json_decode((string) $existing['article_ids_json'], TRUE);
            if (!is_array($saved_ids)) {
                $saved_ids = array();
            }
            if ($saved_ids !== $current_ids) {
                $needs_sync = TRUE;
            }
        }

        if ($needs_sync && !empty($articles)) {
            $existing = $this->generate_for_today(TRUE);
        }

        return $existing;
    }

    /**
     * Get all saved todays_audio records (latest first).
     */
    public function get_history($limit = 15) {
        return $this->db
            ->order_by('audio_date', 'DESC')
            ->limit((int) $limit)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Best-effort MP3 TTS generation saved to backend/uploads/audio/.
     */
    private function synthesize_mp3($script_text, $date) {
        if (!function_exists('curl_init')) {
            return NULL;
        }

        $upload_dir = FCPATH . 'uploads/audio/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, TRUE);
        }

        // Split script into <=180 char chunks for TTS
        $sentences = preg_split('/(?<=[\.\!\?\n])\s+/u', trim($script_text));
        $chunks = array();
        $current = '';
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if ($sentence === '') {
                continue;
            }
            if (mb_strlen($current . ' ' . $sentence, 'UTF-8') <= 180) {
                $current = trim($current . ' ' . $sentence);
            } else {
                if ($current !== '') {
                    $chunks[] = $current;
                }
                if (mb_strlen($sentence, 'UTF-8') > 180) {
                    $chunks[] = mb_substr($sentence, 0, 180, 'UTF-8');
                    $current = '';
                } else {
                    $current = $sentence;
                }
            }
        }
        if ($current !== '') {
            $chunks[] = $current;
        }

        if (empty($chunks)) {
            return NULL;
        }

        $audio_binary = '';
        foreach (array_slice($chunks, 0, 15) as $chunk) {
            $url = 'https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl=ml&q=' . rawurlencode($chunk);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && !empty($data)) {
                $audio_binary .= $data;
            } else {
                return NULL;
            }
        }

        if ($audio_binary === '') {
            return NULL;
        }

        $file_name = 'todays_audio_' . $date . '_' . time() . '.mp3';
        $full_path = $upload_dir . $file_name;
        if (@file_put_contents($full_path, $audio_binary) !== FALSE) {
            return base_url('uploads/audio/' . $file_name);
        }

        return NULL;
    }
}
