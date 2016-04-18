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



<legend style="color:{$legend};">{$custom_fields_title}</legend>  

{section name=nr loop=$custom_input_results}
<div class="control-group">
<label class="control-label">{$custom_input_results[nr].custom_title}</label>
<div class="controls"> <input type="text" name="{$custom_input_results[nr].custom_name}" class="input-xxlarge span12" value="{$custom_input_results[nr].custom_value}" /></div>
</div>
{/section}


