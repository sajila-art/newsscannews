<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class admin_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    public function login($table, $con)
    { //print_r("here model");exit;
        $this->db->select('*');
        $this->db->from($table);
        // $this->db->where('m_admin.a_login_name = ' . "'" . $name . "'");
        // $this->db->where('m_admin.a_password = ' . "'" . $password . "'");
        $this->db->where($con);
        //$this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row(); 
        if ($query->num_rows() > 0) {
            return $row;
        }
        else{
            return 0;
        }
    }
  
    public function insert($table, $data)
    { 
        $this->db->insert($table, $data);
      // echo $this->db->last_query();exit;
        $reg = $this->db->insert_id();
       
        return $reg;
    }
    public function delete_r($table, $con)
    {
        $this->db->where($con);
        $this->db->delete($table);
    }
   
  

   
	
	function alter($table = '', $data = [], $where = '') {
		$this->db->where($where);
		$this->db->update($table, $data);
		$result = $this->db->affected_rows();
		return $result;
	}
	
} ?>