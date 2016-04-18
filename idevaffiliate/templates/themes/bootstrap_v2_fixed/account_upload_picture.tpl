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

{if isset($pic_upload)}
<legend style="color:{$legend};">{$pic_title}</legend>
    {if isset($upload_success)}
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>    
        {$success_message}
    </div> 
    {elseif isset($upload_error)}
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>  
        <h4>{$picture_error_title}</h4>  
        {$error_message}
    </div> 
    {else}
    {/if}
{$pic_info}<br /><br />
<ul>
<li>{$pic_bullet_1}</li>
<li>{$pic_bullet_2}</li>
<li>{$pic_bullet_3}</li>
</ul>
<form ENCTYPE="multipart/form-data" ACTION="account.php" METHOD="post">
<input type="hidden" name="update_picture" value="1">
<input type="hidden" name="page" value="43">
<div class="control-group">
        <label for="email" class="control-label">{$pic_file}</label>
        <div class="controls">  
         <input type="file" name="picture"/>    
        </div>
</div>  
<input class="btn btn-primary" type="submit" value="{$pic_button}"/>
</form>
{if isset($picture_exists)}
<legend style="color:{$legend};">{$pic_current}</legend>
[<a href="account.php?page=43&remove_picture={$affiliate_id}">{$pic_remove}</a>]
<br /><br />
<img style="border:none;" src="assets/pictures/{$picture_exists}" width="{$image_width}" height="{$image_height}" />
{/if}
{/if}