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

{if isset($offline_enabled)}
<legend style="color:{$legend};">{$offline_title}</legend>
<div class="well">{$offline_paragraph_one} {$offline_paragraph_two}</div>
<table class="table table-bordered">
<tr>
<td width="30%"><b>{$offline_send}</b></td>
<td width="70%">{$offline_location}<span class="pull-right"><a href="{$offline_location}" target="_blank" class="btn btn-primary btn-mini">{$offline_page_link}</a></span></td>
</tr>
</table>
{/if}