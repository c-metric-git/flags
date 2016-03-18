<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

    function __construct() 
    {        
        parent::__construct();
        $admin_session = $this->session->userdata('admin_session');        
        if ($admin_session['active'] === TRUE)
            redirect('cdashbord');
        else 
            redirect('login');
    }
}

?>