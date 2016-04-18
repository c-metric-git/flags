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

{if isset($logos_enabled)}
<legend style="color:{$legend};">{$logo_title}</legend> 
    {if isset($upload_success)}
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>    
        {$success_message}
    </div> 
    {elseif isset($upload_error)}
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>  
        <h4>{$logo_error_title}</h4>  
        {$error_message}
    </div> 
    {else}
    {/if}
{$logo_info}<br /><br />
<ul>
<li>{$logo_bullet_one}</li>
<li>{$logo_bullet_two}</li>
<li>{$logo_bullet_three}</li>
{if isset($logo_size_required)}
    <li>
    {$logo_bullet_req_size_one} {$logo_width} {$logo_bullet_req_size_two} {$logo_height} {$logo_bullet_pixels}</li>
{else}
    <li>{$logo_bullet_size_one} {$logo_width} {$logo_bullet_size_two} {$logo_height} {$logo_bullet_pixels}</li>
{/if}
</ul>
<form ENCTYPE="multipart/form-data" ACTION="account.php" METHOD="post">
<input type="hidden" name="update_logo" value="1">
<input type="hidden" name="page" value="27">
<div class="control-group">
        <label for="email" class="control-label">{$logo_file}</label>
        <div class="controls">  
         <input type="file" name="logo"/>    
        </div>
</div>  
<input class="btn btn-primary" type="submit" value="{$logo_button}"/>
</form>
<legend style="color:{$legend};">{$logo_current}</legend>  
{if isset($image_exists)}
<b>{$logo_display_status} {$image_status}</b><br />
[<a href="account.php?page=27&remove_logo={$affiliate_id}">{$logo_remove}</a>]
<br /><br />
{/if}
<img style="border:none;" src="{$image}" height="{$image_height}" width="{$image_width}">
{/if}