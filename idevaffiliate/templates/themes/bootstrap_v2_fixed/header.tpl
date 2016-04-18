{*
-------------------------------------------------------
	iDevAffiliate HTML Front-End Template
-------------------------------------------------------
	Template   : Bootstrap 2 - Fixed Width Responsive
-------------------------------------------------------
	Copyright  : iDevDirect.com LLC
	Website    : www.idevdirect.com
-------------------------------------------------------
*}

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$char_set}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
<title>{$sitename} - {$header_title}</title>
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/style.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/bootstrap/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" type="text/css" href="templates/source/lightbox/css/jquery.fancybox.css" />
<link rel="stylesheet" type="text/css" href="templates/source/lightbox/css/video-js.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/idevaff-res.css" />
<link href="templates/themes/bootstrap_v2_fixed/css/footable.core.css" rel="stylesheet" type="text/css" />
<link href="includes/video_source/skin/functional.css" rel="stylesheet" type="text/css" />
{literal}

<!--[if lt IE 9]>
<script src="templates/themes/bootstrap_v2_fixed/css/bootstrap/js/html5shiv.js"></script>
<![endif]-->
<script type="text/javascript" src="templates/source/lightbox/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="templates/source/lightbox/js/jquery.mousewheel-3.0.6.pack.js"></script>
<script type="text/javascript" src="templates/source/lightbox/js/video.js"></script>
<script type="text/javascript" src="templates/themes/bootstrap_v2_fixed/css/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="templates/source/lightbox/js/jquery.fancybox.js"></script>
<script type="text/javascript" src="templates/source/lightbox/js/jquery.fancybox-media.js"></script>
<script type="text/javascript" src="templates/source/lightbox/js/fancy-custom.js"></script>
<script type="text/javascript" src="templates/themes/bootstrap_v2_fixed/js/footable.js"></script>
<script type="text/javascript" src="includes/video_source/flowplayer.min.js"></script>
<script type="text/javascript">
	$(document).ready(function (){
		$(".tier").footable();
	});
</script>
{/literal}
</head>

<body style="background:{$background_color};color:{$text_color};">
<div id="wrap">
<div class="container">
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
    <div class="navbar alignCenter clearfix">
    {if isset($main_logo)}<a href="index.php" class="brand"><img style="border:none;" src="{$main_logo}" alt="{$sitename} - {$header_title}"></a>{/if}
		{if isset($social_enabled) && isset($social_location_header)}
		<div class="pull-right">
		{section name=nr loop=$social_icons}<a href="{$social_icons[nr].link}" target="_blank" class="pull-right" style="margin-top:15px; padding-right:5px;"><img src="{$social_icons[nr].image}" width="32" height="32" style="border:none;"></a>{/section}
		</div>
		{/if}
    </div>
</div>
</div>
<div class="row-fluid">
<div class="navbar clearfix">
<button data-target=".menu1" data-toggle="collapse" class="btn btn-navbar btn-block" type="button">
            <span class="navi-res">Menu</span>
          </button>
          
<div class="nav-collapse menu1 collapse">
    <ul class="nav mainNav">
        <li><a href="index.php"><span class="btn btn-primary btn-block">{$header_indexLink}</span></a></li>
        <li><a href="signup.php"><span class="btn btn-primary btn-block">{$header_signupLink}</span></a></li>
        <li><a href="account.php"><span class="btn btn-primary btn-block">{$header_accountLink}</span></a></li>
        <li><a href="contact.php" ><span class="btn btn-primary btn-block">{$header_emailLink}</span></a></li>
        {if isset($use_faq) && ($faq_location == 1)}
        <li><a href="faq.php"><span class="btn btn-primary btn-block">FAQ</span></a></li>
        {/if}
        {if isset($testimonials) && (isset($testimonials_active))}        
        <li><a href="testimonials.php"><span class="btn btn-primary btn-block">{$header_testimonials}</span></a></li>
        {/if}
		{if isset($affiliateUsername)}
        <li>
        <a href="logout.php"><span class="btn btn-primary btn-block">{$header_logout}</span></a>
        </li>
		{/if}
        
</ul>
<form method="POST" action="#" class="navbar-form pull-right lang-list-wrap">
<select size="1" name="idev_language" onchange='this.form.submit()' class="lang-list">
{php}
$get_lang_packs = mysql_query("select name from idevaff_language_packs where status = '1' ORDER BY name");
if (mysql_num_rows($get_lang_packs)) {
while ($pack = mysql_fetch_array($get_lang_packs)) {
$pack_value = $pack[name];
$pack_name = ucwords($pack[name]);
echo "<option value='$pack_value'";
if ($_SESSION['idev_language'] == $pack_value) {
echo " selected"; }
echo ">$pack_name</option>\n"; } }
{/php}
</select>
<input name="lang_token" value="{$language_token}" type="hidden" />
</form>
</div>
</div>
</div>
</div>
</div>