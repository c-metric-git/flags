{*
-------------------------------------------------------
iDevAffiliate Version 7
Copyright - iDevDirect.com LLC
Website: http://www.idevdirect.com/
Support: http://www.idevsupport.com/
Email:   support@idevsupport.com
-------------------------------------------------------
*}

{include file='file:header.tpl'}
<div class="container">
    {if isset($maintenance_mode)}
        <div style="margin-top:60px" class="row">
         <div class="span12">
          <h1>{$signup_maintenance_heading}</h1>
          <p>{$signup_maintenance_info}</p>
         </div>
        </div>

		{else}
		
<div class="container-fluid">
<div class="row-fluid">

 <div class="span3">
      <div class="well">
      	<fieldset>
        <legend><img style="border:none;" src="{$theme_folder}/images/signup.gif" width="32" height="32"/> {$signup_left_column_title}</legend>
        <p style="color:{$gb_text_color};">{$signup_left_column_text}</p>
        </fieldset>
      </div>
</div>

<div class="span9"> 
    <form class="form-horizontal" action="signup.php" method="POST">
    <input type="hidden" value="1" name="submit"/> 
        <fieldset>
         {if isset($signup_complete)}
            <div class="alert alert-success">
              <button type="button" class="close" data-dismiss="alert">&times;</button>   
              <h4>{$signup_page_success}</h4>
              {$signup_success_email_comment}
            </div>
			<a href="account.php" class="btn btn-success">{$signup_success_login_link}</a>
        {else}
             {if isset($display_signup_errors)}
                <div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>  
                    <h4>{$error_title}</h4>
                    <p>{$error_list}</p>
                </div>                                
             {/if}
             
           <legend style="color:{$legend};">{$signup_login_title}</legend>  
           <div class="control-group">
            <label for="username" class="control-label">{$signup_login_username}</label>
            <div class="controls">
              <input type="text" id="username" class="input-xlarge span10" name="username"  value="{if isset($postuser)}{$postuser}{/if}" tabindex="1" />
			  <a href="#" class="example" rel="popover" data-content="{$signup_login_minmax_chars}" data-original-title="{$signup_login_username} & {$signup_login_password}" data-placement="left"><img src="{$theme_folder}/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
            </div>
          </div>

           <div class="control-group">
            <label for="password" class="control-label">{$signup_login_password}</label>
            <div class="controls">              
              <input type="password" class="input-xlarge span10" name="password" value="{if isset($postpass)}{$postpass}{/if}" tabindex="2" autocomplete="off" />
            </div>
          </div>     
           <div class="control-group">
            <label for="password_c" class="control-label">{$signup_login_password_again}</label>
            <div class="controls">              
              <input type="password" class="input-xlarge span10" name="password_c" value="{$postpasc}" tabindex="3" autocomplete="off" />
			  <a href="#" class="example" rel="popover" data-content="{$signup_login_must_match}" data-original-title="{$signup_login_password_again}" data-placement="left"><img src="{$theme_folder}/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
            </div>
          </div>   
          {if isset($optionals_used)} 
          <fieldset>
           <legend style="color:{$legend};">{$signup_standard_title}</legend>
           {if isset($row_email)}
               <div class="control-group">
                <label for="email" class="control-label">{$signup_standard_email}</label>
                <div class="controls">                  
                  <input type="text" class="input-xlarge span12" name="email" size="30" value="{$postemail}"  tabindex="4">
                </div>
              </div>  
          {/if}
          {if isset($row_company)}
              <div class="control-group">
                <label for="company" class="control-label">{$signup_standard_company}</label>
                <div class="controls">           
                  <input type="text" class="input-xlarge span12" name="company" size="30" value="{$postcompany}"  tabindex="5">
                </div>
              </div>  
          {/if}
          {if isset($row_checks)}
              <div class="control-group">
                <label for="payable" class="control-label">{$signup_standard_checkspayable}</label>
                <div class="controls">             
                  <input type="text" class="input-xlarge span12" name="payable" size="30" value="{$postchecks}"  tabindex="6">
                </div>
              </div>  
          {/if}
          {if isset($row_website)}
          <div class="control-group">
            <label for="url" class="control-label">{$signup_standard_weburl}</label>
            <div class="controls">           
              <input type="text" class="input-xlarge span12" name="url" size="30" value="{$postwebsite}"  tabindex="7">
            </div>
          </div>  
          {/if}
          {if isset($row_taxinfo)}
          <div class="control-group">
            <label for="tax_id_ssn" class="control-label">{$signup_standard_taxinfo}</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="tax_id_ssn" size="30" value="{$posttax}"  tabindex="8">
            </div>
          </div>  
          {/if}
          </fieldset>
       {/if}
	   
