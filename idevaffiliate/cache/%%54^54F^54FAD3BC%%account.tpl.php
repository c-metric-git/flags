<?php /* Smarty version 2.6.14, created on 2016-04-09 11:51:52
         compiled from account.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="container">
	<div class="container-fluid">      
		<?php if (isset ( $this->_tpl_vars['re_accept'] )): ?>
			<div class="row-fluid">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:tandc_re-accept.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
        <?php else: ?>
	        <div class="row-fluid">
				<div class="topHeadsec clearfix">
					<div class="span6">
						<div class="row-fluid">
							<div class="span6">
	        					<div class="square_boxes">
									<span><?php echo $this->_tpl_vars['total_transactions']; ?>
</span><br /><?php echo $this->_tpl_vars['account_total_transactions']; ?>

								</div>
        					</div>
							<div class="span6">
								<div class="square_boxes">
									<span><?php echo $this->_tpl_vars['total_amount_earned']; ?>
</span> <span class="usdTxt"><?php echo $this->_tpl_vars['total_amount_earned_currency']; ?>
</span><br /><?php echo $this->_tpl_vars['account_earned_todate']; ?>

								</div>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								<div class="square_boxes">
									<span><?php echo $this->_tpl_vars['tier_amount_earned']; ?>
</span> <?php if (isset ( $this->_tpl_vars['tier_amount_active'] )): ?><span class="usdTxt"><?php echo $this->_tpl_vars['total_amount_earned_currency']; ?>
</span><?php endif; ?><br /><?php echo $this->_tpl_vars['account_second_tier']; ?>

								</div>
							</div>
							<div class="span6">
								<div class="square_boxes">
									<span><?php echo $this->_tpl_vars['standard_amount_earnings']; ?>
</span> <span class="usdTxt"><?php echo $this->_tpl_vars['standard_amount_earnings_currency']; ?>
</span><br /><?php echo $this->_tpl_vars['account_standard_earnings']; ?>
<br /><?php if (isset ( $this->_tpl_vars['insert_bonus'] )): ?><div class="label label-important"><?php echo $this->_tpl_vars['account_inc_bonus']; ?>
</div><?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="span6">
						<?php if ($this->_tpl_vars['linking_code'] == 'available'): ?>
		                    <span class="highlight_text"><?php echo $this->_tpl_vars['account_standard_linking_code']; ?>
</span>
		                    <textarea rows="2" class="input-xxlarge span12"><?php echo $this->_tpl_vars['box_code']; ?>
</textarea>
							<span class="highlight_text"><?php echo $this->_tpl_vars['progress_title']; ?>
 <?php echo $this->_tpl_vars['eligible_percent']; ?>
% <?php echo $this->_tpl_vars['progress_complete']; ?>
</span>
							<div class="progress progress-striped active">
								<div class="bar" style="width: <?php echo $this->_tpl_vars['eligible_percent']; ?>
%"></div>
							</div>
							<span class="highlight_text"><?php echo $this->_tpl_vars['eligible_info']; ?>
</span>
	                    <?php elseif ($this->_tpl_vars['linking_code'] == 'pending_approval'): ?>
		                    <p style="text-align:center;"><strong><?php echo $this->_tpl_vars['account_not_approved']; ?>
</strong></p>
	                    <?php elseif ($this->_tpl_vars['linking_code'] == 'account_suspended'): ?>
		                    <p style="text-align:center;"><?php echo $this->_tpl_vars['account_suspended']; ?>
</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
	        <div class="row-fluid">
				<div class="span3">
<?php if (isset ( $this->_tpl_vars['affiliate_library_access'] )): ?>
<div align="center">
<form method="post" target="_blank" action="http://www.affiliatelibrary.com/welcome/index.php">
<input type="hidden" name="aff_fname" value="<?php echo $this->_tpl_vars['aff_fname']; ?>
" />
<input type="hidden" name="aff_lname" value="<?php echo $this->_tpl_vars['aff_lname']; ?>
" />
<input type="hidden" name="aff_email" value="<?php echo $this->_tpl_vars['aff_email']; ?>
" />
<button class="btn btn-danger btn-block"><?php echo $this->_tpl_vars['aff_lib_button']; ?>
</button>
</form>
</div>
<?php endif; ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_menu.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
	            <div class="span9">
					<div class="row-fluid"> 
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:menu_items.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	                </div>   
	                                 
                	<div class="row-fluid page_account"> 
	                    <?php if (isset ( $this->_tpl_vars['page_not_authorized'] )): ?>
		                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_pending_approval.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	                    <?php elseif (isset ( $this->_tpl_vars['affiliate_suspended'] )): ?>
		                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_suspended.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>                
		                <?php else: ?>                
		                    <?php if ($this->_tpl_vars['internal_page'] == 1): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_general_stats.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                <?php elseif ($this->_tpl_vars['internal_page'] == 2): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_tier_stats.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 3): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_payment_history.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 4): ?>
			                    <?php if (isset ( $this->_tpl_vars['sub_affiliates_enabled'] )): ?>
				                     <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_commission_list_subs.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                    <?php else: ?>
				                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_commission_list.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                    <?php endif; ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 5): ?>
			                    <?php if (isset ( $this->_tpl_vars['sub_affiliates_enabled'] )): ?>
				                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_recurring_commissions_subs.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                    <?php else: ?>
				                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_recurring_commissions.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                    <?php endif; ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 6): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_traffic_log.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 7): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_banners.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 8): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_text_ads.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 9): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_text_links.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 10): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_email_links.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 11): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_offline_marketing.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 12): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_tier_code.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 13): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_email_friends.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 14): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_keyword_links.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 15): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_commission_alert.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 16): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_commission_stats.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 17): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_edit.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 18): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_change_password.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 19): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_change_commission.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 21): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_faq.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 22): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_commission_details.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 23): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_html_ads.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 24): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_pdf_marketing.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 25): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_pdf_training.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			                <?php elseif ($this->_tpl_vars['internal_page'] == 26): ?>
				                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_sub_affiliates.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 27): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_upload_logo.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 28): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_email_templates.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 29): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_sub_affiliates_test.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 30): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:custom/30.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	        	            <?php elseif ($this->_tpl_vars['internal_page'] == 31): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:custom/31.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 32): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:custom/32.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 33): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:custom/33.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 34): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:custom/34.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 35): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_alternate_page_links.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 36): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_custom_reports.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 37): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_page_peels.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 38): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_lightboxes.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 39): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:training_videos.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 40): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_direct_links.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php elseif ($this->_tpl_vars['internal_page'] == 41): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_testimonials.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php elseif ($this->_tpl_vars['internal_page'] == 42): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_qr_codes.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php elseif ($this->_tpl_vars['internal_page'] == 43): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_upload_picture.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							<?php elseif ($this->_tpl_vars['internal_page'] == 44): ?>
			                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_coupon_codes.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		                    <?php endif; ?>
                    	<?php endif; ?>                
					</div>
				</div>
	        </div>        
		<?php endif; ?>
    </div>
</div>
<?php if (isset ( $this->_tpl_vars['re_accept'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  endif; ?>