<?php 

function handle_file_upload($upload_file_name, $path, $allowed_types, $max_size = 0, $file_name = "") {
    $CI = &get_instance();
    $CI->load->library('upload');
    
    $config['upload_path']   = $path;
    $config['allowed_types'] = $allowed_types;
    
    if (!empty($file_name)) {
        $config['file_name'] = $file_name;
    }

    if ($max_size > 0) {
        $config['max_size'] = $max_size;
    }

    $CI->upload->initialize($config);

    if (!$CI->upload->do_upload($upload_file_name)) {
        return ['error' => $CI->upload->display_errors()];
    } else {
        return ['upload_data' => $CI->upload->data()];
    }
}


function delete_file($basepath, $image) {     
    $path = $basepath . $image; 
    
    if (file_exists($path)) {
        if (unlink($path)) {
            $message = "The file $image has been deleted successfully.";
        } else {
            $message = "Error: Could not delete the file $image.";
        }
    } else {
        $message = "Error: The file $image does not exist.";
    }

    return $message;
}

?>