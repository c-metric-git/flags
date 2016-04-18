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

<legend style="color:{$legend};">{$edit_paypal_required_account}</legend>     
<input type="hidden" name="pp" value="1">   
<div class="control-group">
    <label for="{$edit_paypal_required_account}" class="control-label">{$edit_paypal_required_account}</label>
    <div class="controls">              
     <input type="text" class="input-xlarge" name="pp_account" value="{$pp_account}" />
     <a href="http://www.paypal.com/" target="_blank"><img border="0" src="{$theme_folder}/images/paypal_small.gif" width="52" height="15"></a>
     <span class="help-block">{$edit_paypal_required_notes}</span>
    </div>
</div>