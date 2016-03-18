<?
echo $_SERVER['DOCUMENT_ROOT'];
echo $_SERVER['HTTP_HOST'];
print_R($_SERVER);
exit;
if($_POST['submit']) {
$get_content = file_get_contents('http://dev.fundanyone.com/wp-content/uploads/2016/03/Tulips-3.jpg');
echo $file_name =  basename('http://dev.fundanyone.com/wp-content/uploads/2016/03/Tulips-3.jpg');
print_R($get_content);
echo $temp = tmpfile();
fwrite($temp, $get_content);
fclose($temp); 
 
//print_R($_FILES);
$_FILES['fileToUpload'] = array('name' => $file_name,'type' => "image/jpeg",'tmp_name' =>$temp);
echo '<pre>';
print_R($_FILES);
exit;
}
//exit;

/*$string = "4th of July, Baby & Kids, Memorial Day, Fall, Valentine's Day";
$pr_season_arr = explode(",",$string);
$holiday_arr = array('4th of July',"Valentine's Day",'Memorial Day');  
$occassion_arr = array('Anniversary',"Baby & Kids",'Cemetery'); 
foreach($pr_season_arr as $pr_season) {
                                 if(in_array(trim($pr_season),$holiday_arr)) {
                                      $pr_holiday_list[] = trim($pr_season); 
                                 }
                                 elseif(in_array(trim($pr_season),$occassion_arr)) {     
                                    $pr_occassion_list[] = trim($pr_season); 
                                 } 
                                 else {
                                     $pr_season_list[] = trim($pr_season);  
                                 }  
                            }  
  echo '<pre>';  
  echo "holiday list =";print_r($pr_holiday_list);
  echo "occasion list =";print_R($pr_occassion_list);
  echo "season list =";print_R($pr_season_list);                        
                            
exit;                            
function pc_permute($items, $perms = array()) {
    if (empty($items)) { 
        echo join(' ', $perms) . "<br />";
    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             pc_permute($newitems, $newperms);
         }
    }
}

$arr = array('Multi Themes', 'Multi Subthemes', 'Multi Seasons', 'Multi Brand', 'Filter Size', 'Filter Type');

pc_permute($arr);
*/
?>

<!DOCTYPE html>
<html>
<body>

<form action="combinations.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload" value="http://dev.fundanyone.com/wp-content/uploads/2016/03/Tulips-3.jpg">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
