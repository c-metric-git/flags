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

<legend style="color:{$legend};">{$qr_code_title}</legend>
<div class="row-fluid">
<div class="span12">
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="42">
{if isset($qr_groups_available)}
    <div class="control-group">
        <label class="control-label" >{$marketing_group_title}</label>
        <div class="controls">                           
          <select size="1" name="qr_picked">
            {section name=nr loop=$qr_results}
            <option value="{$qr_results[nr].qr_group_id}">{$qr_results[nr].qr_group_name}</option>
            {/section}
            </select>
        </div>
    </div>
{/if}
    <div class="control-group">
        <label class="control-label" >{$qr_code_size}</label>
        <div class="controls">                           
          <select size="1" class="input-medium" name="qr_code_size">
            <option value="58"{if ($size_only) == '58'} selected{/if}>58 X 58</option>
			<option value="87"{if ($size_only) == '87'} selected{/if}>87 X 87</option>
			<option value="116"{if ($size_only) == '116'} selected{/if}>116 X 116</option>
			<option value="174"{if ($size_only) == '174'} selected{/if}>174 X 174</option>
			<option value="232"{if ($size_only) == '232'} selected{/if}>232 X 232</option>
			<option value="290"{if ($size_only) == '290'} selected{/if}>290 X 290</option>
          </select> <input class="btn btn-primary" type="submit" value="{$qr_code_button}">
        </div>
    </div>
	</form>
	
    </div>
</div>
{if isset($qr_group_chosen)}
<div class="row-fluid">
<div class="span12">
<h4 style="border-bottom:1px solid #5e5e5e;">{$marketing_group_title}: <font color="#CC0000">{$qr_chosen_group_name}</font></h4>
</div>
</div>
<div class="row-fluid">
<div class="span12">
<b>{$marketing_target_url}</b>: <a href="{$target_url}" target="_blank">{$target_url}</a>
</div>
</div>
<div class="row-fluid">
<div class="span4">
{$qr_image}
</div>
	<div class="span8">
	<strong>{$qr_code_offline_title}</strong><br />{$qr_code_offline_content1}<br />{$qr_code_offline_content2}<br /><br />
	<strong>{$qr_code_online_title}</strong><br />{$qr_code_online_content}<br />
	<textarea class="input-block-level" style="background-color:#f2f6ff;" rows="6"><a href="{$url_only}" target="_blank"><img src="{$base_url}{$image_only}" style="border:none;" height="{$size_only}" width="{$size_only}" /></a>
	</textarea>
	</div>
</div>
{/if}