<?PHP

/*
---------------------------------------------------------------------
	iDevAffiliate Version 7
	Copyright - iDevDirect.com LLC

	Website: http://www.idevdirect.com/
	Support: http://www.idevsupport.com/
	Email:   support@idevsupport.com
---------------------------------------------------------------------
	Code written in parts for easy modification should you desire.
---------------------------------------------------------------------
*/

$pageloc = null;
$ad = null;
$id = null;
$page = null;


$control_panel_session = true;
include_once("includes/control_panel.php");

if (isset($_SERVER['HTTP_REFERER'])) { $clickref = $_SERVER['HTTP_REFERER']; } else { $clickref = false; }

$ad = check_type('ad');
$id = check_type('id');
$page = check_type('page');

if (is_numeric($ad)) {

if (!is_numeric($id)) {
$convert_l2n = mysql_query("select id from idevaff_affiliates where username = '$id'");
$convert_l2n = mysql_fetch_array($convert_l2n);
$id = $convert_l2n['id']; }

if ($ad == 0) {
$adinfo = mysql_query("select title, content from idevaff_ads_default");
$getinfo = mysql_fetch_array($adinfo);
$idevtitle=quote_smart_decode_textads($getinfo['title']);
$idevtext=quote_smart_decode_textads($getinfo['content']);
$idevadurl = null;
} elseif ($ad > 0) {
$adinfo = mysql_query("select * from idevaff_ads where id = '$ad'");
$getinfo = mysql_fetch_array($adinfo);
$idevtitle=quote_smart_decode_textads($getinfo['title']);
$idevtext=quote_smart_decode_textads($getinfo['text']);
$adgroupinfo = mysql_query("select location from idevaff_groups where id = '" . $getinfo['grp'] . "'");
$getgroupinfo = mysql_fetch_array($adgroupinfo);
$idevadurl=$getgroupinfo['location'];
$idevadurl= quote_smart_decode_textads($idevadurl);
}

if ($idevadurl) {
$stitle = parse_url($idevadurl);
$stitle = $stitle['host'];
} else {
$stitle = parse_url($default_destination);
$stitle = $stitle['host'];
}

$get_default_colors = mysql_query("select * from idevaff_ads_default");
$color_result = mysql_fetch_array($get_default_colors);
$default_BoxWidth = $color_result['BoxWidth'];
$default_OutlineColor = $color_result['OutlineColor'];
$default_TitleTextColor = $color_result['TitleTextColor'];
$default_TitleTextBackgroundColor = $color_result['TitleTextBackgroundColor'];
$default_LinkColor = $color_result['LinkColor'];
$default_TextColor = $color_result['TextColor'];
$default_TextBackgroundColor = $color_result['TextBackgroundColor'];

if (isset($page)) { $pageloc = "_" . $page; } else { $pageloc = null; }

	$nodeId = $ad;
    $outputHtml = '';
    ob_start();

	
	


$redirect_location_link = $base_url . "/" . $filename . ".php?id=" . $id . $pageloc . "&curlad=" . $curl . "&set=2&link=" . $ad;
$redirect_location_box  = $base_url . "/" . $filename . ".php?id=" . $id . $pageloc . "&curlad=" . $curl . "&set=2&link=" . $ad . "&clickref=" . $clickref;
?>

<style type="text/css">

    .idevads-container_<?php echo $nodeId;?> {
		
        display: block;
        margin: 20px;
        position: relative;
        text-align: left;
        white-space: normal;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.2);
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        cursor: pointer;
		
    }
    .idevads-container_<?php echo $nodeId;?> .idevads-title_<?php echo $nodeId;?> {
        padding: 8px 14px;
        margin: 0;
        font-size: 13px;
        font-weight: normal;
        line-height: 18px;
        border-bottom: 1px solid #ccc;
        -webkit-border-radius: 5px 5px 0 0;
         -moz-border-radius: 5px 5px 0 0;
              border-radius: 5px 5px 0 0;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
		color: #333333;
    }
    .idevads-container_<?php echo $nodeId;?> .idevads-content_<?php echo $nodeId;?> {
        padding: 9px 14px;
        text-align: left;
        white-space: normal;
        -webkit-border-radius: 0 0 6px 6px;
        -moz-border-radius: 0 0 6px 6px;
        border-radius:  0 0 6px 6px;
    }
    .idevads-container_<?php echo $nodeId;?> .idevads-content_<?php echo $nodeId;?> p {
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 13px;
        line-height: 20px;
        margin: 0 0 10px 0;
        color:inherit !important;
    }


