<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    private $table = 'home_banner';

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('common_model');
        $this->ensure_home_banner_table();
    }

    /**
     * Auto-create and seed the home_banner table if it does not exist yet.
     */
    private function ensure_home_banner_table() {
        if (!$this->db->table_exists($this->table)) {
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
            $this->dbforge->create_table($this->table, TRUE);

            // Seed default lead banner
            $this->db->insert($this->table, array(
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

    /**
     * Home Page Lead Banner Management View
     */
    public function index() {
        $this->require_roles(array('admin'));

        $banner = $this->get_or_create_banner();
        $data['title'] = 'Home Page Management';
        $data['banner'] = $banner;

        $this->render('home/index', $data);
    }

    /**
     * Update Home Page Banner, Title, Description, Author
     */
    public function update() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            redirect('home');
            return;
        }

        $this->form_validation->set_rules('title', 'Headline / Title', 'trim|required|max_length[300]');
        $this->form_validation->set_rules('description', 'Description / Summary', 'trim|required');
        $this->form_validation->set_rules('author', 'Author / Reporter Name', 'trim|required|max_length[160]');
        $this->form_validation->set_rules('author_role', 'Author Role', 'trim|max_length[160]');
        $this->form_validation->set_rules('banner_image', 'Banner Image URL', 'trim|max_length[1000]');
        $this->form_validation->set_rules('subtitle', 'Subtitle', 'trim|max_length[500]');
        $this->form_validation->set_rules('badge', 'Badge / Kicker', 'trim|max_length[100]');
        $this->form_validation->set_rules('link_url', 'Read More Link', 'trim|max_length[500]');

        if (!$this->form_validation->run()) {
            $banner = $this->get_or_create_banner();
            $data['title'] = 'Home Page Management';
            $data['banner'] = $banner;
            $this->render('home/index', $data);
            return;
        }

        $update_data = array(
            'title' => $this->input->post('title', TRUE),
            'subtitle' => $this->input->post('subtitle', TRUE) ?: null,
            'description' => $this->input->post('description', TRUE),
            'author' => $this->input->post('author', TRUE),
            'author_role' => $this->input->post('author_role', TRUE) ?: 'Special Correspondent',
            'banner_image' => $this->input->post('banner_image', TRUE) ?: null,
            'badge' => $this->input->post('badge', TRUE) ?: 'പ്രധാന വാർത്ത',
            'link_url' => $this->input->post('link_url', TRUE) ?: '#',
            'status' => 'active'
        );

        $banner = $this->get_or_create_banner();
        $this->db->where('id', $banner->id)->update($this->table, $update_data);
        $this->audit('update', 'home_banner', $banner->id, 'title=' . $update_data['title']);

        $this->session->set_flashdata('success', 'Home page banner, title, description, and author updated successfully!');
        redirect('home');
    }

    private function get_or_create_banner() {
        $row = $this->db->get($this->table, 1)->row();
        if (!$row) {
            $default = array(
                'banner_image' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1600&q=80',
                'title' => 'കേരളത്തിൽ ഡിജിറ്റൽ വിപ്ലവം: മുഴുവൻ തദ്ദേശ സ്ഥാപനങ്ങളും ഓൺലൈൻ സേവനത്തിലേക്ക്',
                'subtitle' => 'പൊതുജനങ്ങൾക്ക് സുതാര്യമായ സേവനം ഉറപ്പാക്കുന്ന സമഗ്ര ഭരണ പരിഷ്കാരം',
                'description' => 'സംസ്ഥാനത്തെ മുഴുവൻ തദ്ദേശ സ്വയംഭരണ സ്ഥാപനങ്ങളിലെയും സേവനങ്ങൾ സമ്പൂർണ്ണമായി ഡിജിറ്റൈസ് ചെയ്തു.',
                'author' => 'കെ. ആർ. നാരായണൻ',
                'author_role' => 'ചീഫ് റിപ്പോർട്ടർ',
                'badge' => 'പ്രധാന വാർത്ത',
                'link_url' => '#',
                'status' => 'active'
            );
            $this->db->insert($this->table, $default);
            return $this->db->get($this->table, 1)->row();
        }
        return $row;
    }
}
