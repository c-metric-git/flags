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

{if isset($sub_affiliates_enabled)}
<legend style="color:{$legend};">{$sub_tracking_title}</legend>
<div class="well" style="color:{$gb_text_color};">{$sub_tracking_info}</div>
<input style="background-color:#f2f6ff;" class="input-block-level" type="text" name="sub_link" value="{$sub_affiliate_linkurl}"><br />
{$sub_tracking_build}<br />
{$sub_tracking_example}: {$sub_affiliate_sample_link}<font color="#CC0000">&sub_id=123<br /><br />
<a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank" class="btn btn-primary btn-mini"><b>{$sub_tracking_tutorial}</b></a>
</p>
{/if}