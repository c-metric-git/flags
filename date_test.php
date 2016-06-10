<?php

echo "Server Time Zone = ".$script_tz = date_default_timezone_get();

echo "<br>"; 

echo "Current Server Time  = ".$today = date("D M j G:i:S T Y");
exit;
$_SESSION['orders_inserted'] ='';

/*$_SESSION['orders_inserted'][] = 5;

$_SESSION['orders_inserted'][] = 8;

$_SESSION['orders_inserted'][] = 9;   */

/*echo "time =".$time = date('Y-m-d h:i:s',time());
echo "mktime =".$mktime = date('Y-m-d h:i:s',mktime()); 
*/ 
echo "Server Time Zone = ".$script_tz = date_default_timezone_get();

echo "<br>"; 

echo "Current Server Time  = ".$today = date("D M j G:i:S T Y");

$background_file = '1111.jpg';
$top_file = 'cd2131l.jpg';
$bottom_file = 'cd2131m_1.jpg';
$third_bottom_file = 'cd2131mm.png';

// code for resizing second image 
// Content type
/*header('Content-Type: image/jpeg');
// Get new sizes
list($width, $height) = getimagesize($bottom_file);
$newwidth = 244;
$newheight = 350;
// Load
$thumb = imagecreatetruecolor($newwidth, $newheight);
$source = imagecreatefromjpeg($bottom_file);
// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
// Output
imagejpeg($thumb);
*/
$back = imagecreatefromjpeg($background_file);  
$top = imagecreatefromjpeg($top_file);
$bottom = imagecreatefromjpeg($bottom_file);
$third_bottom = imagecreatefrompng($third_bottom_file);  
                    
imagecopy($back, $top, 0, 0, 0, 0, imagesx($top), imagesy($top)); 
imagepng($back, "merged_image7.jpg", 0);   // Save output image.

imagecopy($back, $bottom, 230, 80, 0, 0, imagesx($bottom), imagesy($bottom));
imagepng($back, "merged_image7.jpg", 0);  // Save output image.

imagecopy($back, $third_bottom, 75, 169, 0, 0, imagesx($third_bottom), imagesy($third_bottom));
imagepng($back, "merged_image7.jpg", 0);      
exit;

$back = imagecreatefromjpeg($background_file);  
$top = imagecreatefromjpeg($top_file);
$bottom = imagecreatefromjpeg($bottom_file);
$third_bottom = imagecreatefrompng($third_bottom_file);  
                    
imagecopy($back, $top, 0, 0, 0, 0, imagesx($top), imagesy($top)); 
imagepng($back, "merged_image6.jpg", 0);   // Save output image.

imagecopy($back, $bottom, 150, 80, 0, 0, imagesx($bottom), imagesy($bottom));
imagepng($back, "merged_image6.jpg", 0);  // Save output image.

imagecopy($back, $third_bottom, 60, 169, 0, 0, imagesx($third_bottom), imagesy($third_bottom));
imagepng($back, "merged_image6.jpg", 0);      
exit;

$top = imagecreatefromjpeg($top_file);
$bottom = imagecreatefromjpeg($bottom_file);
$third_bottom = imagecreatefrompng($third_bottom_file);  

imagecopy($top, $bottom, 150, 100, 0, 0, imagesx($bottom), imagesy($bottom));

// Save output image.
imagepng($top, "merged_image3.jpg", 0);
//$merged_image = imagecreatefromjpeg('merged_image1.jpg');  //330
imagecopy($top, $third_bottom, 70, 160, 0, 0, imagesx($third_bottom), imagesy($third_bottom));
imagepng($top, "merged_image3.jpg", 0);  

exit;
$top_file = 'ca45220.jpg';
$bottom_file = 'ca45228.jpg';
$third_bottom_file = 'cd2020mm_350_504.png';

$top = imagecreatefromjpeg($top_file);
$bottom = imagecreatefromjpeg($bottom_file);
$third_bottom = imagecreatefrompng($third_bottom_file);  

// Place "photo_to_paste.png" on "white_image.png"
imagecopy($top, $bottom, 0, 160, 0, 0, imagesx($bottom), imagesy($bottom));

// Save output image.
imagepng($top, "merged_image2.jpg", 0);

//$merged_image = imagecreatefromjpeg('merged_image1.jpg');  //330
imagecopy($top, $third_bottom, 0, 160, 0, 0, imagesx($third_bottom), imagesy($third_bottom));
imagepng($top, "merged_image2.jpg", 0);  
exit;

// get current width/height
/*list($top_width, $top_height) = getimagesize($top_file);
list($bottom_width, $bottom_height) = getimagesize($bottom_file);
*/
// compute new width/height
/*$new_width = ($top_width > $bottom_width) ? $top_width : $bottom_width;
$new_height = $top_height + $bottom_height;
*/// create new image and merge
/*$new = imagecreate($new_width, $new_height);
imagecopy($new, $top, 0, 0, 0, 0, $top_width, $top_height);
imagecopy($new, $bottom, 0, $top_height+1, 0, 0, $bottom_width, $bottom_height);
*/
// save to file
//imagejpeg($new, 'merged_image.jpg');


echo "Done";
// echo md5("deluxe/main.php?maincat=Flags%20by%20Brand");
 exit;
$summery = "akdfjkasbd aksdjashd asd testing ";
 
 $eol_character = "\r\n";
 $form = "orders@clownantics.com";
 $reply_to = $form;
 $headers = "";
 $headers .= "Content-type: text/html; charset=utf-8".$eol_character;
 $headers .= "From: <".$form.">".$eol_character;
 $headers .= "Reply-To: <".$reply_to.">".$eol_character;
 $to = "dhiraj@clownantics.com";
 $subject = "Update products in TD from Amazon with comparision";
 $message = $summery.$eol_character; 
 if(!mail($to, $subject, $message, $headers, "-f".$form)){
     echo "System could not send email.<br>";
 }
 else{
     echo "mail sent !";
 }
 exit; 
phpinfo();
echo $mysql_to =  date("Y-m-d H:i:s", strtotime(strtotime("Y-m-d") . "+30 days")); 

exit;



    $arr = array_count_values($_SESSION['orders_inserted']);

    $success_count = count($arr);



echo "Server Time Zone = ".$script_tz = date_default_timezone_get();

echo "<br>"; 

echo "Current Server Time  = ".$today = date("D M j G:i:S T Y");

echo "<br>";

/*echo $shipDate = strftime("%m/%d/%Y",substr('1283644800000',0,-3)); 

echo "<br>";

echo  "Today is ". $today = date("D M j G:i:S T Y"); */

date_default_timezone_set('America/New_York'); 

echo "<br>";

echo "US Time Zone = ".$script_tz = date_default_timezone_get();

echo "<br>";  

echo "Current US Time  = ".$today = date("D M j G:i:S T Y");   

?>

