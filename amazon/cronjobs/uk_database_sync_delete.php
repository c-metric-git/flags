<?php

include 'uk_config.php';

error_reporting(E_ALL);

ini_set('max_execution_time', 0);



$api = new TDAPI();

$login = $api->login();





$dataset = $api->GetSchema("Amazon Entry",array('sku','isVisibleOnAmazonUK?','recordHistory','amazonErrorReason','UK Amz ASIN','isReadyForDeletionToUkAmz?'));



$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$mysqli->query('DELETE FROM ready_for_deletion');



$result = $api->Query("SELECT [sku], [Product - SKU], [parentage], [isReadyForDeletionToUkAmz?] FROM [Amazon Entry] WHERE [isReadyForDeletionToUkAmz?]=TRUE ORDER BY [sku] DESC");



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

    $sql = 'INSERT INTO ready_for_deletion ('.$arr_fields.') VALUES ('.$arr_values.')';

//    if($arr_values['74']!='')

//    {

//        $res = $api->Query("SELECT * FROM [Amazon Entry] WHERE [parent-sku] = '".$arr_values[74]."'");

//        $arr_fields_var = array();

//        $arr_values_var = array();

//        $new_arr = array();

//        $cou = count($res->Rows);

//

//        for($j=0;$j<$cou;$j++)

//        {

//            foreach($res->Rows[$j] as $key=>$val)

//            {

//                $key = '`'.$key.'`';

//                switch (gettype($val))

//                {

//                    case 'boolean':

//                    {

//                        if($val)

//                            $arr_fields_var [$key] = '"1"';

//                        else

//                            $arr_fields_var [$key] = '"0"';

//                        break;

//                    }

//                    case 'object':

//                    {

//                        $arr_fields_var [$key]= '"'.$val->format( 'd-m-Y H:i:s').'"';

//                        break;

//                    }

//                    default:

//                    {

//                        $arr_fields_var [$key] = '"'.(($val != '') ? htmlspecialchars(str_replace('"', '\"', $val)) : NULL).'"';

//                        break;

//                    }

//                }

//            }

//            $last_record = $arr_fields_var['`sku`'];            

//            $arr_values_var = array_values($arr_fields_var);

//            $arr_fields_var = array_keys($arr_fields_var);            

//            $arr_fields_var = implode(',', $arr_fields_var);

//            $arr_values_var = implode(',', $arr_values_var);

//            $sql_var = 'INSERT INTO ready_for_deletion ('.$arr_fields_var.') VALUES ('.$arr_values_var.')';    

//            $mysqli->query($sql_var);

//            echo "Product variation successfully updated<br/>";

//            unset($arr_fields_var);

//            unset($arr_values_var);

//        }

//    }

    $mysqli->query($sql);

    echo "Products successfully added for deletion.<br/>";

    unset($arr_fields);

    unset($arr_values);

}