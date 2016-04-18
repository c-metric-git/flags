

{include file='file:header.tpl'}

<div class="container">





 <div class="container-fluid">

      <div class="row-fluid">

            <div class="span4">

                <div class="well">

                    <legend><img border="0" src="{$theme_folder}/images/contact.gif" width="32" height="32"/> {$contact_left_column_title}</legend>                   

                    <p style="color:{$gb_text_color};">{$contact_left_column_text}</p>

                </div>

            </div>

            <div class="span8">                

            <form name="contact_form" method="POST" action="contact.php" class="form-horizontal">

            <input type="hidden" name="email_contact" value="1"/>

                <fieldset>                        

                        <legend style="color:{$legend};">{$contact_title_display}</legend>

                        {if isset($display_contact_errors)}

                            <div class="alert alert-error">

                                <button type="button" class="close" data-dismiss="alert">&times;</button>

                                <h4>{$error_title}</h4>

                                {$error_list}

                            </div>                           

                        {/if}

						{if isset($contact_email_received)}

						<div class="alert alert-success">

						<button type="button" class="close" data-dismiss="alert">&times;</button>

						{$contact_received_display}

						</div>   

						{/if}

                         <div class="control-group">

                            <label class="control-label" >{$contact_name_display}</label>

                            <div class="controls">                           

                                <input type="text" name="name" class="input-xlarge span12" value="{$contact_name}"/>

                            </div>

                          </div>

                          <div class="control-group">

                            <label class="control-label" >{$contact_email_display}</label>

                            <div class="controls">                           

                                <input type="text" class="input-xlarge span12" name="email" value="{$contact_email}"/>

                            </div>

                          </div>

                          <div class="control-group">

                            <label class="control-label" >{$contact_message_display}</label>

                            <div class="controls">                           

                                <textarea name="message" class="input-xlarge span12" rows="6">{$contact_message}</textarea>

                            </div>

                          </div>                         

                          {if isset($security_required)}
	                          {if $security_required}
	
	                          <div class="control-group">
	
	                            <label class="control-label" >{$signup_security_code}</label>
	
    <div class="controls">      

      <input class="input-xlarge span4" id="security_code" name="security_code" type="text" />

    </div>

    <div class="controls" style="margin-top:20px;">      

      {$captcha_image}

    </div>                        
	
	                          </div>
	
	                          {/if}
                          {/if}

                           <div class="control-group">

                          {if !isset($contact_email_received)}                         

                          <label class="control-label" ></label>

                          <div class="controls">                           

                                <input class="btn btn-primary" type="submit" value="{$contact_button_display}"/>   

                            </div>                  

                                                   

                         {/if}

                        </div>

                </fieldset>    

            </form>        

    </div>

</div>

</div>

</div>


    {include file='file:footer.tpl'}
