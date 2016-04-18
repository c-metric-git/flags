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
<div class="container">
 <div class="container-fluid">
      <div class="row-fluid">
            <div class="span4">
                <div class="well">
                    <legend><img border="0" src="{$theme_folder}/images/login.gif" width="32" height="32"/> {$login_left_column_title}</legend>                   
                    <p style="color:{$gb_text_color};">{$login_left_column_text}</p>
                </div>
            </div>
            <div class="span8"> 
                <form method="POST"  action="login.php" class="form-horizontal">            
                    <fieldset>                        
                            <legend style="color:{$legend};">{$login_title}</legend>    
                             {if isset($login_invalid)}                               
                                <div class="alert alert-error">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>                               
                                {$login_invalid}
                            </div>    
                             {/if}                    
                             <div class="control-group">
                                <label class="control-label" >{$login_username}</label>
                                <div class="controls">                           
                                    <input type="text" class="input-xlarge" placeholder="Username" name="userid"/>
                                </div>
                              </div>
                              <div class="control-group">
                                <label class="control-label" >{$login_password}</label>
                                <div class="controls">                           
                                    <input type="password" class="input-xlarge" name="password"  placeholder="Password"  autocomplete="off"/>
                                </div>
                              </div>                              
                              <div class="control-group">
                                 <label class="control-label" ></label>
                                  <div class="controls">                           
                                         <input class="btn btn-primary" type="submit" value="{$login_now}"/>
                                    </div>                               
                              </div>
					<input name="token_affiliate_login" value="{$login_token}" type="hidden" />
                  </fieldset>
                  </form>
                  <form method="POST"  action="login.php" class="form-horizontal">    
                  <fieldset>                        
                            <legend style="color:{$legend};">{$login_send_title}</legend> 
                            <div class="control-group">
                                <label class="control-label" >{$login_send_username}</label>
                                <div class="controls">                           
                                    <input class="input-xlarge" type="text" placeholder="Username" name="sendpass"/>
                                </div>
                            </div>  
                            {if isset($login_details)}                           
                            <div class="control-group">
                                <label class="control-label" ></label>
                                <div class="controls">                           
                                    <font color="#CC0000">{$login_details}</font>
                                </div>
                            </div>    
                            {/if} 
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">                           
                                    <input type="submit" class="btn btn-primary" value="{$login_send_pass}"/> 
                                </div>
                            </div>
<input name="token_affiliate_creds" value="{$send_pass_token}" type="hidden" />							
                   </fieldset> 
              </form>  
            </div>
      </div>
 </div>
 </div>
    {include file='file:footer.tpl'}