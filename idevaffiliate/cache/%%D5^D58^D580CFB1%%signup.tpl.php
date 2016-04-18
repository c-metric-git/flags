<?php /* Smarty version 2.6.14, created on 2016-04-09 11:21:05
         compiled from signup.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="container">
    <?php if (isset ( $this->_tpl_vars['maintenance_mode'] )): ?>
        <div style="margin-top:60px" class="row">
         <div class="span12">
          <h1><?php echo $this->_tpl_vars['signup_maintenance_heading']; ?>
</h1>
          <p><?php echo $this->_tpl_vars['signup_maintenance_info']; ?>
</p>
         </div>
        </div>

		<?php else: ?>
		
<div class="container-fluid">
<div class="row-fluid">

 <div class="span3">
      <div class="well">
      	<fieldset>
        <legend><img style="border:none;" src="<?php echo $this->_tpl_vars['theme_folder']; ?>
/images/signup.gif" width="32" height="32"/> <?php echo $this->_tpl_vars['signup_left_column_title']; ?>
</legend>
        <p style="color:<?php echo $this->_tpl_vars['gb_text_color']; ?>
;"><?php echo $this->_tpl_vars['signup_left_column_text']; ?>
</p>
        </fieldset>
      </div>
</div>

<div class="span9"> 
    <form class="form-horizontal" action="signup.php" method="POST">
    <input type="hidden" value="1" name="submit"/> 
        <fieldset>
         <?php if (isset ( $this->_tpl_vars['signup_complete'] )): ?>
            <div class="alert alert-success">
              <button type="button" class="close" data-dismiss="alert">&times;</button>   
              <h4><?php echo $this->_tpl_vars['signup_page_success']; ?>
</h4>
              <?php echo $this->_tpl_vars['signup_success_email_comment']; ?>

            </div>
			<a href="account.php" class="btn btn-success"><?php echo $this->_tpl_vars['signup_success_login_link']; ?>
</a>
        <?php else: ?>
             <?php if (isset ( $this->_tpl_vars['display_signup_errors'] )): ?>
                <div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>  
                    <h4><?php echo $this->_tpl_vars['error_title']; ?>
</h4>
                    <p><?php echo $this->_tpl_vars['error_list']; ?>
</p>
                </div>                                
             <?php endif; ?>
             
           <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['signup_login_title']; ?>
</legend>  
           <div class="control-group">
            <label for="username" class="control-label"><?php echo $this->_tpl_vars['signup_login_username']; ?>
</label>
            <div class="controls">
              <input type="text" id="username" class="input-xlarge span10" name="username"  value="<?php if (isset ( $this->_tpl_vars['postuser'] )):  echo $this->_tpl_vars['postuser'];  endif; ?>" tabindex="1" />
			  <a href="#" class="example" rel="popover" data-content="<?php echo $this->_tpl_vars['signup_login_minmax_chars']; ?>
" data-original-title="<?php echo $this->_tpl_vars['signup_login_username']; ?>
 & <?php echo $this->_tpl_vars['signup_login_password']; ?>
" data-placement="left"><img src="<?php echo $this->_tpl_vars['theme_folder']; ?>
/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
            </div>
          </div>

           <div class="control-group">
            <label for="password" class="control-label"><?php echo $this->_tpl_vars['signup_login_password']; ?>
</label>
            <div class="controls">              
              <input type="password" class="input-xlarge span10" name="password" value="<?php if (isset ( $this->_tpl_vars['postpass'] )):  echo $this->_tpl_vars['postpass'];  endif; ?>" tabindex="2" autocomplete="off" />
            </div>
          </div>     
           <div class="control-group">
            <label for="password_c" class="control-label"><?php echo $this->_tpl_vars['signup_login_password_again']; ?>
</label>
            <div class="controls">              
              <input type="password" class="input-xlarge span10" name="password_c" value="<?php echo $this->_tpl_vars['postpasc']; ?>
" tabindex="3" autocomplete="off" />
			  <a href="#" class="example" rel="popover" data-content="<?php echo $this->_tpl_vars['signup_login_must_match']; ?>
" data-original-title="<?php echo $this->_tpl_vars['signup_login_password_again']; ?>
" data-placement="left"><img src="<?php echo $this->_tpl_vars['theme_folder']; ?>
/css/bootstrap/img/question.png" style="border:none; width:16px; height:16px; cursor:pointer;"/></a>
            </div>
          </div>   
          <?php if (isset ( $this->_tpl_vars['optionals_used'] )): ?> 
          <fieldset>
           <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['signup_standard_title']; ?>
</legend>
           <?php if (isset ( $this->_tpl_vars['row_email'] )): ?>
               <div class="control-group">
                <label for="email" class="control-label"><?php echo $this->_tpl_vars['signup_standard_email']; ?>
</label>
                <div class="controls">                  
                  <input type="text" class="input-xlarge span12" name="email" size="30" value="<?php echo $this->_tpl_vars['postemail']; ?>
"  tabindex="4">
                </div>
              </div>  
          <?php endif; ?>
          <?php if (isset ( $this->_tpl_vars['row_company'] )): ?>
              <div class="control-group">
                <label for="company" class="control-label"><?php echo $this->_tpl_vars['signup_standard_company']; ?>
</label>
                <div class="controls">           
                  <input type="text" class="input-xlarge span12" name="company" size="30" value="<?php echo $this->_tpl_vars['postcompany']; ?>
"  tabindex="5">
                </div>
              </div>  
          <?php endif; ?>
          <?php if (isset ( $this->_tpl_vars['row_checks'] )): ?>
              <div class="control-group">
                <label for="payable" class="control-label"><?php echo $this->_tpl_vars['signup_standard_checkspayable']; ?>
</label>
                <div class="controls">             
                  <input type="text" class="input-xlarge span12" name="payable" size="30" value="<?php echo $this->_tpl_vars['postchecks']; ?>
"  tabindex="6">
                </div>
              </div>  
          <?php endif; ?>
          <?php if (isset ( $this->_tpl_vars['row_website'] )): ?>
          <div class="control-group">
            <label for="url" class="control-label"><?php echo $this->_tpl_vars['signup_standard_weburl']; ?>
</label>
            <div class="controls">           
              <input type="text" class="input-xlarge span12" name="url" size="30" value="<?php echo $this->_tpl_vars['postwebsite']; ?>
"  tabindex="7">
            </div>
          </div>  
          <?php endif; ?>
          <?php if (isset ( $this->_tpl_vars['row_taxinfo'] )): ?>
          <div class="control-group">
            <label for="tax_id_ssn" class="control-label"><?php echo $this->_tpl_vars['signup_standard_taxinfo']; ?>
</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="tax_id_ssn" size="30" value="<?php echo $this->_tpl_vars['posttax']; ?>
"  tabindex="8">
            </div>
          </div>  
          <?php endif; ?>
          </fieldset>
       <?php endif; ?>
	   
<?php if (isset ( $this->_tpl_vars['standards_used'] )): ?>
	<fieldset>
         <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['signup_personal_title']; ?>
</legend>
<?php if (isset ( $this->_tpl_vars['row_fname'] )): ?>
           <div class="control-group">
            <label for="f_name" class="control-label"><?php echo $this->_tpl_vars['signup_personal_fname']; ?>
</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="f_name"  value="<?php echo $this->_tpl_vars['postfname']; ?>
" tabindex="9">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_lname'] )): ?>
           <div class="control-group">
            <label for="l_name" class="control-label"><?php echo $this->_tpl_vars['signup_personal_lname']; ?>
</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="l_name"  value="<?php echo $this->_tpl_vars['postlname']; ?>
"  tabindex="10">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_addr1'] )): ?>
          <div class="control-group">
            <label for="address_one" class="control-label"><?php echo $this->_tpl_vars['signup_personal_addr1']; ?>
</label>
            <div class="controls">           
              <input type="text" class="input-xlarge span12" name="address_one"  value="<?php echo $this->_tpl_vars['postaddr1']; ?>
"  tabindex="11">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_addr2'] )): ?>
          <div class="control-group">
            <label for="address_two" class="control-label"><?php echo $this->_tpl_vars['signup_personal_addr2']; ?>
</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="address_two"  value="<?php echo $this->_tpl_vars['postaddr2']; ?>
"  tabindex="12">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_city'] )): ?>
          <div class="control-group">
            <label for="city" class="control-label"><?php echo $this->_tpl_vars['signup_personal_city']; ?>
</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="city"  value="<?php echo $this->_tpl_vars['postcity']; ?>
"  tabindex="13">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_state'] )): ?>
          <div class="control-group">
            <label for="state" class="control-label"><?php echo $this->_tpl_vars['signup_personal_state']; ?>
</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="state"  value="<?php echo $this->_tpl_vars['poststate']; ?>
"  tabindex="14">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_phone'] )): ?>
		  <div class="control-group">
            <label for="phone" class="control-label"><?php echo $this->_tpl_vars['signup_personal_phone']; ?>
</label>
            <div class="controls">              
              <input type="text" class="input-xlarge span12" name="phone"  value="<?php echo $this->_tpl_vars['postphone']; ?>
"  tabindex="15">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_fax'] )): ?>
          <div class="control-group">
            <label for="fax" class="control-label"><?php echo $this->_tpl_vars['signup_personal_fax']; ?>
</label>
            <div class="controls">             
              <input type="text" class="input-xlarge span12" name="fax"  value="<?php echo $this->_tpl_vars['postfaxnm']; ?>
"  tabindex="16">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_zip'] )): ?>
             <div class="control-group">
            <label for="zip" class="control-label"><?php echo $this->_tpl_vars['signup_personal_zip']; ?>
</label>
            <div class="controls">            
              <input type="text" class="input-xlarge span12" name="zip"  value="<?php echo $this->_tpl_vars['postzip']; ?>
"  tabindex="17">
            </div>
          </div>
<?php endif;  if (isset ( $this->_tpl_vars['row_countries'] )): ?>
          <div class="control-group">
            <label for="countries" class="control-label"><?php echo $this->_tpl_vars['signup_personal_country']; ?>
</label>
            <div class="controls">
                <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:countries.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </div>
          </div>
<?php endif; ?>
</fieldset>
<?php endif; ?>



        <?php if (isset ( $this->_tpl_vars['payment_choice_used'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_payment_choices.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['paypal_required'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_paypal_required.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['paypal_optional'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_paypal_optional.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['terms_conditions'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_terms.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['canspam_conditions'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_canspam.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['insert_custom_fields'] )): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_custom.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endif; ?>

        <?php if (isset ( $this->_tpl_vars['security_required'] )): ?>
        	<?php if ($this->_tpl_vars['security_required']): ?>
        		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:signup_security_code.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
        <?php endif; ?>

        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
               <input type="submit" class="btn-primary btn" value="<?php echo $this->_tpl_vars['signup_page_button']; ?>
"/>
            </div>            
        </div>
    <?php endif; ?>
</fieldset>
</form>

</div>
</div>
</div>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
