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

<table class="table table-bordered">
<tbody>
{if isset($training_materials)}
<tr><td><strong>{$menu_heading_training_materials}</strong></td></tr>
{if isset($training_videos)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=39">{$menu_videos}</a></td></tr>
{/if}
{if isset($pdf_training_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=25">{$menu_pdf_training}</a></td></tr>
{/if}
{if isset($custom_tracking_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank">{$menu_custom_manual}</a></td></tr>
{/if}
{/if}
<tr><td><strong>{$menu_heading_marketing}</strong></td></tr>
{if isset($coupon_codes_available)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=44">{$menu_coupon}</a></tr></td>
{/if}
{if isset($banner_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=7">{$menu_banners}</a></tr></td>
{/if}
{if isset($qr_codes_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=42">{$qr_code_title}</a></td></tr>
{/if}
{if isset($page_peel_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=37">{$menu_page_peels}</a></tr></td>
{/if}
{if isset($lightbox_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=38">{$menu_lightboxes}</a></tr></td>
{/if}
{if isset($textad_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=8">{$menu_text_ads}</a></tr></td>
{/if}
{if isset($htmlcount)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=23">{$menu_html_links}</a></tr></td>
{/if}
{if isset($textlink_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=9">{$menu_text_links}</a></tr></td>
{/if}
{if isset($email_links_available)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=10">{$menu_email_links}</a></tr></td>
{/if}
{if isset($etemplates_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=28">{$menu_etemplates}</a></tr></td>
{/if}
{if isset($offline_marketing)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=11">{$menu_offline}</a></tr></td>
{/if}
{if isset($second_tier)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=12">{$menu_tier_linking_code}</a></tr></td>
{/if}
{if isset($pdf_marketing_count)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=24">{$menu_pdf_marketing}</a></tr></td>
{/if}
{if isset($custom_tracking_enabled)}
<tr><td><strong>{$menu_heading_custom_links}</strong></td></tr>
{if isset($custom_links_enabled) || isset($sub_affiliates_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=36">{$menu_custom_reports}</a></td></tr>
{/if}
{if isset($custom_links_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=14">{$menu_keyword_links}</a></td></tr>
{/if}
{if isset($sub_affiliates_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=26">{$menu_subid_links}</a></td></tr>
{/if}
{if isset($alternate_keywords_enabled)}
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=35">{$menu_alteranate_links}</a></td></tr>
{/if}
{/if}
{if isset($commission_alert)}
<tr><td><strong>{$menu_heading_additional}</strong></td></tr>
<tr><td><i class="icon-circle-arrow-right"></i> <a href="account.php?page=15">{$menu_comalert}</a></td></tr>
{/if}
</tbody>
</table>