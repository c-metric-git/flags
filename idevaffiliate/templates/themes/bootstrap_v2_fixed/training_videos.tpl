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

{if isset($uploaded_training_videos)}
<div class="clearfix"></div>
<h4 class="h3bg">Training Videos</h4>
<table class="table table-bordered">{$Uploaded_Video_Tutorials}</table>
{/if}

<div class="clearfix"></div>

{if isset($active_subscription)}

{foreach from=$video_results key=header item=table}

<h4 class="h3bg">{$header}</h4>
<table class="table table-bordered">
{$table}
</table>

{/foreach}

{else}

{if isset($videos_enabled)}
<h4 class="h3bg">General Affiliate Marketing</h4>
<table class="table table-bordered">{$Table_Rows_General_Affiliate_Marketing}</table>
{/if}

{/if}