<?php
class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->table_name = 'user';
    }
    
    /*
        @Description: Check Login is valid or not
        @Author     : Kashyap Padh
        @Input      : User Email id and Password
        @Output     : If validate then go to home page else login error
        @Date       : 15-08-2013
    */
    
    public function check_login($email, $password)
    {
        $fields = array('id','email','name');
        $match = array('email'=>$email,'password'=>$password);
        $result = $this->getuser($fields,$match,'','=');
        if($result)        
            return true;
        else
            return false;
    }

    /*
        @Description: Check Login is valid or not
        @Author     : Kashyap Padh
        @Input      : Email id and new system generated password
        @Output     : If query execute (password change in DB return 1 else 0
        @Date       : 15-08-2013
    */
    public function forgetpw($email,$gen_pw,$sha)
    {
        $data['password'] = $gen_pw;
        $data['sha_key'] = $sha;
        $this->db->where('email',$email);
        $query = $this->db->update($this->table_name,$data);
        return $s = ($query?1:0);
    }
    
    /*
        @Description: Function for Update Password
        @Author     : Kashyap Padh
        @Input      : User Id and new Password
        @Output     : Updated record
        @Date       : 15-08-2013
    */
    public function updt_pw($data)
    {
        $this->db->where('id',$data['id']);
        $query = $this->db->update($this->table_name,$data); 
        return $query;
    }
    
    /*
        @Description: Function for get User List (Customer)
        @Author     : Kashyap Padh
        @Input      : Fieldl list(id,name..), match value(id=id,..), condition(and,or),compare type(=,like),count,limit per page, starting row number
        @Output     : User details
        @Date       : 15-08-2013
    */
   
    public function getuser($getfields='', $match_values = '',$condition ='', $compare_type = '', $count = '',$num = '',$offset='',$orderby='')
    {
        $fields =  $getfields ? implode(',', $getfields) : '';
        $sql = 'SELECT ';
        
        $sql .= $fields ? $fields : '*';
        $sql .= ' FROM '.$this->table_name;
        $where='';
        
        if($match_values)
        {
            $keys = array_keys($match_values);
            $compare_type = $compare_type ? $compare_type : 'like';
            if($condition!='')
                $and_or=$condition;
            else 
                $and_or = ($compare_type == 'like') ? ' OR ' : ' AND '; 
          
            $where = 'WHERE ';
            switch ($compare_type)
            {
                case 'like':
                    $where .= $keys[0].' '.$compare_type .'"%'.$match_values[$keys[0]].'%" ';
                    break;

                case '=':
                default:
                    $where .= $keys[0].' '.$compare_type .'"'.$match_values[$keys[0]].'" ';
                    break;
            }
            $match_values = array_slice($match_values, 1);
            
            foreach($match_values as $key=>$value)
            {                
                $where .= $and_or.' '.$key.' ';
                switch ($compare_type)
                {
                    case 'like':
                        $where .= $compare_type .'"%'.$value.'%"';
                        break;
                    
                    case '=':
                    default:
                        $where .= $compare_type .'"'.$value.'"';
                        break;
                }
            }
        }
        $orderby = ($orderby !='')?' order by id desc ':'';
        if($offset=="" && $num=="")
            $sql .= ' '.$where.$orderby;
        elseif($offset=="")
            $sql .= ' '.$where.$orderby.' '.'limit '.$num;
        else
             $sql .= ' '.$where.$orderby.' '.'limit '.$offset .','.$num;
        
        $query = ($count) ? 'SELECT count(*) FROM '.$this->table_name.' '.$where.$orderby : $sql;        
        $query = $this->db->query($query);
        return $query->result_array();
    }    
     
    /*
        @Description: Function for get Customer(User) Lists
        @Author     : Kashyap Padh
        @Input      : search text,limit per page, starting row number, 
        @Output     : user list
        @Date       : 15-08-2013
    */
    public function getcustomer($src="",$num,$offset="")
    {
        if($src=="")
        {
            if($offset == "")
                $query = $this->db->query("select id,profile_img,first_name,last_name,email,facility,work_number,mobile from user where user_group = '1' order by id desc limit ".$num);
            else
                $query = $this->db->query("select id,profile_img,first_name,last_name,email,facility,work_number,mobile from user where user_group = '1' order by id desc limit ".$offset.",".$num);
        }
        else
        {
            if($offset == "")
                $query = $this->db->query("select id,profile_img,first_name,last_name,email,facility,work_number,mobile from user where user_group = '1' and (first_name like '%".$src."%' OR last_name like '%".$src."%' OR email like '%".$src."%' OR facility like '%".$src."%' OR mobile like '%".$src."%' OR work_number like '%".$src."%') order by id desc limit ".$num);
            else
                $query = $this->db->query("select id,profile_img,first_name,last_name,email,facility,work_number,mobile from user where user_group = '1' and (first_name like '%".$src."%' OR last_name like '%".$src."%' OR email like '%".$src."%' OR facility like '%".$src."%' OR mobile like '%".$src."%' OR work_number like '%".$src."%') order by id desc limit ".$offset.",".$num);
        }
        return $query->result_array();
    }

    /*
        @Description: Function is for Insert user details
        @Author     : Kashyap Padh
        @Input      : user details
        @Output     : Insert record into DB
        @Date       : 15-08-2013
    */
    function insert_user($data)
    {
        $this->db->insert($this->table_name,$data);	
	
    }

    /*
        @Description: Function is for update user details by Admin
        @Author     : Kashyap Padh
        @Input      : user details
        @Output     : -
        @Date: 15-08-2013
    */
    public function update_user($data)
    {
        $this->db->where('id',$data['id']);
        $query = $this->db->update($this->table_name,$data); 
    }
    
    /*
        @Description: Function for Delete Customer Profile By Admin
        @Author     : Kashyap Padh
        @Input      : user id
        @Output     : -
        @Date       : 15-08-2013
    */
    public function delete_user($id)
    {
        $this->db->where('id',$id);
        $this->db->delete($this->table_name);            
    }    
}