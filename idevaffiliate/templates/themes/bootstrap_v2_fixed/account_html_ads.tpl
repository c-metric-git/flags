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
<legend style="color:{$legend};">{$menu_html_links}</legend>
{section name=nr loop=$html_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<strong>{$marketing_group}:</strong> {$html_link_results[nr].html_group_name}<BR />
<b>{$html_name}</b>: {$html_link_results[nr].html_link_name}<BR />
<b>{$marketing_target_url}</b>: <a href="{$html_link_results[nr].html_target_url}" target="_blank">{$html_link_results[nr].html_target_url}</a><BR /><BR />
<a href="{$base_url}/adview.php?id={$html_link_results[nr].html_link_id}" class="btn btn-mini btn-primary fancy-page">{$html_view_link}</a><BR />
{$marketing_source_code}<BR />
<textarea rows="6" style="background-color:#f2f6ff;" class="input-block-level">{$html_link_results[nr].html_link_url}</textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_html_links}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="23">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="html_picked">
            {section name=nr loop=$htmlad_results}
            <option value="{$htmlad_results[nr].htmlad_group_id}">{$htmlad_results[nr].htmlad_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_html_links}">
    </div>
</div>
</form>
{if isset($html_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$html_chosen_group_name}</font></h4>
{section name=nr loop=$html_link_results}
<p>
<b>{$html_name}</b>: {$html_link_results[nr].html_link_name}<BR />
<b>{$marketing_target_url}</b>: <a href="{$html_link_results[nr].html_target_url}" target="_blank">{$html_link_results[nr].html_target_url}</a><BR /><BR />
<a href="{$base_url}/adview.php?id={$html_link_results[nr].html_link_id}" class="btn btn-mini btn-primary fancy-page">{$html_view_link}</a><BR />
{$marketing_source_code}<BR />
<textarea rows="6" class="input-block-level" style="background-color:#f2f6ff;">{$html_link_results[nr].html_link_url}</textarea>
</p>
{/section}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p><b>{$marketing_choose}</b><BR /><BR />{$marketing_notice}<BR /><BR /><BR /></p> *}
{/if}
{/if}