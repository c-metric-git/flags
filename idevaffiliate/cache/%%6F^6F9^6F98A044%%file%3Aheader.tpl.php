<?php /* Smarty version 2.6.14, created on 2016-04-09 11:19:37
         compiled from file:header.tpl */ ?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_tpl_vars['char_set']; ?>
">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
<title><?php echo $this->_tpl_vars['sitename']; ?>
 - <?php echo $this->_tpl_vars['header_title']; ?>
</title>
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/style.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/bootstrap/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" type="text/css" href="templates/source/lightbox/css/jquery.fancybox.css" />
<link rel="stylesheet" type="text/css" href="templates/source/lightbox/css/video-js.css" />
<link rel="stylesheet" type="text/css" href="templates/themes/bootstrap_v2_fixed/css/idevaff-res.css" />
<link href="templates/themes/bootstrap_v2_fixed/css/footable.core.css" rel="stylesheet" type="text/css" />
<link href="includes/video_source/skin/functional.css" rel="stylesheet" type="text/css" />
<?php echo '

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
'; ?>

</head>

<body style="background:<?php echo $this->_tpl_vars['background_color']; ?>
;color:<?php echo $this->_tpl_vars['text_color']; ?>
;">
<div id="wrap">
<div class="container">
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
    <div class="navbar alignCenter clearfix">
    <?php if (isset ( $this->_tpl_vars['main_logo'] )): ?><a href="index.php" class="brand"><img style="border:none;" src="<?php echo $this->_tpl_vars['main_logo']; ?>
" alt="<?php echo $this->_tpl_vars['sitename']; ?>
 - <?php echo $this->_tpl_vars['header_title']; ?>
"></a><?php endif; ?>
		<?php if (isset ( $this->_tpl_vars['social_enabled'] ) && isset ( $this->_tpl_vars['social_location_header'] )): ?>
		<div class="pull-right">
		<?php unset($this->_sections['nr']);
$this->_sections['nr']['name'] = 'nr';
$this->_sections['nr']['loop'] = is_array($_loop=$this->_tpl_vars['social_icons']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['nr']['show'] = true;
$this->_sections['nr']['max'] = $this->_sections['nr']['loop'];
$this->_sections['nr']['step'] = 1;
$this->_sections['nr']['start'] = $this->_sections['nr']['step'] > 0 ? 0 : $this->_sections['nr']['loop']-1;
if ($this->_sections['nr']['show']) {
    $this->_sections['nr']['total'] = $this->_sections['nr']['loop'];
    if ($this->_sections['nr']['total'] == 0)
        $this->_sections['nr']['show'] = false;
} else
    $this->_sections['nr']['total'] = 0;
if ($this->_sections['nr']['show']):

            for ($this->_sections['nr']['index'] = $this->_sections['nr']['start'], $this->_sections['nr']['iteration'] = 1;
                 $this->_sections['nr']['iteration'] <= $this->_sections['nr']['total'];
                 $this->_sections['nr']['index'] += $this->_sections['nr']['step'], $this->_sections['nr']['iteration']++):
$this->_sections['nr']['rownum'] = $this->_sections['nr']['iteration'];
$this->_sections['nr']['index_prev'] = $this->_sections['nr']['index'] - $this->_sections['nr']['step'];
$this->_sections['nr']['index_next'] = $this->_sections['nr']['index'] + $this->_sections['nr']['step'];
$this->_sections['nr']['first']      = ($this->_sections['nr']['iteration'] == 1);
$this->_sections['nr']['last']       = ($this->_sections['nr']['iteration'] == $this->_sections['nr']['total']);
?><a href="<?php echo $this->_tpl_vars['social_icons'][$this->_sections['nr']['index']]['link']; ?>
" target="_blank" class="pull-right" style="margin-top:15px; padding-right:5px;"><img src="<?php echo $this->_tpl_vars['social_icons'][$this->_sections['nr']['index']]['image']; ?>
" width="32" height="32" style="border:none;"></a><?php endfor; endif; ?>
		</div>
		<?php endif; ?>
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
        <li><a href="index.php"><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_indexLink']; ?>
</span></a></li>
        <li><a href="signup.php"><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_signupLink']; ?>
</span></a></li>
        <li><a href="account.php"><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_accountLink']; ?>
</span></a></li>
        <li><a href="contact.php" ><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_emailLink']; ?>
</span></a></li>
        <?php if (isset ( $this->_tpl_vars['use_faq'] ) && ( $this->_tpl_vars['faq_location'] == 1 )): ?>
        <li><a href="faq.php"><span class="btn btn-primary btn-block">FAQ</span></a></li>
        <?php endif; ?>
        <?php if (isset ( $this->_tpl_vars['testimonials'] ) && ( isset ( $this->_tpl_vars['testimonials_active'] ) )): ?>        
        <li><a href="testimonials.php"><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_testimonials']; ?>
</span></a></li>
        <?php endif; ?>
		<?php if (isset ( $this->_tpl_vars['affiliateUsername'] )): ?>
        <li>
        <a href="logout.php"><span class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_logout']; ?>
</span></a>
        </li>
		<?php endif; ?>
        
</ul>
<form method="POST" action="#" class="navbar-form pull-right lang-list-wrap">
<select size="1" name="idev_language" onchange='this.form.submit()' class="lang-list">
<?php 
$get_lang_packs = mysql_query("select name from idevaff_language_packs where status = '1' ORDER BY name");
if (mysql_num_rows($get_lang_packs)) {
while ($pack = mysql_fetch_array($get_lang_packs)) {
$pack_value = $pack[name];
$pack_name = ucwords($pack[name]);
echo "<option value='$pack_value'";
if ($_SESSION['idev_language'] == $pack_value) {
echo " selected"; }
echo ">$pack_name</option>\n"; } }
 ?>
</select>
<input name="lang_token" value="<?php echo $this->_tpl_vars['language_token']; ?>
" type="hidden" />
</form>
</div>
</div>
</div>
</div>
</div>