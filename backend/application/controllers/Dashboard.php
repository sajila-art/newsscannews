<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function index() {
        $data['stats'] = array(
            'articles' => 0,
            'published' => 0,
            'categories' => 0,
            'active_categories' => 0
        );
        $data['latest_news'] = array();
        $data['categories'] = array();
        $data['articles_available'] = $this->db->table_exists('articles');

        if ($data['articles_available']) {
            $article_fields = $this->table_fields('articles');
            $data['stats']['articles'] = $this->db->count_all('articles');

            if (in_array('status', $article_fields, TRUE)) {
                $data['stats']['published'] = $this->db
                    ->where_in('status', array('published', 'active'))
                    ->count_all_results('articles');
            }

            $data['latest_news'] = $this->get_latest_news($article_fields);
        }

        if ($this->db->table_exists('categories')) {
            $category_fields = $this->table_fields('categories');
            $data['stats']['categories'] = $this->db->count_all('categories');

            if (in_array('status', $category_fields, TRUE)) {
                $data['stats']['active_categories'] = $this->db
                    ->where('status', 'active')
                    ->count_all_results('categories');
            }

            $category_select = array('id');
            foreach (array('name_malayalam', 'name_english', 'slug', 'status', 'display_order') as $field) {
                if (in_array($field, $category_fields, TRUE)) {
                    $category_select[] = $field;
                }
            }

            $this->db->select(implode(', ', $category_select))->from('categories');
            if (in_array('display_order', $category_fields, TRUE)) {
                $this->db->order_by('display_order', 'ASC');
            }
            $data['categories'] = $this->db->limit(8)->get()->result_array();
        }

        $this->render('dashboard', $data);
    }

    private function get_latest_news($fields) {
        $title_field = $this->first_existing_field(
            $fields,
            array('title', 'title_malayalam', 'headline', 'name_malayalam', 'name_english')
        );
        if ($title_field === null) {
            return array();
        }

        $select = array('a.' . $title_field . ' AS title');
        foreach (array(
            'id' => array('id'),
            'status' => array('status'),
            'published_at' => array('published_at', 'created_at', 'updated_at')
        ) as $alias => $candidates) {
            $field = $this->first_existing_field($fields, $candidates);
            if ($field !== null) {
                $select[] = 'a.' . $field . ' AS ' . $alias;
            }
        }

        $category_field = $this->first_existing_field($fields, array('category_id'));
        $join_categories = $category_field !== null
            && $this->db->table_exists('categories')
            && in_array('id', $this->table_fields('categories'), TRUE);

        if ($join_categories) {
            $category_fields = $this->table_fields('categories');
            $category_name_field = $this->first_existing_field(
                $category_fields,
                array('name_malayalam', 'name_english', 'name')
            );
            if ($category_name_field !== null) {
                $select[] = 'c.' . $category_name_field . ' AS category_name';
            }
        }

        $this->db->select(implode(', ', $select), FALSE)->from('articles AS a');
        if ($join_categories) {
            $this->db->join('categories AS c', 'c.id = a.' . $category_field, 'left');
        }

        $order_field = $this->first_existing_field($fields, array('published_at', 'created_at', 'id'));
        if ($order_field !== null) {
            $this->db->order_by('a.' . $order_field, 'DESC');
        }

        return $this->db->limit(8)->get()->result_array();
    }

    private function table_fields($table) {
        $fields = array();
        foreach ($this->db->field_data($table) as $field) {
            $fields[] = $field->name;
        }

        return $fields;
    }

    private function first_existing_field($fields, $candidates) {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $fields, TRUE)) {
                return $candidate;
            }
        }

        return null;
    }
}
