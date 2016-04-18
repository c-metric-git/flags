<?php /* Smarty version 2.6.14, created on 2016-04-11 06:15:14
         compiled from login.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="container">
 <div class="container-fluid">
      <div class="row-fluid">
            <div class="span4">
                <div class="well">
                    <legend><img border="0" src="<?php echo $this->_tpl_vars['theme_folder']; ?>
/images/login.gif" width="32" height="32"/> <?php echo $this->_tpl_vars['login_left_column_title']; ?>
</legend>                   
                    <p style="color:<?php echo $this->_tpl_vars['gb_text_color']; ?>
;"><?php echo $this->_tpl_vars['login_left_column_text']; ?>
</p>
                </div>
            </div>
            <div class="span8"> 
                <form method="POST"  action="login.php" class="form-horizontal">            
                    <fieldset>                        
                            <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['login_title']; ?>
</legend>    
                             <?php if (isset ( $this->_tpl_vars['login_invalid'] )): ?>                               
                                <div class="alert alert-error">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>                               
                                <?php echo $this->_tpl_vars['login_invalid']; ?>

                            </div>    
                             <?php endif; ?>                    
                             <div class="control-group">
                                <label class="control-label" ><?php echo $this->_tpl_vars['login_username']; ?>
</label>
                                <div class="controls">                           
                                    <input type="text" class="input-xlarge" placeholder="Username" name="userid"/>
                                </div>
                              </div>
                              <div class="control-group">
                                <label class="control-label" ><?php echo $this->_tpl_vars['login_password']; ?>
</label>
                                <div class="controls">                           
                                    <input type="password" class="input-xlarge" name="password"  placeholder="Password"  autocomplete="off"/>
                                </div>
                              </div>                              
                              <div class="control-group">
                                 <label class="control-label" ></label>
                                  <div class="controls">                           
                                         <input class="btn btn-primary" type="submit" value="<?php echo $this->_tpl_vars['login_now']; ?>
"/>
                                    </div>                               
                              </div>
					<input name="token_affiliate_login" value="<?php echo $this->_tpl_vars['login_token']; ?>
" type="hidden" />
                  </fieldset>
                  </form>
                  <form method="POST"  action="login.php" class="form-horizontal">    
                  <fieldset>                        
                            <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['login_send_title']; ?>
</legend> 
                            <div class="control-group">
                                <label class="control-label" ><?php echo $this->_tpl_vars['login_send_username']; ?>
</label>
                                <div class="controls">                           
                                    <input class="input-xlarge" type="text" placeholder="Username" name="sendpass"/>
                                </div>
                            </div>  
                            <?php if (isset ( $this->_tpl_vars['login_details'] )): ?>                           
                            <div class="control-group">
                                <label class="control-label" ></label>
                                <div class="controls">                           
                                    <font color="#CC0000"><?php echo $this->_tpl_vars['login_details']; ?>
</font>
                                </div>
                            </div>    
                            <?php endif; ?> 
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">                           
                                    <input type="submit" class="btn btn-primary" value="<?php echo $this->_tpl_vars['login_send_pass']; ?>
"/> 
                                </div>
                            </div>
<input name="token_affiliate_creds" value="<?php echo $this->_tpl_vars['send_pass_token']; ?>
" type="hidden" />							
                   </fieldset> 
              </form>  
            </div>
      </div>
 </div>
 </div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>