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

 <legend style="color:{$legend};">{$signup_terms_title}</legend>     
 <div class="control-group">
    <label for="terms" class="control-label"><img border="0" src="{$theme_folder}/images/cp_terms.gif" width="32" height="32"></label>
    <div class="controls">
        <textarea class="input-xxlarge span12" name="terms" rows="10" readonly>{$terms_t}</textarea>
        {if isset($terms_required)}
        <p><input type="checkbox" name="accepted" value="1"{$terms_checked}>&nbsp;{$signup_terms_agree}</p>
        {/if}
    </div>
 </div>