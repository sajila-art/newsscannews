<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {

    private $table = 'categories';

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('common_model');
        $this->ensure_category_home_page_column();
    }

    private function ensure_category_home_page_column() {
        if ($this->db->table_exists($this->table)) {
            if (!$this->db->field_exists('home_page', $this->table)) {
                $this->load->dbforge();
                $this->dbforge->add_column($this->table, array(
                    'home_page' => array(
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 1,
                        'after' => 'status'
                    )
                ));
            }
        }
    }

    /**
     * Categories Listing View
     */
    public function index() {
        $this->require_roles(array('admin'));

        $data['title'] = 'Categories List';
        $data['rows'] = $this->common_model->get_all_by_order(
            $this->table,
            'id, name_malayalam, name_english, slug, description, display_order, status, home_page, created_at, updated_at',
            array(),
            'display_order',
            'ASC'
        );

        $this->render('categories/list', $data);
    }

    /**
     * Add Category View
     */
    public function add() {
        $this->require_roles(array('admin'));

        $data['title'] = 'Add Category';
        $this->render('categories/add', $data);
    }

    /**
     * Handles Form Submission for New Category
     */
    public function create() {
        $this->require_roles(array('admin'));

        if ($this->input->method() !== 'post') {
            redirect('category/add');
            return;
        }

        $this->set_category_rules();
        if (!$this->form_validation->run()) {
            $data['title'] = 'Add Category';
            $this->render('categories/add', $data);
            return;
        }

        $category = $this->category_input();
        $id = $this->common_model->insert_data($this->table, $category);
        $this->audit('create', 'category', $id, 'slug=' . $category['slug']);

        $this->session->set_flashdata('success', 'Category "' . $category['name_english'] . '" created successfully!');
        redirect('category');
    }

    /**
     * Edit Category View
     */
    public function edit($id) {
        $this->require_roles(array('admin'));

        $data['title'] = 'Edit Category';
        $data['category'] = $this->find_category($id);
        if (!$data['category']) {
            show_404();
        }

        $this->render('categories/edit', $data);
    }

    /**
     * Handles Form Submission for Updating Category
     */
    public function update($id) {
        $this->require_roles(array('admin'));

        $category = $this->find_category($id);
        if (!$category) {
            show_404();
        }

        $this->set_category_rules($id);
        if (!$this->form_validation->run()) {
            $data['title'] = 'Edit Category';
            $data['category'] = $category;
            $this->render('categories/edit', $data);
            return;
        }

        $update = $this->category_input();
        $this->common_model->alter($this->table, array('id' => (int) $id), $update);
        $this->audit('update', 'category', (int) $id, 'slug=' . $update['slug']);

        $this->session->set_flashdata('success', 'Category updated successfully!');
        redirect('category');
    }

    /**
     * Delete Category
     */
    public function delete($id) {
        $this->require_roles(array('admin'));

        $category = $this->find_category($id);
        if ($category) {
            $this->db->where('id', (int) $id)->delete($this->table);
            $this->audit('delete', 'category', (int) $id, 'slug=' . $category->slug);
            $this->session->set_flashdata('success', 'Category deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Category not found.');
        }

        redirect('category');
    }

    public function slug_available($slug) {
        if ($this->common_model->check_exists($this->table, array('slug' => trim($slug)))) {
            $this->form_validation->set_message('slug_available', 'That category slug is already in use.');
            return FALSE;
        }

        return TRUE;
    }

    public function slug_available_for_update($slug, $id) {
        $duplicate = $this->common_model->check_exists($this->table, array(
            'slug' => trim($slug),
            'id !=' => (int) $id
        ));

        if ($duplicate) {
            $this->form_validation->set_message(
                'slug_available_for_update',
                'That category slug is already in use.'
            );
            return FALSE;
        }

        return TRUE;
    }

    private function set_category_rules($id = null) {
        $this->form_validation->set_rules(
            'name_malayalam',
            'Malayalam name',
            'trim|required|max_length[255]'
        );
        $this->form_validation->set_rules(
            'name_english',
            'English name',
            'trim|required|max_length[255]'
        );

        $slug_rule = 'trim|required|max_length[191]|callback_slug_available';
        if ($id !== null) {
            $slug_rule = 'trim|required|max_length[191]|callback_slug_available_for_update[' . (int) $id . ']';
        }
        $this->form_validation->set_rules('slug', 'Slug', $slug_rule);

        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('display_order', 'Display order', 'integer');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive]');
        $this->form_validation->set_rules('home_page', 'Home Page Display', 'integer|in_list[0,1]');
    }

    private function category_input() {
        return array(
            'name_malayalam' => $this->input->post('name_malayalam', TRUE),
            'name_english' => $this->input->post('name_english', TRUE),
            'slug' => $this->input->post('slug', TRUE),
            'description' => $this->input->post('description', TRUE),
            'display_order' => (int) $this->input->post('display_order'),
            'status' => $this->input->post('status', TRUE),
            'home_page' => $this->input->post('home_page') !== null ? (int) $this->input->post('home_page') : 1
        );
    }

    private function find_category($id) {
        return $this->common_model->get_row_by_id(
            $this->table,
            'id, name_malayalam, name_english, slug, description, display_order, status, home_page, created_at, updated_at',
            array('id' => (int) $id)
        );
    }
}
