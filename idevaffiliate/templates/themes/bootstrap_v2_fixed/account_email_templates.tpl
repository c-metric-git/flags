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

{if isset($one_click_delivery)}
<legend style="color:{$legend};">{$menu_etemplates}</legend>
{section name=nr loop=$etemplates_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$marketing_group}:</b> {$etemplates_link_results[nr].etemplates_group_name}<br />
<b>{$etemplates_name}</b>: {$etemplates_link_results[nr].etemplates_link_name}<BR />
<b>{$marketing_target_url}</b>: <a href="{$etemplates_link_results[nr].etemplates_target_url}" target="_blank">{$etemplates_link_results[nr].etemplates_target_url}</a><BR /><BR />
<a href="{$base_url}/eview.php?id={$etemplates_link_results[nr].etemplates_link_id}" class="btn btn-mini btn-primary fancy-page">{$etemplates_view_link}</a><BR />
{$marketing_source_code}<BR />
<textarea rows="6" style="background-color:#f2f6ff;" class="input-block-level">{$etemplates_link_results[nr].etemplates_box_code}</textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_etemplates}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="28">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
         <select size="1" name="etemplates_picked">
        {section name=nr loop=$etemplates_results}
        <option value="{$etemplates_results[nr].etemplates_group_id}">{$etemplates_results[nr].etemplates_group_name}</option>
        {/section}
        </select>
        </div>
    </div>
    </div>
    <div class="span3">
       <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_etemplates}">
    </div>
</div>
</form>
{if isset($etemplates_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$etemplates_chosen_group_name}</font></h4>
{section name=nr loop=$etemplates_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$etemplates_name}</b>: {$etemplates_link_results[nr].etemplates_link_name}<BR />
<b>{$marketing_target_url}</b>: <a href="{$etemplates_link_results[nr].etemplates_target_url}" target="_blank">{$etemplates_link_results[nr].etemplates_target_url}</a><BR /><BR />
<a href="{$base_url}/eview.php?id={$etemplates_link_results[nr].etemplates_link_id}" class="btn btn-mini btn-primary fancy-page">{$etemplates_view_link}</a><BR />
{$marketing_source_code}<BR />
<textarea rows="6" class="input-block-level" style="background-color:#f2f6ff;">{$etemplates_link_results[nr].etemplates_box_code}</textarea>
</p>
{/section}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p><b>{$marketing_choose}</b><BR /><BR />{$marketing_notice}</p> *}
{/if}
{/if}