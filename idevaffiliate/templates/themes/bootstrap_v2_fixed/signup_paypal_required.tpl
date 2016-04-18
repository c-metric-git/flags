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

<legend style="color:{$legend};">{$signup_paypal_required_title}</legend>     
<input type="hidden" name="pp" value="1">   
<div class="control-group">
    <label for="{$signup_paypal_required_account}" class="control-label">{$signup_paypal_required_account}</label>
    <div class="controls">              
     <input type="text" class="input-xlarge span12" name="pp_account" value="{$pp_account}" />
     <span class="help-block">{$signup_paypal_required_notes}</span>
    </div>
</div>