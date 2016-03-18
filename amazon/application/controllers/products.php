<?php 
ini_set('max_execution_time', 0);
class Products extends CI_Controller
{
    public $api;
    public $login;
    public function __construct()
    {
        parent::__construct();
        $this->admin_session = $this->session->userdata('admin_session');
        (!$this->admin_session['active']=== TRUE) ? redirect('login') : '';        
        $this->api = new TDAPI();
        $this->login = $this->api->login();
    }

    /*
        @Description: Function to get all product records
        @Author     : Mannan Kagdi
        @Input      : 
        @Output     : Detail for update
        @Date       : 13-09-2013
    */
    public function index()
    {
        $this->data['src']=$this->input->post('search');
        {
            $this->data['flag'] = 1;
            ini_set('memory_limit', '-1');
            $this->load->library('pagination');
            $this->load->view('include/header');
            $this->load->view('include/left');
            $orderbyfield='';$orderby='';
            $config['base_url'] = base_url().'products/'.$orderbyfield.'/'.$orderby;
            $config['per_page'] = '2000';
            $config['next_tag_open'] = '<li >';
            $config['next_tag_close'] = '</li>';
            $config['next_link'] = 'Next';
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['prev_link'] = 'Previous';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="act" >';
            $config['cur_tag_close'] = '</li>';
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['uri_segment'] = 2;
            $uri_segment = $this->uri->segment(2);
            if($this->data['src']!="")
            {
                $fields = array('`sku`','`Amazon Title`','`ASIN`','`description`','`item-type`', '`item-price`','`quantity`','`@id`'); 
                $arr = array('sku' => $this->data['src'],'ASIN' => $this->data['src'],'description' => $this->data['src'],'`Amazon Title`' =>$this->data['src'],'`item-type`' =>$this->data['src'],'`item-price`' =>$this->data['src']);
                $this->data['result'] = $this->product_model->get_all_records($fields,$arr,'OR', '', '',$config['per_page'], $uri_segment,'Date Modified');
                $num_rows = count($this->product_model->get_all_records());
            }
            else
            {
                $fields = array('`sku`','`Amazon Title`','`ASIN`','`description`','`item-type`', '`item-price`','`quantity`','`@id`'); 
                $this->data['result'] = $this->product_model->get_all_records($fields,'','', '', '',$config['per_page'], $uri_segment,'Date Modified');
                $num_rows = count($this->product_model->get_all_records());
            }
                $config['total_rows'] = $num_rows;
                $this->pagination->initialize($config);
                $this->load->view('products/list',$this->data);
        }
    }
    /*
        @Description: Function to get all product records
        @Author     : Mannan Kagdi
        @Input      : 
        @Output     : Detail for update
        @Date       : 13-09-2013
    */
    public function ready_for_amazon()
    {
        $this->data['src']=$this->input->post('search');
        {
            $this->data['flag'] = 0;
            ini_set('memory_limit', '-1');
            $this->load->library('pagination');
            $this->load->view('include/header');
            $this->load->view('include/left');
            $orderbyfield='';$orderby='';
            $config['base_url'] = base_url().'products/'.$orderbyfield.'/'.$orderby;
            $config['per_page'] = '20';
            $config['next_tag_open'] = '<li >';
            $config['next_tag_close'] = '</li>';
            $config['next_link'] = 'Next';
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['prev_link'] = 'Previous';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="act" >';
            $config['cur_tag_close'] = '</li>';
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['uri_segment'] = 3;
            $uri_segment = $this->uri->segment(3);
            if($this->data['src']!="")
            {
                $fields = array('`sku`','`Amazon Title`','`ASIN`','`description`','`item-type`', '`item-price`','`quantity`','`@id`'); 
                $arr = array('sku' => $this->data['src'],'ASIN' => $this->data['src'],'description' => $this->data['src'],'`Amazon Title`' =>$this->data['src'],'`item-type`' =>$this->data['src'],'`item-price`' =>$this->data['src']);
                $this->data['result'] = $this->product_model->get_all_records_amazon($fields,$arr,'OR', '', '',$config['per_page'], $uri_segment,'Date Modified');
                $num_rows = count($this->product_model->get_all_records_amazon());
            }
            else
            {
                $fields = array('`sku`','`Amazon Title`','`ASIN`','`description`','`item-type`', '`item-price`','`quantity`','`@id`'); 
                $this->data['result'] = $this->product_model->get_all_records_amazon($fields,'','', '', '',$config['per_page'], $uri_segment,'Date Modified');
                $num_rows = count($this->product_model->get_all_records_amazon());
            }
            $config['total_rows'] = $num_rows;
            $this->pagination->initialize($config);
            $this->load->view('products/list',$this->data);
        }
    }
    public function fetchsku()
    {
        $offset=5*60*60;
        $start_time = gmdate(time());        
        $end_time = gmdate(time()+$offset);
        ini_set('max_execution_time', 0);
        $this->insert_live_records(0,'',$param='A5426');
        //$result = $this->api->GetUpdated("Amazon Entry",$start_time,$end_time);
        $this->index();
    }
    /*
        @Description: Function to synchronize all product records
        @Author     : Kashyap Padh
        @Input      : 
        @Output     : 
        @Date       : 19-08-2013
    */
    public function sync_live_db()
    {
//        $schema = $this->api->GetSchema('Amazon Entry');
//        $fields = array();
//        foreach($schema->Columns as $key=>$val)
//        {
//            $fields[] = '`'.$key .'` TEXT NULL ';
//        }        
//        $fields = implode(',', $fields);
//        $sql = "CREATE TABLE ready_for_amazon (".$fields.")";
//        mysql_query($sql);
//        exit;
        //$asin = array('B004ZLXA4I','B005VJI3UO');
        $ids = array();
        $this->product_model->delete_records($ids);
        $top = 1;
        for($i=0;$i<2;$i++)
        {
            $last_record = $this->insert_live_records($top,$last_record);            
            $top++;
        }
        echo "Success";
    }
    
