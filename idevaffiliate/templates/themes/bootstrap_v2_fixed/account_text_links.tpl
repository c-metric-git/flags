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
<legend style="color:{$legend};">{$menu_text_links}</legend>
{section name=nr loop=$textlink_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<strong>{$marketing_group}:</strong> {$textlink_link_results[nr].textlink_group_name}<BR />
<b>{$textlink_name}</b>: {$textlink_link_results[nr].textlink_link_text}<BR />
<b>{$marketing_target_url}</b>: <a href="{$textlink_link_results[nr].textlink_target_url}" target="_blank"{$tl_rel_values}>{$textlink_link_results[nr].textlink_target_url}</a><BR /><BR />
{$marketing_source_code}<BR />
<textarea rows="3" class="input-block-level" style="background-color:#f2f6ff;"><a href="{$textlink_link_results[nr].textlink_link_url}" target="_blank"{$tl_rel_values}>{$textlink_link_results[nr].textlink_link_text}</a></textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_text_links}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="9">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="textlinks_picked">
            {section name=nr loop=$textlink_results}
            <option value="{$textlink_results[nr].textlink_group_id}">{$textlink_results[nr].textlink_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
    </div>
    <div class="span3">
       <input class="btn btn-primary" type="submit" value="{$links_button} {$menu_text_links}">
    </div>
</div>
</form>
{if isset($textlink_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$textlink_chosen_group_name}</font></h4>
{section name=nr loop=$textlink_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$textlink_name}</b>: {$textlink_link_results[nr].textlink_link_text}<BR />
<b>{$marketing_target_url}</b>: <a href="{$textlink_link_results[nr].textlink_target_url}" target="_blank"{$tl_rel_values}>{$textlink_link_results[nr].textlink_target_url}</a><BR /><BR />
{$marketing_source_code}<BR />
<textarea rows="3" class="input-block-level" style="background-color:#f2f6ff;"><a href="{$textlink_link_results[nr].textlink_link_url}" target="_blank"{$tl_rel_values}>{$textlink_link_results[nr].textlink_link_text}</a></textarea>
</p>
{/section}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p><b>{$marketing_choose}</b><BR /><BR />{$marketing_notice}<BR /><BR /><BR /></p> *}
{/if}
{/if}