<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_row_by_id')) {
    
    function get_row_by_id($table, $condi, $select) { 
        $CI =& get_instance(); 
        $CI->load->database(); 
        $CI->db->select($select);
        $query = $CI->db->get_where($table, $condi);
      
        return $query->row(); 
    }
}
if (!function_exists('get_all')) {
    function get_all($table, $condi, $select) { 
        $CI =& get_instance(); 
        $CI->load->database(); 
        $CI->db->select($select);
        $query = $CI->db->get_where($table, $condi);
        return $query->result(); 
    }
}

if (!function_exists('get_quotation_items')) {
    function get_quotation_items($quotation_id) {
        $CI =& get_instance();
        $CI->load->database();

        return $CI->db
            ->select('*')
            ->from('quotation_items')
            ->where('quotation_id', (int) $quotation_id)
            ->get()
            ->result_array();
    }
}

if (!function_exists('get_quotation_milestones')) {
    function get_quotation_milestones($quotation_id) {
        $CI =& get_instance();
        $CI->load->database();

        return $CI->db
            ->select('*')
            ->from('milestones')
            ->where('quotation_id', (int) $quotation_id)
            ->order_by('sequence_no')
            ->get()
            ->result_array();
    }
}

   if (!function_exists('check_row_exists')) { 
    function check_row_exists($table, $condi) { 
       $CI =& get_instance(); 
        $CI->load->database(); 
        $query = $CI->db->get_where($table, $condi);
      echo $CI->db->last_query();exit;
        return $query->num_rows(); 
    }
}
?>