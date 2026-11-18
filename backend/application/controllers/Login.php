<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('admin_model');
        $this->load->database();
        $this->ensure_admin_table_exists();
    }

    /**
     * Ensures 'admin' table exists in thenewsdb database and seeds default admin if empty
     */
    private function ensure_admin_table_exists() {
        if (!$this->db->table_exists('admin')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `admin` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(150) NOT NULL DEFAULT 'Admin',
                    `username` VARCHAR(100) NOT NULL UNIQUE,
                    `email` VARCHAR(150) NOT NULL UNIQUE,
                    `password` VARCHAR(255) NULL,
                    `password_hash` VARCHAR(255) NOT NULL,
                    `role` VARCHAR(50) DEFAULT 'admin',
                    `is_active` TINYINT(1) DEFAULT 1,
                    `last_login_at` DATETIME NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            // Seed default admin (admin / admin123)
            $this->db->insert('admin', array(
                'name' => 'Chief Editor',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'password_hash' => md5('admin123'),
                'role' => 'admin',
                'is_active' => 1
            ));
        } else {
            // Seed default admin if admin table is empty
            $count = $this->db->count_all('admin');
            if ($count === 0) {
                $fields = $this->db->list_fields('admin');
                $seed = array();
                if (in_array('name', $fields)) $seed['name'] = 'Chief Editor';
                if (in_array('username', $fields)) $seed['username'] = 'admin';
                if (in_array('email', $fields)) $seed['email'] = 'admin@gmail.com';
                if (in_array('password', $fields)) $seed['password'] = md5('admin123');
                if (in_array('password_hash', $fields)) $seed['password_hash'] = md5('admin123');
                if (in_array('role', $fields)) $seed['role'] = 'admin';
                if (in_array('is_active', $fields)) $seed['is_active'] = 1;
                if (!empty($seed)) {
                    $this->db->insert('admin', $seed);
                }
            }
        }
    }

    public function index() {
        if ($this->session->userdata('user')) {
            redirect('dashboard');
        }

        $this->load->view('login');
    }

    public function authenticate() {  
        if ($this->session->userdata('user')) {
            redirect('dashboard', 'refresh');
            return;
        }

        $this->form_validation->set_rules('login_id', 'Username or Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('login');
            return;
        }
      
        $login_id = $this->input->post('login_id', TRUE);
        $password_plain = $this->input->post('password');
        
        $user = null;

        // 1. Primary: Authenticate using 'admin' table in thenewsdb
        if ($this->db->table_exists('admin')) {
            $user = $this->find_authenticated_user('admin', $login_id, $password_plain);
        }

        // 2. Fallback: Check 'users' table if not found in 'admin'
        if (!$user && $this->db->table_exists('users')) {
            $user = $this->find_authenticated_user('users', $login_id, $password_plain);
        }

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid username/email or password');
            redirect('login');
        }

        // Update last login timestamp if field exists
        $target_table = isset($user->auth_table) ? $user->auth_table : 'admin';
        $table_fields = $this->db->list_fields($target_table);
        if (in_array('last_login_at', $table_fields)) {
            $this->db->where('id', $user->id)->update($target_table, array('last_login_at' => date('Y-m-d H:i:s')));
        }
    
        // Record audit log if table exists
        if ($this->db->table_exists('audit_logs')) {
            $data = array(
                'user_id' => $user->id,
                'action' => 'login',
                'entity_type' => 'admin',
                'entity_id' => $user->id,
                'ip_address' => $this->input->ip_address()
            );
            $this->db->insert('audit_logs', $data);
        }

        // Set session
        $user_role = isset($user->role) ? $user->role : 'admin';
        $user_name = !empty($user->name) ? $user->name : (!empty($user->username) ? $user->username : 'Admin');
        $user_email = !empty($user->email) ? $user->email : (!empty($user->username) ? $user->username : 'admin');

        $this->session->set_userdata('user', array(
            'id' => $user->id,
            'name' => $user_name,
            'email' => $user_email,
            'role' => $user_role
        ));

        redirect('dashboard', 'refresh');
    }

    private function find_authenticated_user($table, $login_id, $password) {
        $fields = $this->db->list_fields($table);
        $login_fields = array_intersect(array('email', 'username'), $fields);
        if ($table === 'admin' && in_array('a_login_name', $fields)) {
            $login_fields[] = 'a_login_name';
        }
        if (empty($login_fields)) {
            return null;
        }

        $this->db->group_start();
        foreach (array_values($login_fields) as $index => $field) {
            if ($index === 0) {
                $this->db->where($field, $login_id);
            } else {
                $this->db->or_where($field, $login_id);
            }
        }
        $this->db->group_end();

        if (in_array('is_active', $fields)) {
            $this->db->where('is_active', 1);
        } elseif ($table === 'admin' && in_array('status', $fields)) {
            $this->db->where('status', 'active');
        } elseif ($table === 'admin' && in_array('a_status', $fields)) {
            $this->db->where('a_status', 1);
        }

        foreach ($this->db->get($table)->result() as $candidate) {
            if ($this->verify_user_password($candidate, $password)) {
                $candidate->auth_table = $table;
                if (!isset($candidate->id) && isset($candidate->admin_id)) {
                    $candidate->id = $candidate->admin_id;
                }
                if (empty($candidate->name) && !empty($candidate->employee_name)) {
                    $candidate->name = $candidate->employee_name;
                }
                if (empty($candidate->username) && !empty($candidate->a_login_name)) {
                    $candidate->username = $candidate->a_login_name;
                }
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Verifies password against hash (supports MD5, password_hash bcrypt, sha1, plain)
     */
    private function verify_user_password($user, $plain_password) {
        $stored = '';
        if (isset($user->password_hash) && !empty($user->password_hash)) {
            $stored = $user->password_hash;
        } elseif (isset($user->password) && !empty($user->password)) {
            $stored = $user->password;
        }

        if (empty($stored)) {
            return false;
        }

        if (md5($plain_password) === $stored) {
            return true;
        }
        if (password_verify($plain_password, $stored)) {
            return true;
        }
        if (sha1($plain_password) === $stored) {
            return true;
        }
        if ($plain_password === $stored) {
            return true;
        }

        return false;
    }

    public function dashboard() {
        if (!$this->session->userdata('user')) {
            redirect('login');
        }

        redirect('dashboard');
    }

    public function logout() {
        $user = $this->session->userdata('user');
        if ($user && $this->db->table_exists('audit_logs')) {
            $this->db->insert('audit_logs', array(
                'user_id' => $user['id'],
                'action' => 'logout',
                'entity_type' => 'admin',
                'entity_id' => $user['id'],
                'ip_address' => $this->input->ip_address()
            ));
        }

        $this->session->sess_destroy();
        redirect('login');
    }
}
