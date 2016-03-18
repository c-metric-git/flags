<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CDashbord extends CI_Controller
{ 
    public function _construct()
    {
        parent::_construct();
    }

    public function index()
    {        
        $doc_session_array = $this->session->userdata('admin_session');
        ($doc_session_array['active'] == true) ? $this->displaydashbord() : redirect('login');
    }
	
    public function displaydashbord()
    {        
        $data['submodule'] = $this->lang->line('home_submodule');
        $data['smenu_title'] = $this->lang->line('home_mainmodule');
        $this->load->view('include/header',$data);        
        $this->load->view('include/left');
        $data['msg'] = ($this->uri->segment(3) == 'msg') ? $this->uri->segment(4) : '';
        $this->load->view('cdashbord',$data);
        $this->load->view('include/footer');
    }
}