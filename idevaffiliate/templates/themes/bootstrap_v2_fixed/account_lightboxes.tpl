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
<legend style="color:{$legend};">{$menu_lightboxes}</legend>
<h4>{$lb_head_title}</h4>
<p style="border-bottom:1px solid #5e5e5e;">
{$lb_head_description}<BR /><BR />
{$lb_head_source_code}<BR />
<textarea rows="8" style="background-color:#f2f6ff;" class="input-block-level"><link rel="stylesheet" href="{$install_url}/templates/source/lightbox/css/jquery.fancybox.css" type="text/css" />
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/video.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/fancy-custom.js"></script></textarea>
{$lb_head_code_notes}<BR />
<a href="http://www.idevlibrary.com/docs/Lightboxes.pdf" target="_blank" class="btn btn-mini btn-primary">{$lb_head_tutorial}</a>
<br /><br />
</p>
{section name=nr loop=$lightbox_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<strong>{$marketing_group}:</strong> {$lightbox_link_results[nr].lightbox_group_name}<br />
<b>{$lb_body_title}</b>: {$lightbox_link_results[nr].lightbox_link_name}<BR />
<b>{$lb_body_description}</b>: {$lightbox_link_results[nr].lightbox_description}<BR />
<b>{$marketing_target_url}</b>: <a href="{$lightbox_link_results[nr].lightbox_target_url}" target="_blank">{$lightbox_link_results[nr].lightbox_target_url}</a><BR /><BR />
<a  href="media/lightboxes/{$lightbox_link_results[nr].lightbox_image}" title="<a href='{$lightbox_link_results[nr].lightbox_link}' target='_blank'>{$lightbox_link_text}</a>" class="fancy-image">
<img src="media/lightboxes/{$lightbox_link_results[nr].lightbox_thumbnail}" width="{$lightbox_link_results[nr].lightbox_thumb_width}" height="{$lightbox_link_results[nr].lightbox_thumb_height}" style="border:none;" /></a>
<BR />
{$lb_body_click}<BR /><BR />
{$lb_body_source_code}<BR />
<textarea rows="6" class="input-block-level" style="background-color:#f2f6ff;">{$lightbox_link_results[nr].lightbox_code}</textarea>
</p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_lightboxes}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="38">
<div class="row-fluid">
    <div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
         <select size="1" name="lb_picked">
            {section name=nr loop=$lb_results}
            <option value="{$lb_results[nr].lb_group_id}">{$lb_results[nr].lb_group_name}</option>
            {/section}
        </select>
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_lightboxes}">
    </div>
</div>
</form>
{if isset($lb_group_chosen)}
<h4>{$lb_head_title}</h4>
<p>
{$lb_head_description}<BR /><BR />
{$lb_head_source_code}<BR />
<textarea rows="7" style="background-color:#f2f6ff;" class="input-block-level"><link rel="stylesheet" href="{$install_url}/templates/source/lightbox/css/jquery.fancybox.css" type="text/css" />
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/video.js"></script>
<script type="text/javascript" src="{$install_url}/templates/source/lightbox/js/fancy-custom.js"></script></textarea>
{$lb_head_code_notes}<BR />
<a href="http://www.idevlibrary.com/docs/Lightboxes.pdf" target="_blank" class="btn btn-mini btn-primary">{$lb_head_tutorial}</a>
<br /><br />
</p>
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$lb_chosen_group_name}</font></h4>
{section name=nr loop=$lightbox_link_results}
<p style="border-bottom:1px solid #5e5e5e;">
<b>{$lb_body_title}</b>: {$lightbox_link_results[nr].lightbox_link_name}<BR />
<b>{$lb_body_description}</b>: {$lightbox_link_results[nr].lightbox_description}<BR />
<b>{$marketing_target_url}</b>: <a href="{$lightbox_link_results[nr].lightbox_target_url}" target="_blank">{$lightbox_link_results[nr].lightbox_target_url}</a><BR /><BR />
<a rev="{$lightbox_link_results[nr].lightbox_target_url}" href="media/lightboxes/{$lightbox_link_results[nr].lightbox_image}" title="<a href='{$lightbox_link_results[nr].lightbox_link}' target='_blank'>{$lightbox_link_text}</a>" class="fancy-image" alt="http://www.google.com">
<img src="media/lightboxes/{$lightbox_link_results[nr].lightbox_thumbnail}" width="{$lightbox_link_results[nr].lightbox_thumb_width}" height="{$lightbox_link_results[nr].lightbox_thumb_height}" href="{$lightbox_link_results[nr].lightbox_target_url}" style="border:none;" /></a>
<BR />
{$lb_body_click}<BR /><BR />
{$lb_body_source_code}<BR />
<textarea rows="6" style="background-color:#f2f6ff;" class="input-block-level">{$lightbox_link_results[nr].lightbox_code}</textarea>
</p>
{/section}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p>{$marketing_choose}</b><BR /><BR /><font color="#CC0000">{$marketing_notice}</font></p> *}
{/if}
{/if}