<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
	
	class Logout extends CI_Controller
	{
            public function index()
            {
                $admin_session = $this->session->userdata('admin_session');
                
                if($admin_session['active']==TRUE)
                {
                    $this->session->unset_userdata('admin_session');
                    $this->session->unset_userdata('name');
                    $this->session->unset_userdata('id');
                    $this->session->unset_userdata('useremail');
                    $this->session->unset_userdata('active');
                    $this->session->unset_userdata('admin_session');
                    $this->load->helper('cookie');
                    $cookie=  $this->config->item('sess_cookie_name');
                    delete_cookie($cookie);
                    redirect('login');
                }
                else
                    redirect('login');
            }
	}
?>