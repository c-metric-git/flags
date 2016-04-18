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
<legend style="color:{$legend};">{$menu_page_peels}</legend>
{section name=nr loop=$peel_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<strong>{$marketing_group}:</strong> {$peel_link_results[nr].peel_group_name}<BR />
<b>{$peels_title}</b>: {$peel_link_results[nr].peel_link_name}<BR />
<b>{$peels_description}</b>: {$peel_link_results[nr].peel_description}<BR />
<b>{$marketing_target_url}</b>: <a href="{$peel_link_results[nr].peel_target_url}" target="_blank">{$peel_link_results[nr].peel_target_url}</a><BR /><BR />
<a href="{$peel_link_results[nr].peel_sample_url}" title="{$peels_title}: {$peel_link_results[nr].peel_link_name}" class="btn btn-mini btn-primary fancy-page">{$peels_view}</a>
<BR />{$marketing_source_code}<BR />
<textarea rows="4" style="background-color:#f2f6ff;" class="input-block-level"><script src="{$peel_link_results[nr].peel_source_location}/jquery-2.0.3.min.js"></script>
<script src="{$peel_link_results[nr].peel_source_location}/jquery.peelback.js"></script>
<script src="{$peel_link_results[nr].peel_link_url}"></script></textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_page_peels}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="37">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="peels_picked">
            {section name=nr loop=$peels_results}
            <option value="{$peels_results[nr].peels_group_id}">{$peels_results[nr].peels_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_page_peels}"/>
    </div>
</div>
</form>
{if isset($peels_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$peels_chosen_group_name}</font></h4>
{section name=nr loop=$peels_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$peels_title}</b>: {$peels_link_results[nr].peels_link_name}<BR />
<b>{$peels_description}</b>: {$peels_link_results[nr].peels_description}<BR />
<b>{$marketing_target_url}</b>: <a href="{$peels_link_results[nr].peels_target_url}" target="_blank">{$peels_link_results[nr].peels_target_url}</a><BR /><BR />
<a href="{$peels_link_results[nr].peels_sample_url}" title="{$peels_title}: {$peels_link_results[nr].peels_link_name}" class="btn btn-mini btn-primary fancy-page">{$peels_view}</a>
<br />{$marketing_source_code}<BR />
<textarea rows="4" style="background-color:#f2f6ff;" class="input-block-level"><script src="{$peels_link_results[nr].peels_source_location}/jquery-2.0.3.min.js"></script>
<script src="{$peels_link_results[nr].peels_source_location}/jquery.peelback.js"></script>
<script src="{$peels_link_results[nr].peels_link_url}"></script></textarea>
</p>
{/section}
{$peels_link_results[nr].peels_peelback_url}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p><b>{$marketing_choose}</b><BR /><BR /><font color="#CC0000">{$marketing_notice}</font></p> *}
{/if}
{/if}