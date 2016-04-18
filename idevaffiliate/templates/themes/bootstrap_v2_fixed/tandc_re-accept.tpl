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

<p style="margin-top:15px">
    <h4>{$tc_reaccept_title}</h4>
    {$tc_reaccept_sub_title}
</p>
<br />
<form method="post" value="account.php">
    <textarea rows="10" name="terms" class="input-block-level" readonly>{$terms_t}</textarea>
    <input type="hidden" name="terms_accepted" value="true">
    <input type="submit" class="btn btn-primary" name="Re-Accept Terms and Conditions" value="{$tc_reaccept_button}">
</form>