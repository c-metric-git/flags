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

 <legend style="color:{$legend};">{$signup_canspam_title}</legend>     
 <div class="control-group">
    <label for="terms" class="control-label"></label>
    <div class="controls">       
        <textarea class="input-xxlarge span12" rows="5" name="canspam" readonly>{$canspam_t}</textarea>
        {if isset($canspam_required)}
        <p><input type="checkbox" name="canspam_accepted" value="1"{$canspam_checked}>&nbsp;{$signup_canspam_agree}</p>
        {/if}
    </div>
 </div>