{if isset($display_testimonial_success)}
<legend style="color:{$legend};">{$testi_title}</legend>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>   
    <h4>{$testimonial_success_title}</h4>
    {$testimonial_success_message}
</div> 
{elseif isset($display_testimonial_errors)}
<legend style="color:{$legend};">{$testi_title}</legend>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>  
    <h4>{$testimonial_error_title}</h4>  
    {$testimonial_error_list}
</div> 
{else}
{/if}
{if isset($testimonials) && !isset($display_testimonial_success)}
<legend style="color:{$legend};">{$testi_title}</legend>
<form action="account.php" method="post" class="form-horizontal">
<input type="hidden" name="create_testimonial" value="1">
<input type="hidden" name="page" value="41">
<div class="control-group">
    <p>{$testi_description}</p>
</div>
<div class="control-group">
    <label class="control-label" >{$testi_name}</label>
    <div class="controls">  
        <input  class="input-block-level" type="text" name="submit_name" value="{$submit_name}">
    </div>
</div>
<div class="control-group">
    <label class="control-label" >{$testi_url}</label>
    <div class="controls"> 
        <input  class="input-block-level" type="text" name="submit_website" value="{$submit_website}">
    </div>
</div>
<div class="control-group">
    <label class="control-label" >{$testi_content}</label>
    <div class="controls">      
        <textarea name="submit_testimonial" class="input-block-level" rows="6">{$submit_testimonial}</textarea>
    </div>
</div>
{if isset($testimonials_security)}
	{if $testimonials_security}
	<div class="control-group">
	    <label class="control-label" >{$testi_code}</label>
    <div class="controls">      
      <input class="input-xlarge span4" id="security_code" name="security_code" type="text" />
    </div>
    <div class="controls" style="margin-top:20px;">      
      {$testimonial_captcha}
    </div>
	</div>
	{/if}
{/if}
 <div class="control-group">
    <label for="" class="control-label"></label>
    <div class="controls">
       <input class="btn btn-primary" type="submit" value="{$testi_submit}" name="iDevAffiliate">
    </div>
    </div>
</form>
{/if}