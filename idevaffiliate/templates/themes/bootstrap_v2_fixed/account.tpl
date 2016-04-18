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

{include file='file:header.tpl'}
<div class="container">
	<div class="container-fluid">      
		{if isset($re_accept)}
			<div class="row-fluid">
				{include file='file:tandc_re-accept.tpl'}
			</div>
        {else}
	        <div class="row-fluid">
				<div class="topHeadsec clearfix">
					<div class="span6">
						<div class="row-fluid">
							<div class="span6">
	        					<div class="square_boxes">
									<span>{$total_transactions}</span><br />{$account_total_transactions}
								</div>
        					</div>
							<div class="span6">
								<div class="square_boxes">
									<span>{$total_amount_earned}</span> <span class="usdTxt">{$total_amount_earned_currency}</span><br />{$account_earned_todate}
								</div>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								<div class="square_boxes">
									<span>{$tier_amount_earned}</span> {if isset($tier_amount_active)}<span class="usdTxt">{$total_amount_earned_currency}</span>{/if}<br />{$account_second_tier}
								</div>
							</div>
							<div class="span6">
								<div class="square_boxes">
									<span>{$standard_amount_earnings}</span> <span class="usdTxt">{$standard_amount_earnings_currency}</span><br />{$account_standard_earnings}<br />{if isset($insert_bonus)}<div class="label label-important">{$account_inc_bonus}</div>{/if}
								</div>
							</div>
						</div>
					</div>
					<div class="span6">
						{if $linking_code == 'available'}
		                    <span class="highlight_text">{$account_standard_linking_code}</span>
		                    <textarea rows="2" class="input-xxlarge span12">{$box_code}</textarea>
							<span class="highlight_text">{$progress_title} {$eligible_percent}% {$progress_complete}</span>
							<div class="progress progress-striped active">
								<div class="bar" style="width: {$eligible_percent}%"></div>
							</div>
							<span class="highlight_text">{$eligible_info}</span>
	                    {elseif $linking_code == 'pending_approval'}
		                    <p style="text-align:center;"><strong>{$account_not_approved}</strong></p>
	                    {elseif $linking_code == 'account_suspended'}
		                    <p style="text-align:center;">{$account_suspended}</p>
						{/if}
					</div>
				</div>
			</div>
	        <div class="row-fluid">
				<div class="span3">
{if isset($affiliate_library_access)}
<div align="center">
<form method="post" target="_blank" action="http://www.affiliatelibrary.com/welcome/index.php">
<input type="hidden" name="aff_fname" value="{$aff_fname}" />
<input type="hidden" name="aff_lname" value="{$aff_lname}" />
<input type="hidden" name="aff_email" value="{$aff_email}" />
<button class="btn btn-danger btn-block">{$aff_lib_button}</button>
</form>
</div>
{/if}
                    {include file='file:account_menu.tpl'}
				</div>
	            <div class="span9">
					<div class="row-fluid"> 
						{include file='file:menu_items.tpl'}
	                </div>   
	                {* {include_php file='file:includes/media/marketing.php'} *}                 
                	<div class="row-fluid page_account"> 
	                    {if isset($page_not_authorized)}
		                    {include file='file:account_pending_approval.tpl'}
	                    {elseif isset($affiliate_suspended)}
		                    {include file='file:account_suspended.tpl'}                
		                {else}                
		                    {if $internal_page == 1}
			                    {include file='file:account_general_stats.tpl'}
			                {elseif $internal_page == 2}
			                    {include file='file:account_tier_stats.tpl'}
		                    {elseif $internal_page == 3}
			                    {include file='file:account_payment_history.tpl'}
		                    {elseif $internal_page == 4}
			                    {if isset($sub_affiliates_enabled)}
				                     {include file='file:account_commission_list_subs.tpl'}
			                    {else}
				                    {include file='file:account_commission_list.tpl'}
			                    {/if}
		                    {elseif $internal_page == 5}
			                    {if isset($sub_affiliates_enabled)}
				                    {include file='file:account_recurring_commissions_subs.tpl'}
			                    {else}
				                    {include file='file:account_recurring_commissions.tpl'}
			                    {/if}
		                    {elseif $internal_page == 6}
			                    {include file='file:account_traffic_log.tpl'}
		                    {elseif $internal_page == 7}
			                    {include file='file:account_banners.tpl'}
		                    {elseif $internal_page == 8}
			                    {include file='file:account_text_ads.tpl'}
		                    {elseif $internal_page == 9}
			                    {include file='file:account_text_links.tpl'}
		                    {elseif $internal_page == 10}
			                    {include file='file:account_email_links.tpl'}
		                    {elseif $internal_page == 11}
			                    {include file='file:account_offline_marketing.tpl'}
		                    {elseif $internal_page == 12}
			                    {include file='file:account_tier_code.tpl'}
		                    {elseif $internal_page == 13}
			                    {include file='file:account_email_friends.tpl'}
		                    {elseif $internal_page == 14}
			                    {include file='file:account_keyword_links.tpl'}
		                    {elseif $internal_page == 15}
			                    {include file='file:account_commission_alert.tpl'}
		                    {elseif $internal_page == 16}
			                    {include file='file:account_commission_stats.tpl'}
		                    {elseif $internal_page == 17}
			                    {include file='file:account_edit.tpl'}
		                    {elseif $internal_page == 18}
			                    {include file='file:account_change_password.tpl'}
		                    {elseif $internal_page == 19}
			                    {include file='file:account_change_commission.tpl'}
		                    {elseif $internal_page == 21}
			                    {include file='file:account_faq.tpl'}
		                    {elseif $internal_page == 22}
			                    {include file='file:account_commission_details.tpl'}
		                    {elseif $internal_page == 23}
			                    {include file='file:account_html_ads.tpl'}
		                    {elseif $internal_page == 24}
			                    {include file='file:account_pdf_marketing.tpl'}
		                    {elseif $internal_page == 25}
			                    {include file='file:account_pdf_training.tpl'}
			                {elseif $internal_page == 26}
				                {include file='file:account_sub_affiliates.tpl'}
		                    {elseif $internal_page == 27}
			                    {include file='file:account_upload_logo.tpl'}
		                    {elseif $internal_page == 28}
			                    {include file='file:account_email_templates.tpl'}
		                    {elseif $internal_page == 29}
			                    {include file='file:account_sub_affiliates_test.tpl'}
		                    {elseif $internal_page == 30}
			                    {include file='file:custom/30.tpl'}
	        	            {elseif $internal_page == 31}
			                    {include file='file:custom/31.tpl'}
		                    {elseif $internal_page == 32}
			                    {include file='file:custom/32.tpl'}
		                    {elseif $internal_page == 33}
			                    {include file='file:custom/33.tpl'}
		                    {elseif $internal_page == 34}
			                    {include file='file:custom/34.tpl'}
		                    {elseif $internal_page == 35}
			                    {include file='file:account_alternate_page_links.tpl'}
		                    {elseif $internal_page == 36}
			                    {include file='file:account_custom_reports.tpl'}
		                    {elseif $internal_page == 37}
			                    {include file='file:account_page_peels.tpl'}
		                    {elseif $internal_page == 38}
			                    {include file='file:account_lightboxes.tpl'}
		                    {elseif $internal_page == 39}
			                    {include file='file:training_videos.tpl'}
		                    {elseif $internal_page == 40}
			                    {include file='file:account_direct_links.tpl'}
		                    {elseif $internal_page == 41}
			                    {include file='file:account_testimonials.tpl'}
							{elseif $internal_page == 42}
			                    {include file='file:account_qr_codes.tpl'}
							{elseif $internal_page == 43}
			                    {include file='file:account_upload_picture.tpl'}
							{elseif $internal_page == 44}
			                    {include file='file:account_coupon_codes.tpl'}
		                    {/if}
                    	{/if}                
					</div>
				</div>
	        </div>        
		{/if}
    </div>
</div>
{if isset($re_accept)}
	{include file='file:footer.tpl'}
{else}
	{include file='file:footer.tpl'}
{/if}