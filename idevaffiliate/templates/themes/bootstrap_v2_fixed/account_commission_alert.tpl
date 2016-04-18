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

<legend style="color:{$legend};">{$commissionalert_title}</legend>
<div class="well">
{$commissionalert_info}
</div>
<div class="block-help"><p>{$commissionalert_hint}</p></div>
<hr />
<div class="row-fluid">
    <div class="span8">
        <form method="POST" class="form-horizontal" action="commissionalert/download.php">
        <div class="control-group">
            <label class="control-label" >{$commissionalert_profile}</label>
            <div class="controls">                           
                <input class="input-xlarge" type="text" size="20" value="{$sitename}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">{$commissionalert_username}</label>
            <div class="controls">                           
                <input class="input-xlarge"  type="text" size="20" value="{$username}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">{$commissionalert_id}</label>
            <div class="controls">                           
                <input class="input-xlarge"  type="text" size="20" value="{$link_id}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">{$commissionalert_source}</label>
            <div class="controls">                           
                <input class="input-xlarge"  type="text" size="50" value="{$base_url}/">
            </div>
        </div>
        <div class="control-group">
             <input type="hidden" name="affid" value="{$link_id}">
             <label class="control-label"></label>
            <div class="controls">                           
                  <input class="btn btn-primary" type="submit" value="{$commissionalert_download}">
            </div>
        </div>      
    </form>
    </div>
    <div class="span4">
        <img border="0" src="{$theme_folder}/images/ca1.gif" width="148" height="59">
    </div>
</div>    