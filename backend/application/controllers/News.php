<?php defined('BASEPATH') OR exit('No direct script access allowed');

class News extends MY_Controller {

    private $table = 'articles';

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('pagination');
        $this->load->model('common_model');
        $this->ensure_article_status_column();
    }

    private function ensure_article_status_column() {
        if ($this->db->table_exists($this->table)) {
            if (!$this->db->field_exists('status', $this->table)) {
                $this->load->dbforge();
                $this->dbforge->add_column($this->table, array(
                    'status' => array(
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'default' => 'published',
                        'after' => 'tags_json'
                    )
                ));
            }
        }
    }

    public function index($offset = 0) {
        $this->require_roles(array('admin'));

        $per_page = 10;
        $offset = ctype_digit((string) $offset) ? (int) $offset : 0;
        $total_rows = $this->article_count();

        $data['title'] = 'News Articles';
        $data['rows'] = $this->article_rows($per_page, $offset);
        $data['total_rows'] = $total_rows;
        $data['per_page'] = $per_page;
        $data['offset'] = $offset;
        $data['pagination'] = get_pagination(site_url('news/index'), $total_rows, $per_page, 3);

        $this->render('news/list', $data);
    }

    public function add() {
        $this->require_roles(array('admin'));

        $data['title'] = 'Create News Article';
        $data = $this->news_form_data(array());

        $this->render('news/add', $data);
    }

    public function create() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            redirect('news/add');
            return;
        }

        $this->set_article_rules();
        if (!$this->form_validation->run()) {
            $data['title'] = 'Create News Article';
            $data = $this->news_form_data(array());

            $this->render('news/add', $data);
            return;
        }

        $upload_result = $this->upload_image('image_file');
        if (!empty($upload_result['error'])) {
            $this->session->set_flashdata('error', $upload_result['error']);
            $data = $this->news_form_data(array());
            $data['title'] = 'Create News Article';
            $this->render('news/add', $data);
            return;
        }

        $article = $this->article_input();
        if (!empty($upload_result['url'])) {
            $article['image_url'] = $upload_result['url'];
        }

        $this->common_model->insert_data($this->table, $article);
        $this->audit('create', 'article', $article['id'], 'title=' . $article['title']);
        if ($article['status'] === 'published') {
            $this->sync_todays_audio_if_broadcast($article['category_id'], $article['is_breaking']);
        }
        $status_msg = ($article['status'] === 'published')
            ? 'News article published successfully!'
            : 'News article added as draft. You can publish it anytime from the list or edit page.';
        $this->session->set_flashdata('success', $status_msg);
        redirect('news');
    }

    public function edit($id) {
        $this->require_roles(array('admin'));

        $data['article'] = $this->find_article($id);
        if (!$data['article']) {
            show_404();
        }

        $this->render('news/edit', $this->news_form_data($data));
    }

    public function update($id) {
        $this->require_roles(array('admin'));

        $article = $this->find_article($id);
        if (!$article) {
            show_404();
        }

        $this->set_article_rules();
        if (!$this->form_validation->run()) {
            $data['article'] = $article;
            $this->render('news/edit', $this->news_form_data($data));
            return;
        }

        $upload_result = $this->upload_image('image_file');
        if (!empty($upload_result['error'])) {
            $this->session->set_flashdata('error', $upload_result['error']);
            $data['article'] = $article;
            $this->render('news/edit', $this->news_form_data($data));
            return;
        }

        $update = $this->article_input(FALSE);
        if (!empty($upload_result['url'])) {
            $update['image_url'] = $upload_result['url'];
        } else {
            $update['image_url'] = $article['image_url'];
        }

        $this->common_model->alter($this->table, array('id' => $id), $update);
        $this->audit('update', 'article', $id, 'title=' . $update['title']);
        if ($update['status'] === 'published') {
            $this->sync_todays_audio_if_broadcast($update['category_id'], $update['is_breaking']);
        }
        $status_msg = ($update['status'] === 'published')
            ? 'News article updated and published successfully!'
            : 'News article updated and saved as draft.';
        $this->session->set_flashdata('success', $status_msg);
        redirect('news');
    }

    public function publish($id) {
        $this->require_roles(array('admin'));

        $article = $this->find_article($id);
        if ($article) {
            $update = array('status' => 'published');
            if (empty($article['published_at']) || $article['published_at'] === '0000-00-00 00:00:00') {
                $update['published_at'] = date('Y-m-d H:i:s');
            }
            $this->common_model->alter($this->table, array('id' => $id), $update);
            $this->audit('publish', 'article', $id, 'title=' . $article['title']);
            $this->sync_todays_audio_if_broadcast($article['category_id'], isset($article['is_breaking']) ? $article['is_breaking'] : 0);
            $this->session->set_flashdata('success', 'News article "' . $article['title'] . '" is now published!');
        } else {
            $this->session->set_flashdata('error', 'Article not found.');
        }

        redirect('news');
    }

    public function unpublish($id) {
        $this->require_roles(array('admin'));

        $article = $this->find_article($id);
        if ($article) {
            $this->common_model->alter($this->table, array('id' => $id), array('status' => 'draft'));
            $this->audit('unpublish', 'article', $id, 'title=' . $article['title']);
            $this->session->set_flashdata('success', 'News article "' . $article['title'] . '" moved to draft (unpublished).');
        } else {
            $this->session->set_flashdata('error', 'Article not found.');
        }

        redirect('news');
    }

    public function delete($id) {
        $this->require_roles(array('admin'));

        $article = $this->find_article($id);
        if ($article) {
            $this->common_model->delete($this->table, array('id' => $id));
            $this->audit('delete', 'article', $id, 'title=' . $article['title']);
            $this->session->set_flashdata('success', 'News article deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Article not found.');
        }

        redirect('news');
    }

    /**
     * Handle standalone/AJAX image upload for news articles.
     */
    public function upload() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Method not allowed.')));
            return;
        }

        $result = $this->upload_image('image_file');
        if (!empty($result['error'])) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => $result['error'])));
            return;
        }

        if (empty($result['url'])) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Please select an image file to upload.')));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => TRUE,
                'url' => $result['url'],
                'file_name' => $result['file_name']
            )));
    }

    /**
     * Internal helper to upload featured image into backend/uploads/news/.
     */
    private function upload_image($field = 'image_file') {
        if (empty($_FILES[$field]['name'])) {
            return array('url' => '', 'error' => '');
        }

        $upload_dir = FCPATH . 'uploads/news/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, TRUE);
        }

        $config = array(
            'upload_path'   => $upload_dir,
            'allowed_types' => 'jpg|jpeg|png|webp|gif',
            'max_size'      => 5120, // 5MB
            'encrypt_name'  => TRUE
        );

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field)) {
            return array(
                'url'   => '',
                'error' => strip_tags($this->upload->display_errors())
            );
        }

        $upload_data = $this->upload->data();
        $url = base_url('uploads/news/' . $upload_data['file_name']);

        return array(
            'url'       => $url,
            'file_name' => $upload_data['file_name'],
            'error'     => ''
        );
    }

    public function category_available($category_id) {
        $available = $this->common_model->check_exists('categories', array('id' => (int) $category_id));
        if (!$available) {
            $this->form_validation->set_message('category_available', 'Select a valid news category.');
            return FALSE;
        }

        return TRUE;
    }

    public function valid_published_at($published_at) {
        $date = DateTime::createFromFormat('!Y-m-d\TH:i', $published_at);
        if (!$date || $date->format('Y-m-d\TH:i') !== $published_at) {
            $this->form_validation->set_message(
                'valid_published_at',
                'Enter a valid publication date and time.'
            );
            return FALSE;
        }

        return TRUE;
    }

    public function valid_content($content) {
        if (trim($content) === '') {
            $this->form_validation->set_message('valid_content', 'Article content is required.');
            return FALSE;
        }

        return TRUE;
    }

    private function article_count() {
        if (!$this->db->table_exists($this->table)) {
            return 0;
        }

        return (int) $this->db->count_all($this->table);
    }

    private function article_rows($limit = 10, $offset = 0) {
        if (!$this->db->table_exists($this->table)) {
            return array();
        }

        $has_status = $this->db->field_exists('status', $this->table);
        $status_col = $has_status ? 'a.status' : "'published' AS status";

        return $this->db
            ->select('a.id, a.title, a.subtitle, a.category_id, a.author_name, a.published_at, a.image_url, ' . $status_col . ', a.is_breaking, a.is_featured, a.is_trending, c.name_malayalam, c.name_english')
            ->from('articles AS a')
            ->join('categories AS c', 'c.id = a.category_id', 'left')
            ->order_by('a.published_at', 'DESC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->result_array();
    }

    private function set_article_rules() {
        $this->form_validation->set_rules('title', 'Headline', 'trim|required|max_length[300]');
        $this->form_validation->set_rules('subtitle', 'Subtitle', 'trim|max_length[500]');
        $this->form_validation->set_rules('content', 'Article content', 'required|callback_valid_content');
        $this->form_validation->set_rules(
            'category_id',
            'Category',
            'required|integer|callback_category_available'
        );
        $this->form_validation->set_rules('author_name', 'Author name', 'trim|required|max_length[160]');
        $this->form_validation->set_rules('author_role', 'Author role', 'trim|required|max_length[160]');
        $this->form_validation->set_rules('author_avatar', 'Author avatar URL', 'trim|max_length[500]');
        $this->form_validation->set_rules(
            'published_at',
            'Publication date',
            'required|callback_valid_published_at'
        );
        $this->form_validation->set_rules('read_time', 'Read time', 'trim|required|max_length[40]');
        $this->form_validation->set_rules('image_url', 'Image URL', 'trim|max_length[1000]');
        $this->form_validation->set_rules('image_caption', 'Image caption', 'trim|max_length[500]');
        $this->form_validation->set_rules('tags', 'Tags', 'trim');
    }

    private function article_input($include_id = TRUE) {
        $content = trim((string) $this->input->post('content', FALSE));
        $paragraphs = preg_split('/\R(?:[ \t]*\R)+/u', $content);
        $paragraphs = array_values(array_filter($paragraphs, 'strlen'));

        $tag_input = trim((string) $this->input->post('tags', TRUE));
        $tags = preg_split('/[,\\r\\n]+/u', $tag_input);
        $tags = array_values(array_unique(array_filter(array_map('trim', $tags), 'strlen')));

        // Auto-generate excerpt from first paragraph if omitted
        $excerpt = trim((string) $this->input->post('excerpt', TRUE));
        if ($excerpt === '') {
            $first_para = !empty($paragraphs[0]) ? strip_tags($paragraphs[0]) : '';
            $excerpt = mb_substr($first_para, 0, 250, 'UTF-8');
            if ($excerpt === '') {
                $excerpt = (string) $this->input->post('title', TRUE);
            }
        }

        // Determine status from button clicked: "publish" or "draft"
        $submit_action = $this->input->post('submit_action', TRUE);
        $status = ($submit_action === 'draft') ? 'draft' : 'published';

        $article = array(
            'title' => $this->input->post('title', TRUE),
            'subtitle' => $this->optional_post('subtitle'),
            'excerpt' => $excerpt,
            'content_json' => $this->encode_json($paragraphs, 'article content'),
            'category_id' => (int) $this->input->post('category_id'),
            'author_name' => $this->input->post('author_name', TRUE),
            'author_role' => $this->input->post('author_role', TRUE),
            'author_avatar' => $this->optional_post('author_avatar'),
            'published_at' => str_replace('T', ' ', $this->input->post('published_at', TRUE)) . ':00',
            'read_time' => $this->input->post('read_time', TRUE),
            'image_url' => (string) $this->input->post('image_url', TRUE),
            'image_caption' => $this->optional_post('image_caption'),
            'tags_json' => $this->encode_json($tags, 'article tags'),
            'status' => $status,
            'is_breaking' => $this->input->post('is_breaking') ? 1 : 0,
            'is_featured' => $this->input->post('is_featured') ? 1 : 0,
            'is_trending' => $this->input->post('is_trending') ? 1 : 0
        );

        if ($include_id) {
            $article['id'] = bin2hex(random_bytes(16));
        }

        return $article;
    }

    private function news_form_data($data) {
        if ($this->db->table_exists('categories')) {
            $data['categories'] = $this->db
                ->select('id, name_malayalam, name_english, status')
                ->from('categories')
                ->order_by('display_order', 'ASC')
                ->order_by('name_english', 'ASC')
                ->get()
                ->result_array();
        } else {
            $data['categories'] = array();
        }

        return $data;
    }

    private function find_article($id) {
        return $this->db
            ->get_where($this->table, array('id' => (string) $id))
            ->row_array();
    }

    private function optional_post($field) {
        $value = $this->input->post($field, TRUE);
        return $value === '' ? null : $value;
    }

    private function encode_json($value, $label) {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === FALSE) {
            show_error('Unable to save ' . $label . '. Check the text and try again.', 500);
        }

        return $json;
    }

    /**
     * Automatically refresh todays_audio table when breaking news or live-news-broadcast is published today.
     */
    private function sync_todays_audio_if_broadcast($category_id, $is_breaking = 0) {
        $is_target = ((int) $is_breaking === 1) || in_array((int) $category_id, array(17, 18), TRUE);
        if (!$is_target && $this->db->field_exists('slug', 'categories')) {
            $cat = $this->db->select('slug, name_english')->get_where('categories', array('id' => (int) $category_id))->row_array();
            if ($cat && (in_array($cat['slug'], array('breaking-news', 'live-news-broadcast'), TRUE) || in_array(strtolower((string) $cat['name_english']), array('breaking news', 'live news broadcast'), TRUE))) {
                $is_target = TRUE;
            }
        }
        if ($is_target) {
            $this->load->model('todays_audio_model');
            $this->todays_audio_model->generate_for_today(TRUE);
        }
    }
}