</style>

<div class="idevads-container_<?php echo $nodeId;?>" id="idevads_<?php echo $nodeId;?>" onclick="goIDevAdsSite<?php echo $nodeId;?>()">
<h3 class="idevads-title_<?php echo $nodeId;?>" id="idevads_title_<?php echo $nodeId;?>"><?PHP echo $idevtitle; ?></h3>
<div class="idevads-content_<?php echo $nodeId;?>" id="idevads_content_<?php echo $nodeId;?>">
<p><?PHP echo $idevtext; ?><br /><br /><a href="<?PHP echo $redirect_location_link; ?>"><?PHP echo $stitle; ?></a></p>
</div>
</div>

<script type="text/javascript">

function goIDevAdsSite<?php echo $nodeId;?>() {
    var idevadsUrl = '<?PHP echo $redirect_location_box; ?>';
    document.location.href = idevadsUrl;
}
	var idevadsId = '<?PHP echo $nodeId;?>';

/**
* Check if older JS code is calling script.
* If so, convert to default color scheme.
*/

if (typeof iDevAffiliate_TitleTextBackgroundColor === 'undefined') {
	document.getElementById('idevads_title_' + idevadsId).style.backgroundColor = '#<?PHP echo $default_TitleTextBackgroundColor; ?>';
	document.getElementById('idevads_title_' + idevadsId).style.color = '#<?PHP echo $default_TitleTextColor; ?>';
	document.getElementById('idevads_' + idevadsId).style.width = '<?PHP echo $default_BoxWidth; ?>' + "px";
	document.getElementById('idevads_' + idevadsId).style.borderColor = '#<?PHP echo $default_OutlineColor; ?>';
	document.getElementById('idevads_content_' + idevadsId).style.color = '#<?PHP echo $default_TextColor; ?>';
	document.getElementById('idevads_content_' + idevadsId).style.backgroundColor = '#<?PHP echo $default_TextBackgroundColor; ?>';
	var aTagList = document.getElementById('idevads_' + idevadsId).getElementsByTagName('A');
    if (aTagList.length > 0) {
    for (idx = 0; idx < aTagList.length; idx++ ) {
    aTagList[idx].style.color = '#<?PHP echo $default_LinkColor; ?>';
    } }

} else {

/**
* Use variables passed in new style JS code.
*/
    
    if (typeof iDevAffiliate_BoxWidth !== 'undefined') {
        document.getElementById('idevads_' + idevadsId).style.width = parseInt(iDevAffiliate_BoxWidth) + "px";
    }
        
    
    if (typeof iDevAffiliate_OutlineColor !== 'undefined')
        document.getElementById('idevads_' + idevadsId).style.borderColor = iDevAffiliate_OutlineColor;
    
    if (typeof iDevAffiliate_TitleTextColor !== 'undefined')
        document.getElementById('idevads_title_' + idevadsId).style.color = iDevAffiliate_TitleTextColor;
    
    if (typeof iDevAffiliate_LinkColor !== 'undefined') {
        var aTagList = document.getElementById('idevads_' + idevadsId).getElementsByTagName('A');
        if (aTagList.length > 0) {
            for (idx = 0; idx < aTagList.length; idx++ ) {
                aTagList[idx].style.color = iDevAffiliate_LinkColor;
            }
        }
    }
    if (typeof iDevAffiliate_TextColor !== 'undefined') {
        document.getElementById('idevads_content_' + idevadsId).style.color = iDevAffiliate_TextColor;
    }
    
    if (typeof iDevAffiliate_TextBackgroundColor !== 'undefined') {
        document.getElementById('idevads_content_' + idevadsId).style.backgroundColor = iDevAffiliate_TextBackgroundColor;
    }
    
    if (typeof iDevAffiliate_TitleTextBackgroundColor !== 'undefined') {
        document.getElementById('idevads_title_' + idevadsId).style.backgroundColor = iDevAffiliate_TitleTextBackgroundColor;
    }
}

    
</script>

<?php 
   
    $outputHtml = ob_get_contents();
    ob_end_clean();
    
    $outputHtml = str_replace(PHP_EOL, '', addslashes($outputHtml));
   
?>

document.write('<?php echo $outputHtml;?>');

<?PHP } ?>
