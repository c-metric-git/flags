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

{if isset($alternate_keywords_enabled)}
{if isset($display_custom_success)}
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>   
<h4>{$custom_success_title}</h4> 
{$custom_success_message}
</div> 
{elseif isset($display_custom_errors)}
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>   
<h4>{$custom_error_title}</h4> 
{$custom_error_list}
</div> 
{/if}
<legend style="color:{$legend};">{$alternate_title}</legend>
<form action="account.php" method="post">
<input type="hidden" name="create_alternate" value="1">
<input type="hidden" name="page" value="35">
<h4>{$alternate_option_1}</h4>
<div class="help-block">
<p>{$alternate_info_1}</p>
</div>
<div class="row-fluid">
    <div class="span9">
        <input class="input-xxlarge" type="text" name="custom_link" value="http://" />
    </div>
    <div class="span3">
       <input class="btn btn-primary" type="submit" value="{$alternate_button}" name="{$alternate_button}">
    </div>
</div>
</form>
<h5>{$alternate_links_heading}</h5>
{section name=nr loop=$clinks_results}
<p>{$clinks_results[nr].clink_url}</p>
<p><input class="input-xxlarge" type="text" name="sub_link" style="background-color:#f2f6ff;" value="{$clinks_results[nr].clink_linkurl}" /> [<a href="account.php?page=35&custom_remove={$clinks_results[nr].clink_id}">{$alternate_links_remove}</a>]</p>
{sectionelse}
<p>{$alternate_none}</p>
{/section}
<p>{$alternate_links_note}</p>
<p><a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank" class="btn btn-small btn-success">{$alternate_tutorial}</a></p>
<div class="row-fluid">
<div class="span12">
<h4>{$alternate_option_2}</h4>
</div>
</div>
<p>{$alternate_info_2}</p>
<p>{$alternate_variable}: url</p>
<p>{$alternate_build}</p>
<p><input class="input-xxlarge" type="text" name="sub_link" value="{$alternate_keyword_linkurl}" /></p>
<p>{$alternate_example}: {$alternate_keyword_linkurl}&url=<b>http://www.yahoo.com</b></p>
<p><a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank" class="btn btn-small btn-success">{$alternate_tutorial}</a></p>
{/if}