    public function insert_live_records($top,$last_record = '',$param='')
    {        
//        $file_nm = ($_SERVER['DOCUMENT_ROOT'].'/amazon_api/amazon_cat.csv');
//        if(($handle = fopen($file_nm, "r")) !== FALSE)
//        {
//            $field_val = array();
//            while (($data = fgetcsv($handle, 1000,'	')) !== FALSE)
//            {
//                $data[0] = "'".trim($data[0])."'";                
//                if(isset($data[1]) && stripos($data[1],'/'))
//                {
//                    $a = explode('/', $data[0]);                    
//                    $data[1] = "'".trim($a[1])."'";
//                    $data[0] = '"'.$data[0].$a[0]."'";
//                }
//                else if(isset($data[1]) && stripos($data[1],'       '))
//                {
//                    $a = explode('       ', $data[0]);
//                    
//                    $data[1] = "'".trim($a[1])."'";
//                    $data[0] = '"'.$data[0].$a[0]."'";
//                }
//                else if(isset ($data[1]))
//                    $data[1] = "'".trim($data[1])."'";
//                
//                if(isset($data[2]))
//                    $data[2] = "'".trim($data[2])."'";
//                else
//                    $data[2] = "NULL";
//                $field_val[] = $data;
//            }
//            fclose($handle);            
//        }
//        
//        foreach($field_val as $key=>$values)
//        {
//            $sql = "INSERT INTO amazon_cat VALUES (".$values[0].",".$values[1].",".$values[2].")";            
//            mysql_query($sql);
//        }
//        exit;
        if($param)
            $result = $this->api->Query("SELECT * FROM [Amazon Entry] WHERE [is_visible] = '1'");
        else
        {
            //if($top == 1)
              //  $result = $this->api->Query("SELECT TOP 5 * FROM [Amazon Entry] WHERE [is_visible] = '1' ORDER BY [sku] DESC ");
            //else
            $result = $this->api->Query("SELECT * FROM [Amazon Entry] WHERE [sku] = 'A5426' ORDER BY [sku] DESC");
            //$result = $this->api->Query("SELECT * FROM [Amazon Entry] WHERE [is_visible] AND [isReadyForUpdate?] ORDER BY [sku] DESC");
        }
        
        $arr_fields = array();
        $arr_values = array();
        $new_arr = array();
        $count = count($result->Rows);
        
        for($i=0;$i<$count;$i++)
        {
            foreach($result->Rows[$i] as $key=>$val)
            {
                //if($key == '@id')
                    //continue;
                $key = '`'.$key.'`';
                switch (gettype($val))
                {
                    case 'boolean':
                    {
                        if($val)
                            $arr_fields [$key] = '"1"';
                        else
                            $arr_fields [$key] = '"0"';
                        break;
                    }
                    case 'object':
                    {
                        $arr_fields [$key]= '"'.$val->format( 'd-m-Y H:i:s').'"';
                        break;
                    }
                    default:
                    {
                        $arr_fields [$key] = '"'.(($val != '') ? htmlspecialchars(str_replace('"', '\"', $val)) : NULL).'"';
                        break;
                    }
                }
            }
            $last_record = $arr_fields['`sku`'];            
            $arr_values = array_values($arr_fields);            
            $arr_fields = array_keys($arr_fields);            
            $arr_fields = implode(',', $arr_fields);
            $arr_values = implode(',', $arr_values);
            echo $sql = 'INSERT INTO ready_for_amazon ('.$arr_fields.') VALUES ('.$arr_values.')';
            mysql_query($sql);
            //$this->product_model->insert_products($arr_values);
            unset($arr_fields);
            unset($arr_values);
        }
        return $last_record;
    }

    public function insert_live_records_categories()
    {        
        $result = $this->api->Query("SELECT * FROM [Amazon Category]");
        $arr_fields = array();
        $arr_values = array();
        $new_arr = array();
        $count = count($result->Rows);
        
        for($i=0;$i<$count;$i++)
        {
            foreach($result->Rows[$i] as $key=>$val)
            {                
                $key = '`'.$key.'`';
                switch (gettype($val))
                {
                    case 'boolean':
                    {
                        if($val)
                            $arr_fields [$key] = '"1"';
                        else
                            $arr_fields [$key] = '"0"';
                        break;
                    }
                    case 'object':
                    {
                        $arr_fields [$key]= '"'.$val->format( 'd-m-Y H:i:s').'"';
                        break;
                    }
                    default:
                    {
                        $arr_fields [$key] = '"'.(($val != '') ? htmlspecialchars ($val) : NULL).'"';
                        break;
                    }
                }
            }            
            $arr_values = array_values($arr_fields);            
            $arr_fields = array_keys($arr_fields);            
            $arr_fields = implode(',', $arr_fields);
            $arr_values = implode(',', $arr_values);
            $sql = 'INSERT INTO amazon_categories ('.$arr_fields.') VALUES ('.$arr_values.')';            
            mysql_query($sql);
            unset($arr_fields);
            unset($arr_values);
        }        
    }
    
    
    /*
    @Description: Display error list as per sku.
    @Author: Mayank Patel
    @Input: - SKU 
    @Output: - List of erro display
    @Date: 29-08-2013
    */
    public function get_error()
    {
        $error_sku = $this->uri->segment(3);
        $data['error_sku'] = $error_sku;
        $data['error_list'] = $this->product_model->get_error($error_sku);
        $this->load->view('products/error_list',$data);
    }

}