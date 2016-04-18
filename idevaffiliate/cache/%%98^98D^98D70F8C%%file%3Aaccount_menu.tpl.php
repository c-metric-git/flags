<?php /* Smarty version 2.6.14, created on 2016-04-09 11:51:52
         compiled from file:account_menu.tpl */ ?>

<table class="table table-bordered">
<tbody>
<?php if (isset ( $this->_tpl_vars['training_materials'] )): ?>
<tr><td><strong><?php echo $this->_tpl_vars['menu_heading_training_materials']; ?>
</strong></td></tr>
<?php if (isset ( $this->_tpl_vars['training_videos'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=39"><?php echo $this->_tpl_vars['menu_videos']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['pdf_training_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=25"><?php echo $this->_tpl_vars['menu_pdf_training']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['custom_tracking_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank"><?php echo $this->_tpl_vars['menu_custom_manual']; ?>
</a></td></tr>
<?php endif;  endif; ?>
<tr><td><strong><?php echo $this->_tpl_vars['menu_heading_marketing']; ?>
</strong></td></tr>
<?php if (isset ( $this->_tpl_vars['coupon_codes_available'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=44"><?php echo $this->_tpl_vars['menu_coupon']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['banner_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=7"><?php echo $this->_tpl_vars['menu_banners']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['qr_codes_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=42"><?php echo $this->_tpl_vars['qr_code_title']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['page_peel_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=37"><?php echo $this->_tpl_vars['menu_page_peels']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['lightbox_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=38"><?php echo $this->_tpl_vars['menu_lightboxes']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['textad_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=8"><?php echo $this->_tpl_vars['menu_text_ads']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['htmlcount'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=23"><?php echo $this->_tpl_vars['menu_html_links']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['textlink_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=9"><?php echo $this->_tpl_vars['menu_text_links']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['email_links_available'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=10"><?php echo $this->_tpl_vars['menu_email_links']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['etemplates_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=28"><?php echo $this->_tpl_vars['menu_etemplates']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['offline_marketing'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=11"><?php echo $this->_tpl_vars['menu_offline']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['second_tier'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=12"><?php echo $this->_tpl_vars['menu_tier_linking_code']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['pdf_marketing_count'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=24"><?php echo $this->_tpl_vars['menu_pdf_marketing']; ?>
</a></tr></td>
<?php endif;  if (isset ( $this->_tpl_vars['custom_tracking_enabled'] )): ?>
<tr><td><strong><?php echo $this->_tpl_vars['menu_heading_custom_links']; ?>
</strong></td></tr>
<?php if (isset ( $this->_tpl_vars['custom_links_enabled'] ) || isset ( $this->_tpl_vars['sub_affiliates_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=36"><?php echo $this->_tpl_vars['menu_custom_reports']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['custom_links_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=14"><?php echo $this->_tpl_vars['menu_keyword_links']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['sub_affiliates_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=26"><?php echo $this->_tpl_vars['menu_subid_links']; ?>
</a></td></tr>
<?php endif;  if (isset ( $this->_tpl_vars['alternate_keywords_enabled'] )): ?>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=35"><?php echo $this->_tpl_vars['menu_alteranate_links']; ?>
</a></td></tr>
<?php endif;  endif;  if (isset ( $this->_tpl_vars['commission_alert'] )): ?>
<tr><td><strong><?php echo $this->_tpl_vars['menu_heading_additional']; ?>
</strong></td></tr>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=15"><?php echo $this->_tpl_vars['menu_comalert']; ?>
</a></td></tr>
<?php endif; ?>
</tbody>
</table>