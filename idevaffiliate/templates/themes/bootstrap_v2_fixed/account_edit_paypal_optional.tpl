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

<legend style="color:{$legend};">{$edit_paypal_optional_title}</legend>     
<div class="control-group">
<label for="{$edit_paypal_optional_payme}" class="control-label">{$edit_paypal_optional_payme}</label>
<div class="controls">              
 <input type="checkbox" name="pp" value="1"{$postcheck}> ({$edit_paypal_optional_checkbox})
</div>
</div>
<div class="control-group">
<label for="{$edit_paypal_optional_account}" class="control-label">{$edit_paypal_optional_account}</label>
<div class="controls">              
 <input type="text" name="pp_account" value="{$pp_account}" class="input-xlarge">
 <a href="http://www.paypal.com/" target="_blank"><img border="0" src="{$theme_folder}/images/paypal_small.gif" width="52" height="15"></a>
 <p>{$edit_paypal_optional_notes}</p>
</div>
</div>