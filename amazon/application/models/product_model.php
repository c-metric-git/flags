<?php
class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'amazon_entries';
        $this->ready_table_name = 'ready_for_amazon';        
    }
    
    /*
        @Description : Function to get records from amazon_entries table
        @Author      : Kashyap Padh
        @Input       : Fieldl list(id,name..), match value(id=id,..), condition(and,or),compare type(=,like),count,limit per page, starting row number
        @Output      : Amazon entries
        @Date        : 21-08-2013
    */
    public function get_all_records($getfields='', $match_values = '',$condition ='', $compare_type = '', $count = '',$num = '',$offset='',$orderby='')
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
        
        $orderby = ($orderby !='')?' order by `'.$orderby.'` desc ':'';
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
        @Description : Function to delete records from amazon_entries table
        @Author      : Kashyap Padh
        @Input       : record id
        @Output      : Amazon entries
        @Date        : 21-08-2013
    */
    
    public function delete_records($ids)
    {
        if($ids)
        {
            $ids[0] = $ids[0] ? "'".$ids[0] : '';
            $ids[count($ids)-1] = $ids[count($ids)-1] ? $ids[count($ids)-1]."'" : '';
            $ids = implode("','", $ids);
            $sql = "DELETE FROM ".$this->table_name." WHERE ASIN IN  (".$ids.")";            
        }
        else
            $sql = "DELETE FROM ".$this->table_name;
        $this->db->query($sql);
    }    
    
    
    /*
    @Description: Function for getting error detail
    @Author: Mayank Patel
    @Input: sku
    @Output: Get error list and display into colorbox
    @Date: 29-8-2013
    */
    public function get_error($sku)
    {
        $all_users = array();
        $where = "SELECT ResultMessageCode,ResultDescription,SKU from amazon_error_reporting WHERE SKU = '".$sku."'";
        $query=$this->db->query($where);		
        $all_users = $query->result_array();	 	
        return $all_users;
    }
    
    /*
        @Description    : Function for getting error detail
        @Author         : Kashyap Padh
        @Input          : product data
        @Output         : inserted id
        @Date           : 11-09-2013
    */
    public function get_all_records_amazon($getfields='', $match_values = '',$condition ='', $compare_type = '', $count = '',$num = '',$offset='',$orderby='')
    {
        $fields =  $getfields ? implode(',', $getfields) : '';
        $sql = 'SELECT ';
        
        $sql .= $fields ? $fields : '*';
        $sql .= ' FROM '.$this->ready_table_name;
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
        
        $orderby = ($orderby !='')?' order by `'.$orderby.'` desc ':'';
        if($offset=="" && $num=="")
            $sql .= ' '.$where.$orderby;
        elseif($offset=="")
            $sql .= ' '.$where.$orderby.' '.'limit '.$num;
        else
             $sql .= ' '.$where.$orderby.' '.'limit '.$offset .','.$num;
        
        $query = ($count) ? 'SELECT count(*) FROM '.$this->ready_table_name.' '.$where.$orderby : $sql;
        $query = $this->db->query($query);
        
        return $query->result_array();
    }
    
}