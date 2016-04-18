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

{if isset($display_edit_errors)}
<br /><br /><br />
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4>{$error_title}</h4>
    {$error_list}
</div>   
{/if}

{if isset($edit_success)}
<br /><br /><br />
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {$edit_success}
</div>   
{/if}

{include file='file:account_edit_custom.tpl'}
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="edit" value="1">
<input type="hidden" name="page" value="17">
<legend style="color:{$legend};">General Preferences</legend>  
<div class="control-group">
    <label for="email" class="control-label">Email Language</label>
    <div class="controls">                  
      {include file='file:account_edit_email_preferences.tpl'}
    </div>
</div>  
{if isset($optionals_used)}
{if isset($row_email)}
       <div class="control-group">
        <label for="email" class="control-label">{$edit_standard_email}</label>
        <div class="controls">                  
          <input type="text" class="input-xxlarge span12" name="email" size="30" value="{$postemail}"  tabindex="4">
        </div>
      </div>  
{/if}
 {if isset($row_company)}
      <div class="control-group">
        <label for="company" class="control-label">{$edit_standard_company}</label>
        <div class="controls">           
          <input type="text" class="input-xxlarge span12" name="company" size="30" value="{$postcompany}"  tabindex="5">
        </div>
      </div>  
 {/if}
 {if isset($row_checks)}
              <div class="control-group">
                <label for="payable" class="control-label">{$edit_standard_checkspayable}</label>
                <div class="controls">             
                  <input type="text" class="input-xxlarge span12" name="payable" size="30" value="{$postchecks}"  tabindex="6">
                </div>
              </div>  
{/if}
 {if isset($row_website)}
          <div class="control-group">
            <label for="url" class="control-label">{$edit_standard_weburl}</label>
            <div class="controls">           
              <input type="text" class="input-xxlarge span12" name="url" size="30" value="{$postwebsite}"  tabindex="7">
            </div>
          </div>  
{/if}
  {if isset($row_taxinfo)}
  <div class="control-group">
    <label for="tax_id_ssn" class="control-label">{$edit_standard_taxinfo}</label>
    <div class="controls">             
      <input type="text" class="input-xxlarge span12" name="tax_id_ssn" size="30" value="{$posttax}"  tabindex="8">
    </div>
  </div>  
  {/if}
{/if}
<legend style="color:{$legend};">{$edit_personal_title}</legend>        
   <div class="control-group">
    <label for="f_name" class="control-label">{$edit_personal_fname}</label>
    <div class="controls">              
      <input type="text" class="input-xxlarge span12" name="f_name"  value="{$postfname}"  tabindex="9">
    </div>
  </div>
   <div class="control-group">
    <label for="l_name" class="control-label">{$edit_personal_lname}</label>
    <div class="controls">            
      <input type="text" class="input-xxlarge span12" name="l_name"  value="{$postlname}"  tabindex="10">
    </div>
  </div>
 
     <div class="control-group">
    <label for="phone" class="control-label">{$edit_personal_phone}</label>
    <div class="controls">              
      <input type="text" class="input-xxlarge span12" name="phone"  value="{$postphone}"  tabindex="15">
    </div>
  </div>
  <div class="control-group">
    <label for="fax" class="control-label">{$edit_personal_fax}</label>
    <div class="controls">             
      <input type="text" class="input-xxlarge span12" name="fax"  value="{$postfaxnm}"  tabindex="16">
    </div>
  </div>
 
  <div class="control-group">
    <label for="address_one" class="control-label">{$edit_personal_addr1}</label>
    <div class="controls">           
      <input type="text" class="input-xxlarge span12" name="address_one"  value="{$postaddr1}"  tabindex="11">
    </div>
  </div>
  <div class="control-group">
    <label for="address_two" class="control-label">{$edit_personal_addr2}</label>
    <div class="controls">             
      <input type="text" class="input-xxlarge span12" name="address_two"  value="{$postaddr2}"  tabindex="12">
    </div>
  </div>
  <div class="control-group">
    <label for="city" class="control-label">{$edit_personal_city}</label>
    <div class="controls">            
      <input type="text" class="input-xxlarge span12" name="city"  value="{$postcity}"  tabindex="13">
    </div>
  </div>
  <div class="control-group">
    <label for="state" class="control-label">{$edit_personal_state}</label>
    <div class="controls">              
      <input type="text" class="input-xxlarge span12" name="state"  value="{$poststate}"  tabindex="14">
    </div>
  </div>
    
     <div class="control-group">
    <label for="zip" class="control-label">{$edit_personal_zip}</label>
    <div class="controls">            
      <input type="text" class="input-xxlarge span12" name="zip"  value="{$postzip}"  tabindex="17">
    </div>
  </div>
   
  <div class="control-group">
    <label for="countries" class="control-label">{$edit_personal_country}</label>
    <div class="controls">
        {include file='file:account_edit_countries.tpl'}
    </div>
</div>
{if isset($paypal_required)}
{include file='file:account_edit_paypal_required.tpl'}
{/if}
{if isset($paypal_optional)}
{include file='file:account_edit_paypal_optional.tpl'}
{/if}
 <div class="control-group">
    <label for="" class="control-label"></label>
    <div class="controls">
       <input class="btn btn-primary" type="submit" value="{$edit_button}">
    </div>
</div>
</form>