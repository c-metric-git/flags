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
<legend style="color:{$legend};">{$menu_banners}</legend>
{section name=nr loop=$banner_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$marketing_group}:</b> {$banner_link_results[nr].banner_group_name}<br />
<b>{$banners_size}</b>: {$banner_link_results[nr].banner_size_1} x {$banner_link_results[nr].banner_size_2}<br/>
<b>{$banners_description}</b>: {$banner_link_results[nr].banner_description}<br/>
<b>{$marketing_target_url}</b>: <a href="{$banner_link_results[nr].banner_target_url}" target="_blank">{$banner_link_results[nr].banner_target_url}</a><BR /><BR />
{$banner_link_results[nr].banner_display}<br/><br/>{$marketing_source_code}<br/>
<textarea rows="5" class="input-block-level" style="background-color:#f2f6ff;">{$banner_link_results[nr].banner_code}</textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_banners}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="7">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="banner_picked">
            {section name=nr loop=$banner_results}
            <option value="{$banner_results[nr].banner_group_id}">{$banner_results[nr].banner_group_name}</option>
            {/section}
          </select>
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_banners}">
    </div>
</div>
</form>
{if isset($banner_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$banner_chosen_group_name}</font></h4>
{section name=nr loop=$banner_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
    <b>{$banners_size}</b>: {$banner_link_results[nr].banner_size_1} x {$banner_link_results[nr].banner_size_2}<BR />
    <b>{$banners_description}</b>: {$banner_link_results[nr].banner_description}<BR />
    <b>{$marketing_target_url}</b>: <a href="{$banner_link_results[nr].banner_target_url}" target="_blank">{$banner_link_results[nr].banner_target_url}</a><BR /><BR />
    {$banner_link_results[nr].banner_display}<BR /><BR />
    {$marketing_source_code}<BR />
    <textarea rows="5" class="input-block-level" style="background-color:#f2f6ff;">{$banner_link_results[nr].banner_code}</textarea>
</p>
{/section}
{else}
{* turn this text on if you want *}
{* <h5>{$marketing_no_group}</h5> *}
{* {$marketing_choose}<BR /><BR /><font color="#CC0000">{$marketing_notice}</font> *}
{/if}
{/if}