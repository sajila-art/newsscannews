<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected $user = null;

    public function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('user');
        $public = array('login');
        if (!in_array($this->router->fetch_class(), $public, true) && !$this->user) {
            redirect('login');
        }
    }

    protected function require_roles($roles) {
        if (!$this->user || !in_array($this->user['role'], (array)$roles, true)) {
            show_error('You do not have permission to access this page.', 403);
        }
    }

    protected function audit($action, $entity_type, $entity_id = null, $details = '') {
        if ($this->db->table_exists('audit_logs')) {
            $this->db->insert('audit_logs', array(
                'user_id' => $this->user ? (int)$this->user['id'] : null,
                'action' => $action,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'details' => substr((string)$details, 0, 4000),
                'ip_address' => $this->input->ip_address()
            ));
        }
    }

    protected function render($view, $data = array()) {
        $data['current_user'] = $this->user;
        $this->load->view('layout/header', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/footer');
    }

    protected function money($amount, $currency) {
        return strtoupper($currency) . ' ' . number_format((float)$amount, 2);
    }
}
