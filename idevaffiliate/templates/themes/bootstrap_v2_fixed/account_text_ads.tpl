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
<legend style="color:{$legend};">{$menu_text_ads}</legend>
{section name=nr loop=$textad_link_results}
<p>
<strong>{$marketing_group}:</strong> {$textad_link_results[nr].textad_group_name}<BR />
<b>{$marketing_target_url}</b>: <a href="{$textad_link_results[nr].textad_target_url}" target="_blank">{$textad_link_results[nr].textad_target_url}</a><BR />
<script type="text/javascript"><!--
iDevAffiliate_BoxWidth = "{$BoxWidth}";
iDevAffiliate_OutlineColor = "#{$OutlineColor}";
iDevAffiliate_TitleTextColor = "#{$TitleTextColor}";
iDevAffiliate_TitleTextBackgroundColor = "#{$TitleTextBackgroundColor}";
iDevAffiliate_LinkColor = "#{$LinkColor}";
iDevAffiliate_TextColor = "#{$TextColor}";
iDevAffiliate_TextBackgroundColor = "#{$TextBackgroundColor}";
//-->
</script>
<script language="JavaScript" type="text/javascript" src="{$textad_link_results[nr].textad_full_url}"></script>
<BR />
{$marketing_source_code}
<BR />
<textarea rows="10" class="input-block-level" style="background-color:#f2f6ff;">
<script type="text/javascript"><!--
iDevAffiliate_BoxWidth = "{$BoxWidth}";
iDevAffiliate_OutlineColor = "#{$OutlineColor}";
iDevAffiliate_TitleTextColor = "#{$TitleTextColor}";
iDevAffiliate_TitleTextBackgroundColor = "#{$TitleTextBackgroundColor}";
iDevAffiliate_LinkColor = "#{$LinkColor}";
iDevAffiliate_TextColor = "#{$TextColor}";
iDevAffiliate_TextBackgroundColor = "#{$TextBackgroundColor}";
//-->
</script>
<script language="JavaScript" type="text/javascript" src="{$textad_link_results[nr].textad_full_url}"></script></textarea>
{$ad_info}
</p>
<br />
<p style="border-bottom:1px solid #5e5e5e;"></p>
{/section}
{else}
<legend style="color:{$legend};">{$menu_text_ads}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="8">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="textads_picked">
            {section name=nr loop=$textad_results}
            <option value="{$textad_results[nr].textad_group_id}">{$textad_results[nr].textad_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$marketing_button} {$menu_text_ads}">
    </div>
</div>
</form>
{if isset($textad_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$textad_chosen_group_name}</font></h4>
{section name=nr loop=$textad_link_results}
<p>
<b>{$marketing_target_url}:</b> <a href="{$textad_link_results[nr].textad_target_url}" target="_blank">{$textad_link_results[nr].textad_target_url}</a>
<br />
<script type="text/javascript"><!--
iDevAffiliate_BoxWidth = "{$BoxWidth}";
iDevAffiliate_OutlineColor = "#{$OutlineColor}";
iDevAffiliate_TitleTextColor = "#{$TitleTextColor}";
iDevAffiliate_TitleTextBackgroundColor = "#{$TitleTextBackgroundColor}";
iDevAffiliate_LinkColor = "#{$LinkColor}";
iDevAffiliate_TextColor = "#{$TextColor}";
iDevAffiliate_TextBackgroundColor = "#{$TextBackgroundColor}";
//-->
</script>
<script language="JavaScript" type="text/javascript" src="{$textad_link_results[nr].textad_full_url}"></script>
{$marketing_source_code}<br />
<textarea rows="10" class="input-block-level" style="background-color:#f2f6ff;">
<script type="text/javascript"><!--
iDevAffiliate_BoxWidth = "{$BoxWidth}";
iDevAffiliate_OutlineColor = "#{$OutlineColor}";
iDevAffiliate_TitleTextColor = "#{$TitleTextColor}";
iDevAffiliate_TitleTextBackgroundColor = "#{$TitleTextBackgroundColor}";
iDevAffiliate_LinkColor = "#{$LinkColor}";
iDevAffiliate_TextColor = "#{$TextColor}";
iDevAffiliate_TextBackgroundColor = "#{$TextBackgroundColor}";
//-->
</script>
<script language="JavaScript" type="text/javascript" src="{$textad_link_results[nr].textad_full_url}"></script>
</textarea>
</p>
<br />
<p style="border-bottom:1px solid #5e5e5e;"></p>
{/section}
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$marketing_no_group}</legend> *}
{* <p><b>{$marketing_choose}</b><BR /><BR />{$marketing_notice}</p> *}
{/if}
{/if}