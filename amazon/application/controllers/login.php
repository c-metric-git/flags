<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->data = array();        
    }

    public function index() 
    {
        $admin_session = $this->session->userdata('admin_session');        
        if ($admin_session['active'] == TRUE)
        {
           redirect('cdashbord');
        }
        else 
            $this->doLogin();
    }
    
    /*
        @Description : Check Login is valid or not (Admin login)
        @Author      : Kashyap Padh
        @Input       : useremail, passowrd and / or useremail
        @Output      : true or false
        @Date        : 15-08-2013
    */
    public function doLogin() 
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $forgot_password = $this->input->post('forgotpwd');
        if($forgot_password)
        {
            $this->forgetpw_action();
        }
        else
        {            
            if($email && $password)
            {                
                $field = array('sha_key');
                $match = array('email'=>$email);
                $sha = $this->user_model->getuser($field,$match,'','=');                
                if($sha)
                {
                    $sha = $sha[0]['sha_key'];
                    $password = sha1($password.$sha);
                    $result = $this->user_model->check_login($email, $password);
                    $field = array('id','name','email');
                    $match = array('email'=>$email,'password'=>$password);
                    $udata = $this->user_model->getuser($field, $match,'','=');
                    if(count($udata) > 0)
                    {
                        $newdata = array(
                                'name'  => $udata[0]['name'],
                                'id' =>$udata[0]['id'],
                                'useremail' =>$udata[0]['email'],
                                'active' => TRUE);
                        $this->session->set_userdata('admin_session', $newdata);
                        redirect('cdashbord');
                    }
                    else
                    {
                        $this->data['msg'] = "Invalid user name or password";
                        $this->load->view('login',$this->data);
                    }
                }
                else
                {
                    $this->data['msg'] = "Invalid user name or password";
                    $this->load->view('login',$this->data);
                }
            }
            else
            {                
                $this->data['msg'] = "Invalid user name or password";
                $this->load->view('login',$this->data);
            }
        }
    }   
    
    /*
        @Description : Function to generate password and email the same to user
        @Author      : Kashyap Padh
        @Input       : email address
        @Output      : password to the email address
        @Date        : 15-08-2013
    */
    public function forgetpw_action()
    {
        $email = strip_tags($this->input->post('email'));
        if(isset($email))
        {
            $fields=array('id','name');
            $arr=array('email'=> $email);
            $result = $this->user_model->getuser($fields,$arr,'','=');
            if($result)
            {
                $gen_pw = $this->randr(8);
                $sha = uniqid(mt_rand(), true);
                $pass = sha1($gen_pw.$sha);
                $forget_pw = $this->user_model->forgetpw($email,$pass,$sha);

                if($forget_pw == 1)
                {                    
                    $sub = "Amazon API : New Password ";                    
                    $msg = '<body>
                    <table width="700" border="0" cellspacing="0" cellpadding="0" style="border:5px solid #333;" bgcolor="#e6e4e1">
                    <tr><td></td></tr>
                    <tr>
                    <td align="center"><h1 style="font-family:Arial, Helvetica, sans-serif; font-size:24px; color:#333;">Hello <span style="color:#990000;">'.$result[0]['name'].'</span></h1></td>
                    </tr>
                    <tr>
                    <td height="40">&nbsp;</td>
                    </tr>
                    <tr>
                    <td style="color:#333; font-size:14px; font-family:Arial, Helvetica, sans-serif; text-align:center;">Your New password is : <b>'.$gen_pw.'</b></td>
                    </tr>
                    <tr>
                    <td height="40">&nbsp;</td>
                    </tr>
                    </table>
                    </body>';

                    unset($config);
                    $this->load->library('email');
                    $config['protocol'] = 'sendmail';
                    $config['mailpath'] = '/usr/sbin/sendmail';
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                    $config['protocol'] = 'smtp';
                    $config['smtp_port'] = '26';
                    $config['smtp_host'] = 'mail.tops-tech.com';
                    $config['smtp_user'] = 'test@tops-tech.com';  
                    $config['smtp_pass'] = 'tops123';  
                    $config['mailtype']='html';
                    $config['newline']="\r\n";
                    $this->load->library('email', $config);

                    $this->email->initialize($config);
                    $this->email->from('demotops@gmail.com',"Admin");                
                    $this->email->to($email);                
                    $this->email->subject($sub);
                    $this->email->message($msg);	
                    $this->email->send();
                }
                $this->data['msg'] = "Mail Sent Successfully";                
            }
            else
                $this->data['msg'] = "No Such User Found";
        }
        else
            $this->data['msg'] = "No Such User Found";
        
        $this->load->view('login',$this->data);
    }
    
    /*
        @Description : Function to generate random password
        @Author      : Kashyap Padh
        @Input       : length of password 
        @Output      : generated password
        @Date        : 15-08-2013
    */
    public function randr($j = 8)
    {
        $string = "";
        for($i=0;$i < $j;$i++)
        {
            srand((double)microtime()*1234567);
            $x = mt_rand(0,2);
            switch($x)
            {
                case 0:$string.= chr(mt_rand(97,122));break;
                case 1:$string.= chr(mt_rand(65,90));break;
                case 2:$string.= chr(mt_rand(48,57));break;
            }
        }
        return strtoupper($string);
    }
    
}
?>