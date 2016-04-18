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

<legend style="color:{$legend};">{$password_title}</legend>  
{if isset($password_notice)}
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>    
    {$password_notice}
</div> 
{elseif isset($password_warning)}
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>    
    {$password_warning}
</div> 
{else}
{/if}
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="change_password" value="1">
<input type="hidden" name="page" value="18">
<div class="control-group">
        <label for="password" class="control-label">{$password_new_password}</label>
        <div class="controls">  
          <input class="input-xxlarge span10" type="password" name="pass2" size="20">
		  <a href="#" class="example" rel="popover" data-content="{$help_new_password_info}" data-original-title="{$help_new_password_heading}" data-placement="left"><img src="{$theme_folder}/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
        </div>
</div>  
<div class="control-group">
        <label for="password_confirm" class="control-label">{$password_confirm_password}</label>
        <div class="controls">                  
          <input class="input-xxlarge span10" type="password" name="pass3" size="20">
		  <a href="#" class="example" rel="popover" data-content="{$help_confirm_password_info}" data-original-title="{$help_confirm_password_heading}" data-placement="left"><img src="{$theme_folder}/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
        </div>
</div>  
 <div class="control-group">
    <label for="" class="control-label"></label>
    <div class="controls">
      <input class="btn btn-primary" type="submit" value="{$password_button}">
    </div>
</div>
</form>