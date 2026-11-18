<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model
{
   
    function __construct()
    {
        parent::__construct();
      //  $this->db = $this->load->database('default', TRUE, TRUE);
    }

  function insert_data($table,$data)
	{ 
        $this->db->insert($table, $data);
    //echo $this->db->last_query();exit;
        $pri_id=$this->db->insert_id();
    
        return $pri_id;
        
	}

    function alter($table, $condi, $fields){
        $this->db->where($condi);
        $this->db->update($table, $fields);
//echo $this->db->last_query(); exit;
        //$afftectedRows = $this->db->affected_rows();
        return 1; 
    }
    
    
    function check_exists($table, $conditions) {
    $this->db->from($table);
    $this->db->where($conditions);
   
    return $this->db->count_all_results() > 0;
}

    function get_all($table, $select,$condi){
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($condi);
        $query = $this->db->get();
   
        return  $query->result();
    }

    function get_all2($table, $select, $condi) {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($condi);
        $count = $this->db->count_all_results();
         return $count; 
       
    }
    function get_all_by_order($table, $select,$condi, $order_by, $order){
      $this->db->select($select);
      $this->db->from($table);
      $this->db->where($condi);
      $this->db->order_by($order_by, $order);
      $query = $this->db->get();
     //  echo $this->db->last_query();
      return  $query->result();
  }
  
   function get_all_by_order_join($table, $select, $condi, $join_table, $join_condition, $join_type, $order_by, $order) {
    $this->db->select($select);
    $this->db->from($table);

    $this->db->join($join_table, $join_condition, $join_type);

    $this->db->where($condi);
    $this->db->order_by($order_by, $order);
    
    $query = $this->db->get();
  
    return $query->result();
}
  
  
  
  
  function get_row_by_id($table, $select, $condi){
    $this->db->select($select);
    $this->db->where($condi);
    $this->db->from($table);
    $query = $this->db->get();
 
    $row = $query->row(); 
    return $row;
  }
  function pagination_get_all($table, $select, $con, $limit, $start, $order_colum, $order_by){
    $this->db->select($select);
    $this->db->where($con);
    $this->db->from($table);
    $this->db->order_by($order_colum, $order_by);
    $this->db->limit($limit,$start);
    $query = $this->db->get();
 ///echo $this->db->last_query(); exit;
    return $query->result();
}


function pagination_get_all_join($table, $select, $con, $join_table, $join_condition, $join_type, $limit, $start, $order_colum, $order_by){
    $this->db->select($select);
     $this->db->from($table);
     $this->db->join($join_table, $join_condition, $join_type);
    $this->db->where($con);
   
    $this->db->order_by($order_colum, $order_by);
    $this->db->limit($limit,$start);
    $query = $this->db->get();
    return $query->result();
}
 function pagination_famous_place($table, $select, $con, $value, $limit, $start, $order_colum, $order_by){
           $this->db->select($select);
        $this->db->from($table);
        $this->db->where($con);
        $this->db->group_start(); 
        $this->db->like('name', $value);
        $this->db->or_like('area', $value); 
        $this->db->group_end();   
    $this->db->order_by($order_colum, $order_by);
    $this->db->limit($limit,$start);
    $query = $this->db->get();
    return $query->result();
}
	function select1($table,$cond)//edit
		{
		 $this->db->from($table);
         $this->db->where($cond);           
         $reg=$this->db->get();
		// echo  $this->db->last_query(); exit;
		 return $reg;
		}

	function join_twotables($table_one,  $condi, $limit, $offset){
			$this->db->from('activity_booking');
			$this->db->join('bv_activities', 'activity_booking.activity_id = bv_activities.activity_id');
			$this->db->where($condi);
			$this->db->order_by('activity_booking.booking_id', 'desc');
			$this->db->limit($limit,$offset);
			$query = $this->db->get();
		  // echo $this->db->last_query(); exit;
			return $query->result();
		}
  function delete($table, $con)
  {
      $this->db->where($con);
     $result= $this->db->delete($table);
      $afftectedRows = $this->db->affected_rows();
      return $afftectedRows; 
  }

  public function activity_details($activity_id)
    {
        $result = [];
        $this->db->select('a.*, c.activity_category_name as category, m_city.*, m_city.name as cityname, m_country.*, m_country.nicename as countryname');
        $this->db->from('bv_activities as a');
        // $this->db->where('a.status', 1);
        $this->db->where('a.delete', 0);
        $this->db->where('a.activity_id', $activity_id);
        $this->db->join('activity_category as c', 'a.activity_category_id = c.activity_category_id', 'left');
        $this->db->join('m_city', 'a.city = m_city.ci_id', 'left');
        $this->db->join('m_country', 'a.country = co_id', 'left');

        $query = $this->db->get();
        if ($query->num_rows()) {
            $result = $query->result();
        }
        return $result;
    }

    public function fetchactivitycitydetails($cityid)
    {
        $sql1 = "SELECT * FROM `m_city` WHERE ci_id = $cityid";
        $details = $this->db->query($sql1)->result_array();
        return $details;
    }

    public function fetchactivityagent($activityid)
    {
        $sql = "SELECT done_by FROM `bv_activities` where activity_id = $activityid";
        $query = $this->db->query($sql);
        $response = $query->result();
        return $response;
    }
}


?>