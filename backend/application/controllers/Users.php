<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(array('session', 'form_validation'));
    }

    public function index() {
        $this->require_roles(array('admin'));

        $data['rows'] = $this->db
            ->select('id, name, email, role, is_active, created_at, last_login_at')
            ->order_by('id', 'DESC')
            ->get('users')
            ->result_array();

        $this->render('users/index', $data);
    }

    public function create() {
        $this->require_roles(array('admin'));

        $this->form_validation->set_rules('name', 'Name', 'required|max_length[150]');
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email|callback_email_available'
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[12]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,manager,accounts,staff]');

        $email = strtolower($this->input->post('email', TRUE));
        if (!$this->form_validation->run()) {
            $this->index();
            return;
        }

        $role = $this->input->post('role');
        $this->db->insert('users', array(
            'name' => $this->input->post('name'),
            'email' => $email,
            'password_hash' => password_hash($this->input->post('password', FALSE), PASSWORD_DEFAULT),
            'role' => $role,
            'is_active' => 1
        ));

        $id = $this->db->insert_id();
        $this->audit('create', 'user', $id, 'role=' . $role);
        redirect('users');
    }

    public function edit($id) {
        $this->require_roles(array('admin'));

        $data['user'] = $this->find_user($id);
        if (!$data['user']) {
            show_404();
        }

        $this->render('users/edit', $data);
    }

    public function update($id) {
        $this->require_roles(array('admin'));

        $user = $this->find_user($id);
       
        if (!$user) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Name', 'required|max_length[150]');
        $this->form_validation->set_rules(
            'email',
            'Email',
            'trim|required|valid_email|max_length[190]|callback_email_available_for_update[' . (int) $id . ']'
        );
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,manager,accounts,staff]');

        $password = $this->input->post('password', FALSE);
        if ($password === FALSE) {
            $password = '';
        }

        if ($password !== '') {
            $this->form_validation->set_rules('password', 'Password', 'min_length[12]');
        }

        $email = strtolower($this->input->post('email', TRUE));
        if (!$this->form_validation->run()) {
            $data['user'] = $user;
            $this->render('users/edit', $data);
            return;
        }

        $role = $this->input->post('role');
        $update = array(
            'name' => $this->input->post('name', TRUE),
            'email' => $email,
            'role' => $role,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );

        if ($password !== '') {
            $update['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->where('id', (int) $id)->update('users', $update);
        $this->audit('update', 'user', $id, 'role=' . $role);
        redirect('users');
    }

    public function email_available($email) {
        if ($this->email_exists(strtolower($email))) {
            $this->form_validation->set_message('email_available', 'That email address is already in use.');
            return FALSE;
        }

        return TRUE;
    }

    public function email_available_for_update($email, $id) {
        $duplicate = $this->db
            ->where('email', strtolower($email))
            ->where('id !=', (int) $id)
            ->count_all_results('users') > 0;

        if ($duplicate) {
            $this->form_validation->set_message(
                'email_available_for_update',
                'That email address is already in use.'
            );
            return FALSE;
        }

        return TRUE;
    }

    private function find_user($id) {
        return $this->db
            ->select('id, name, email, role, is_active, created_at, last_login_at')
            ->get_where('users', array('id' => (int) $id))
            ->row_array();
    }

    private function email_exists($email) {
        return $this->db
            ->where('email', $email)
            ->count_all_results('users') > 0;
    }
}
