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

<legend style="color:{$legend};">{$signup_paypal_optional_title}</legend>     
<div class="control-group">
<label for="{$signup_paypal_optional_payme}" class="control-label">{$signup_paypal_optional_payme}</label>
<div class="controls">              
 <input type="checkbox" name="pp" value="1"{$postcheck}> ({$signup_paypal_optional_checkbox})
</div>
</div>
<div class="control-group">
<label for="{$signup_paypal_optional_account}" class="control-label">{$signup_paypal_optional_account}</label>
<div class="controls">              
 <input type="text" name="pp_account" size="25" value="{$pp_account}" class="input-large span12">
 <p>{$signup_paypal_optional_notes}</p>
</div>
</div>