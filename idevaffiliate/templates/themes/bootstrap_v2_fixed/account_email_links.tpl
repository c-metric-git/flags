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

<legend style="color:{$legend};">{$email_title}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="10">
<div class="row-fluid">
<div class="span9">
    <div class="control-group">
        <label class="control-label" >{$email_group}</label>
        <div class="controls">                           
          <select size="1" name="email_picked" class="span12">
            {section name=nr loop=$email_results}
            <option value="{$email_results[nr].email_group_id}">{$email_results[nr].email_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
    </div>
    <div class="span3">
       <input class="btn btn-primary" type="submit" value="{$email_button}">
    </div>
</div>
</form>
{if isset($email_group_chosen)}
<h4 style="border-bottom:1px solid #5e5e5e;">{$email_group}: <font color="#CC0000">{$email_chosen_group_name}</font></h4>
<p style="border-bottom:1px solid #5e5e5e; padding:20px 0 20px 0;">{$email_ascii}<span class="pull-right">{$email_source}</span><br /><textarea rows="3" class="input-block-level" style="background-color:#f2f6ff;">{$email_chosen_url}</textarea></p>
<p style="border-bottom:1px solid #5e5e5e; padding:20px 0 20px 0;">{$email_html}<span class="pull-right">{$email_source}</span><br /><textarea rows="3" class="input-block-level" style="background-color:#f2f6ff;"><a href="{$email_chosen_url}{$rel_values}">{$email_chosen_group_name}</a></textarea></p>
<p><b>{$email_test}</b>: <a href="{$email_chosen_url}{$rel_values}" target="_blank">{$email_chosen_display_tag}</a></p>
<p>{$email_test_info}</p>
{else}
{* turn this text on if you want *}
{* <legend style="color:{$legend};">{$email_no_group}</legend> *}
{* <p><b>{$email_choose}</b><BR /><BR />{$email_notice}</p> *}
{/if}