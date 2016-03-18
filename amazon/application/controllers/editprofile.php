<?php 
class Editprofile extends CI_Controller
{
	
    function __construct()
    {
        parent::__construct();
        $this->admin_session = $this->session->userdata('admin_session');
        (!$this->admin_session['active']=== TRUE) ? redirect('login') : ''; 
        $this->load->model('user_model');
    }
    
    /*
    @Description: Function for load Edit Login Profile form
    @Author: Kashyap Padh
    @Input: - 
    @Output: User details
    @Date: 10-09-2013
    */
    public function index()
    {
        $this->edit_loginprofile();
    }
    /*
    @Description: Function for load Edit Login Profile form
    @Author: Kashyap Padh
    @Input: - Login user id
    @Output: - Detail for update
    @Date: 13-08-2013
    */
    public function edit_loginprofile()
    {
        
        $data['smenu_title']=$this->lang->line('admin_left_menu2');
        $data['submodule']=$this->lang->line('edit_ownprofile_tableheading');
        $this->load->view('include/header',$data);        
        $this->load->view('include/left');
        $match = array('id'=>$this->admin_session['id']);
        $data['profile'] = $this->user_model->getuser('',$match,'','=');
        //$uri_segment = ($this->uri->segment(2) == 'msg') ? $this->uri->segment(4) : $this->uri->segment();
        $data['msg'] = ($this->uri->segment(3) == 'msg') ? $this->uri->segment(4) : '';
        //$data['msg'] = ($this->uri->segment(3) == 'msg') ? $this->uri->segment(4) : '';
        $this->load->view('edit_ownprofile',$data);
    }

    /*
    @Description: Function for Update Login Profile
    @Author: Kashyap Padh
    @Input: - Details of login user
    @Output: - Update details
    @Date: 13-08-2013
    */
    public function update_admin()
    {
        $emailchange = 0; $log = 0;
        $id = $this->input->post('id');
        $emailid = $this->input->post('email');
        $fields=array('email');
        $arr=array('id'=>$id,'email'=>$emailid);
        $eresult = $this->user_model->getuser($fields,$arr,'','=');
        (count($eresult)==0)? $emailchange = 1: $emailchange = 0;
        if($emailchange == 1)
        {
            $fields = array('email');
            $match=array('email'=>$emailid);
            $result = $this->user_model->getuser($fields,$match,'','=');
            (count($result)>0)? $log = 1:$log = 0;
            if($log == 1)
            {
                $this->load->view('include/header');
                $this->load->view('include/left');
                $match = array('id'=>$this->admin_session['id']);
                $data['profile'] = $this->user_model->getuser('',$match,'','=');
                $msg = $this->lang->line("email_exist_error");
                $data['msg'] = $this->lang->line("email_exist_error");
                $this->load->view('edit_ownprofile',$data);
            }
        }
        if($emailchange == 0 || $log == 0)
        {
            $data['id'] = $this->input->post('id');
            $data['name'] = $this->input->post('fname');
            $data['email'] = $emailid;
            $this->user_model->update_user($data);

            ($emailchange == 0)?redirect('/editprofile/edit_loginprofile/msg/'.$this->lang->line('profile_edit_successmsg')):redirect('/logout');
        }
    }

    /*
    @Description: Function for Changer Password
    @Author: Kashyap Padh
    @Input: - oldpassword, newpassword, id
    @Output: - Update data
    @Date: 13-08-2013
    */
    function change_pw()
    {  
        $id = $this->admin_session['id'];
        $old_pw = $this->input->post('oldpw');
        $new_pw = $this->input->post('newpw');
        $fields = array('id,sha_key,password');
        $match = array('id'=>$id);
        $user = $this->user_model->getuser($fields,$match,'','=');
        print_r($user);

        $sha = $user[0]['sha_key'];
        $old_pass = sha1($old_pw.$sha);
        $new_pass = sha1($new_pw.$sha);
        
        if($user[0]['password'] == $old_pass)
        {
            $data['id'] = $id;
            $data['password'] = $new_pass;
            $change_pw = $this->user_model->updt_pw($data);
            if($change_pw == 1)
                redirect('editprofile/edit_loginprofile/msg/'.$this->lang->line('password_edit_successmsg'));
            else
                redirect('editprofile/edit_loginprofile/msg/'.$this->lang->line('old_password_notmatch'));
        }
        else
            redirect('editprofile/edit_loginprofile/msg/'.$this->lang->line('old_password_notmatch'));
    }
    
    /*
    @Description: Function for check content name exist or not
    @Author: Kashyap Padh
    @Input: - content name
    @Output: - Error if exist else continue
    @Date: 05-07-2013
    */
    public function getcontentname()
    {
        $email = $this->input->post('name');
        $fields = array('email');
        $match = array('email'=>$email);
        $result = $this->user_model->getuser($fields,$match,'','=');
        if(count($result) > 0)
            echo $this->lang->line('email_add_existerror');
    }
}
?>