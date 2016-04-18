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

{if isset($custom_tracking_enabled)}
<legend style="color:{$legend};">{$cr_title}</legend>
<form method="POST" action="account.php">
<input type="hidden" name="custom_report" value="1">
<input type="hidden" name="page" value="36">
<div class="row-fluid">


<div class="span4">
<p>
<select size='1' name='tid1' class='input-xlarge span12'>
{if isset($tid1_set)}
<option value='none'>TID1: {$cr_select}</option>
{section name=nr loop=$get1_results}
<option value='{$get1_results[nr].tid1_value}'>{$get1_results[nr].tid1_value}</option>
{/section}
{else}
<option value='none'>TID1: {$cr_none}</option>
{/if}
</select>
</p>

<p>
<select size='1' name='tid2' class='input-xlarge span12'>
{if isset($tid2_set)}
<option value='none'>TID2: {$cr_select}</option>
{section name=nr loop=$get2_results}
<option value='{$get2_results[nr].tid2_value}'>{$get2_results[nr].tid2_value}</option>
{/section}
{else}
<option value='none'>TID2: {$cr_none}</option>
{/if}
</select>
</p>

</div>


<div class="span4">

<p>
<select size='1' name='tid3' class='input-xlarge span12'>
{if isset($tid3_set)}
<option value='none'>TID3: {$cr_select}</option>
{section name=nr loop=$get3_results}
<option value='{$get3_results[nr].tid3_value}'>{$get3_results[nr].tid3_value}</option>
{/section}
{else}
<option value='none'>TID3: {$cr_none}</option>
{/if}
</select>
</p>

<p>
<select size='1' name='tid4' class='input-xlarge span12'>
{if isset($tid4_set)}
<option value='none'>TID4: {$cr_select}</option>
{section name=nr loop=$get4_results}
<option value='{$get4_results[nr].tid4_value}'>{$get4_results[nr].tid4_value}</option>
{/section}
{else}
<option value='none'>TID4: {$cr_none}</option>
{/if}
</select>
</p>

</div>
<div class="span4">


<input class="btn btn-primary" type="submit" value="{$cr_button}">
</div>
</div>
</form>
{if isset($custom_logs_exist)}
<div class="row-fluid">
    <div class="span12">
	<legend style="color:{$legend};">{$cr_title}<span class="pull-right">{$report_total_links} {$cr_unique}</span></legend>
    </div>
</div>
<table class="table table-bordered">
<tr>
<td width="60%"><strong>{$cr_used}</strong></td>
<td width="15%"><strong>{$cr_found}</strong></td>
<td width="25%"><strong>{$cr_detailed}</strong></td>
</tr>
{section name=nr loop=$report_results}
<form method="POST" action="export/export.php">
<input type="hidden" name="export" value="1">
<input type="hidden" name="custom_links_report" value="1">
<input type="hidden" name="linkid" value="{$affiliate_id}">
<input type="hidden" name="tid1" value="{$report_results[nr].report_tid1}">
<input type="hidden" name="tid2" value="{$report_results[nr].report_tid2}">
<input type="hidden" name="tid3" value="{$report_results[nr].report_tid3}">
<input type="hidden" name="tid4" value="{$report_results[nr].report_tid4}">
<tr>
<td width="60%">{$report_results[nr].report_keywords}</td>
<td width="15%">{$report_results[nr].report_links} {$cr_times}</td>
<td width="25%"><input type="submit" value="{$cr_export}" class="btn btn-mini btn-primary"></td>
</tr>
</form>
{/section}
</table>
{elseif isset($no_results_found)}
<div class="row-fluid">
<legend style="color:{$legend};">{$cr_title}</legend>
<p><strong>{$cr_no_results}</strong><BR />{$cr_no_results_info}<br /><BR /></p>
</div>
{/if}
{/if}