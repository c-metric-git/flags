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

{include file='file:header.tpl'}
<div class="container" style="margin-top:15px;">
 <div class="container-fluid">
      <div class="row-fluid">
            <div class="span3">
                <div class="well">
                    <legend><img border="0" src="{$theme_folder}/images/signup.gif" width="32" height="32"/> {$private_heading}</legend>                   
                    <p>{$private_info}</p>
                </div>
            </div>
            <div class="span9">                
                <form method="POST" action="private.php" class="form-horizontal">     
                    <fieldset>                        
                            <legend style="color:{$legend};">{$private_required_heading}</legend>    
                            
                             {if isset($display_signup_errors)}                               
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>  
                                    <h4>{$error_title}</h4>                             
                                    {$error_list}
                                </div>    
                             {/if}                    
                             <div class="control-group">
                                <label class="control-label" >{$private_code_title}</label>
                                <div class="controls">                           
                                    <input type="text" style="width:100%;height:40px" class="input-xxlarge" name="signup_code" value="{if isset($signup_code)}{$signup_code}{/if}"/>
                                </div>
                             </div>
        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
               <input type="submit" class="btn-primary btn" value="{$private_button}"/>
            </div>            
        </div>
		<input name="token_affiliate_private" value="{$private_token}" type="hidden" />
                </form>
            
            </div>
      </div>
 </div>
 </div>

    {include file='file:footer.tpl'}
