<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Audio extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('todays_audio_model');
    }

    /**
     * Today's Audio News Management View
     */
    public function index() {
        $this->require_roles(array('admin'));

        $today = date('Y-m-d');
        $data['title'] = "Today's Audio News";
        $data['today_date'] = $today;
        $data['broadcast_articles'] = $this->todays_audio_model->get_todays_broadcast_articles($today);
        $data['todays_audio'] = $this->todays_audio_model->get_today();
        $data['history'] = $this->todays_audio_model->get_history(10);

        $this->render('audio/index', $data);
    }

    /**
     * Generate audio news from today's live-news-broadcast articles & save to todays_audio table.
     */
    public function generate() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            redirect('audio');
            return;
        }

        $today = date('Y-m-d');
        $articles = $this->todays_audio_model->get_todays_broadcast_articles($today);

        if (empty($articles)) {
            $this->session->set_flashdata(
                'error',
                'No published articles found in "live-news-broadcast" for today (' . $today . ').'
            );
            redirect('audio');
            return;
        }

        $record = $this->todays_audio_model->generate_for_today(TRUE);
        $this->audit('generate', 'todays_audio', $record['id'], 'articles=' . $record['article_count']);

        $msg = 'Today\'s audio news generated from ' . $record['article_count'] . ' live-news-broadcast article(s) and saved to todays_audio.';
        $this->session->set_flashdata('success', $msg);
        redirect('audio');
    }

    /**
     * Update title, script_text, or upload custom MP3/WAV audio file for today's audio.
     */
    public function save() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            redirect('audio');
            return;
        }

        $today = date('Y-m-d');
        $title = trim((string) $this->input->post('title', TRUE));
        $script_text = trim((string) $this->input->post('script_text', TRUE));
        $duration_label = trim((string) $this->input->post('duration_label', TRUE));

        if ($title === '' || $script_text === '') {
            $this->session->set_flashdata('error', 'Title and broadcast script cannot be empty.');
            redirect('audio');
            return;
        }

        $audio_url = NULL;
        if (!empty($_FILES['audio_file']['name'])) {
            $upload_dir = FCPATH . 'uploads/audio/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0777, TRUE);
            }

            $config = array(
                'upload_path'   => $upload_dir,
                'allowed_types' => 'mp3|wav|ogg|m4a|aac',
                'max_size'      => 20480, // 20MB
                'encrypt_name'  => TRUE
            );

            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('audio_file')) {
                $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
                redirect('audio');
                return;
            }

            $upload_data = $this->upload->data();
            $audio_url = base_url('uploads/audio/' . $upload_data['file_name']);
        }

        $articles = $this->todays_audio_model->get_todays_broadcast_articles($today);
        $article_ids = array_map(function ($a) { return (string) $a['id']; }, $articles);

        $existing = $this->db->get_where('todays_audio', array('audio_date' => $today))->row_array();

        $update_data = array(
            'audio_date' => $today,
            'title' => $title,
            'script_text' => $script_text,
            'duration_label' => $duration_label !== '' ? $duration_label : '03:00',
            'article_ids_json' => json_encode($article_ids, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'article_count' => count($articles),
            'status' => 'active'
        );

        if ($audio_url !== NULL) {
            $update_data['audio_url'] = $audio_url;
        }

        if ($existing) {
            $this->db->where('id', (int) $existing['id'])->update('todays_audio', $update_data);
            $id = (int) $existing['id'];
        } else {
            $this->db->insert('todays_audio', $update_data);
            $id = (int) $this->db->insert_id();
        }

        $this->audit('update', 'todays_audio', $id, 'title=' . $title);
        $this->session->set_flashdata('success', "Today's audio news saved successfully!");
        redirect('audio');
    }
}
