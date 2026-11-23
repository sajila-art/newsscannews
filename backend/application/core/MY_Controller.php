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
            $user_id = ($this->user && isset($this->user['id'])) ? (int) $this->user['id'] : null;
            if ($user_id !== null && $this->db->table_exists('users')) {
                if ($this->db->where('id', $user_id)->count_all_results('users') === 0) {
                    $user_id = null;
                }
            }

            $safe_entity_id = null;
            $details_str = (string) $details;
            if ($entity_id !== null && $entity_id !== '') {
                if (ctype_digit((string) $entity_id)) {
                    $safe_entity_id = (int) $entity_id;
                } else {
                    $details_str = trim('ref=' . $entity_id . ($details_str !== '' ? ' | ' . $details_str : ''));
                }
            }

            $this->db->insert('audit_logs', array(
                'user_id' => $user_id,
                'action' => $action,
                'entity_type' => $entity_type,
                'entity_id' => $safe_entity_id,
                'details' => substr($details_str, 0, 4000),
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