{if isset($standards_used)}
	<fieldset>
         <legend style="color:{$legend};">{$signup_personal_title}</legend>
{if isset($row_fname)}
           <div class="control-group">
            <label for="f_name" class="control-label">{$signup_personal_fname}</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="f_name"  value="{$postfname}" tabindex="9">
            </div>
          </div>
{/if}
{if isset($row_lname)}
           <div class="control-group">
            <label for="l_name" class="control-label">{$signup_personal_lname}</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="l_name"  value="{$postlname}"  tabindex="10">
            </div>
          </div>
{/if}
{if isset($row_addr1)}
          <div class="control-group">
            <label for="address_one" class="control-label">{$signup_personal_addr1}</label>
            <div class="controls">           
              <input type="text" class="input-xlarge span12" name="address_one"  value="{$postaddr1}"  tabindex="11">
            </div>
          </div>
{/if}
{if isset($row_addr2)}
          <div class="control-group">
            <label for="address_two" class="control-label">{$signup_personal_addr2}</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="address_two"  value="{$postaddr2}"  tabindex="12">
            </div>
          </div>
{/if}
{if isset($row_city)}
          <div class="control-group">
            <label for="city" class="control-label">{$signup_personal_city}</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="city"  value="{$postcity}"  tabindex="13">
            </div>
          </div>
{/if}
{if isset($row_state)}
          <div class="control-group">
            <label for="state" class="control-label">{$signup_personal_state}</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="state"  value="{$poststate}"  tabindex="14">
            </div>
          </div>
{/if}
{if isset($row_phone)}
		  <div class="control-group">
            <label for="phone" class="control-label">{$signup_personal_phone}</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="phone"  value="{$postphone}"  tabindex="15">
            </div>
          </div>
{/if}
{if isset($row_fax)}
          <div class="control-group">
            <label for="fax" class="control-label">{$signup_personal_fax}</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="fax"  value="{$postfaxnm}"  tabindex="16">
            </div>
          </div>
{/if}
{if isset($row_zip)}
             <div class="control-group">
            <label for="zip" class="control-label">{$signup_personal_zip}</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="zip"  value="{$postzip}"  tabindex="17">
            </div>
          </div>
{/if}
{if isset($row_countries)}
          <div class="control-group">
            <label for="countries" class="control-label">{$signup_personal_country}</label>
            <div class="controls">
                {include file='file:countries.tpl'}
            </div>
          </div>
{/if}
</fieldset>
{/if}



        {if isset($payment_choice_used)}
        {include file='file:signup_payment_choices.tpl'}
        {/if}

        {if isset($paypal_required)}
        {include file='file:signup_paypal_required.tpl'}
        {/if}

        {if isset($paypal_optional)}
        {include file='file:signup_paypal_optional.tpl'}
        {/if}

        {if isset($terms_conditions)}
        {include file='file:signup_terms.tpl'}
        {/if}

        {if isset($canspam_conditions)}
        {include file='file:signup_canspam.tpl'}
        {/if}

        {if isset($insert_custom_fields)}
        {include file='file:signup_custom.tpl'}
        {/if}

        {if isset($security_required)}
        	{if $security_required}
        		{include file='file:signup_security_code.tpl'}
			{/if}
        {/if}

        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
               <input type="submit" class="btn-primary btn" value="{$signup_page_button}"/>
            </div>            
        </div>
    {/if}
</fieldset>
</form>

</div>
</div>
</div>
{/if}
</div>

{include file='file:footer.tpl'}

