<?php 
if (!function_exists('get_pagination')) {
    function get_pagination($base_url, $count, $per_page,$uri){ 
        $ci =& get_instance();
        $ci->load->library('pagination');
        $config["base_url"] = $base_url;
        $config["total_rows"]=$count;
        $config["per_page"] = $per_page;
        $config["uri_segment"] = $uri;
        $config['reuse_query_string'] = TRUE;
        /*---designpagination--*/
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
         $config['first_link'] = $ci->lang->line('pagination_first_link');
         $config['last_link'] = $ci->lang->line('pagination_last_link');
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        
        $ci->pagination->initialize($config);

        return $ci->pagination->create_links();
    }
}
?>