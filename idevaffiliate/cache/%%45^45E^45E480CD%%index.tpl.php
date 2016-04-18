<?php /* Smarty version 2.6.14, created on 2016-04-09 11:19:36
         compiled from index.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="container">
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span8">
            <h4><?php echo $this->_tpl_vars['index_heading_1']; ?>
</h4>
            <p><?php echo $this->_tpl_vars['index_paragraph_1']; ?>
</p>
            <h4><?php echo $this->_tpl_vars['index_heading_2']; ?>
</h4>
            <p><?php echo $this->_tpl_vars['index_paragraph_2']; ?>
</p>
            <h4><?php echo $this->_tpl_vars['index_heading_3']; ?>
</h4>
            <p><?php echo $this->_tpl_vars['index_paragraph_3']; ?>
</p>
        </div>
        <div class="span4">
		
            <div class="well"><legend><?php echo $this->_tpl_vars['index_login_title']; ?>
</legend>
<?php if (! isset ( $this->_tpl_vars['affiliateUsername'] )): ?>
                <form method="POST" action="login.php">       
                    <fieldset>                        
                        <div class="control-group">
                                <div class="controls">                           
                                    <input class="span12" placeholder="Username" type="text" name="userid" size="10" value="<?php echo $this->_tpl_vars['index_login_username_field']; ?>
"/>
                                </div>
                        </div> 
                        <div class="control-group">
                                <div class="controls">                           
                                    <input class="span12" type="password" placeholder="Password" name="password" size="10" value="<?php echo $this->_tpl_vars['index_login_password_field']; ?>
" autocomplete="off"/>
                                </div>
                        </div> 
                        <input class="btn btn-primary btn-block" type="submit" value="<?php echo $this->_tpl_vars['index_login_button']; ?>
"/>     
<input name="token_affiliate_login" value="<?php echo $this->_tpl_vars['login_token']; ?>
" type="hidden" />				
                    </fieldset>       
                </form>
<?php else: ?>
<a href="account.php" class="btn btn-primary btn-block"><?php echo $this->_tpl_vars['header_accountLink']; ?>
</a>
<?php endif; ?>
            </div>
        </div>
        </div>
        
        <div class="padding"></div>
        <div class="row-fluid">                        
                <table class="table table-bordered" style="color:<?php echo $this->_tpl_vars['text_color']; ?>
;">
                <thead>
                    <tr>
                      <th><?php echo $this->_tpl_vars['index_table_title']; ?>
</th>
                      <th></th>
                    </tr>
                 </thead>                    
                    <tr><td><?php echo $this->_tpl_vars['index_table_commission_type']; ?>
</td>
                    <td><?php echo $this->_tpl_vars['commission_type_info']; ?>
</td>
                    </tr>                    
                                        <?php if (isset ( $this->_tpl_vars['choose_percentage_payout'] )): ?>
                    <tr><td><?php echo $this->_tpl_vars['index_table_sale']; ?>
:</td><td><?php echo $this->_tpl_vars['bot1']; ?>
% <?php echo $this->_tpl_vars['index_table_sale_text']; ?>
</td></tr>
                    <?php endif; ?>
                    
                    <?php if (isset ( $this->_tpl_vars['choose_flatrate_payout'] )): ?>
                    <tr><td><?php echo $this->_tpl_vars['index_table_sale']; ?>
:</td><td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['bot2'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
 <?php echo $this->_tpl_vars['index_table_sale_text']; ?>
</td></tr>
                    <?php endif; ?>
                    
                    <?php if (isset ( $this->_tpl_vars['choose_perclick_payout'] )): ?>
                    <tr><td><?php echo $this->_tpl_vars['index_table_click']; ?>
:</td><td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['bot3'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
 <?php echo $this->_tpl_vars['index_table_click_text']; ?>
</td></tr>
                    <?php endif; ?>
                    
                    <?php if (isset ( $this->_tpl_vars['payout_add_small_row'] )): ?>
                    
                    <?php endif; ?>
                    
                                        
                    <?php if (isset ( $this->_tpl_vars['add_balance_row'] )): ?>
                    <tr><td><?php echo $this->_tpl_vars['index_table_initial_deposit']; ?>
</td><td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['init_deposit'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
 - <font color="#CC0000"><b><?php echo $this->_tpl_vars['index_table_deposit_tag']; ?>
</b></font></td></tr>
                    <?php endif; ?>
                    
                    <?php if (isset ( $this->_tpl_vars['add_requirements_row'] )): ?>
                    <tr><td><?php echo $this->_tpl_vars['index_table_requirements']; ?>
</td><td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['init_req'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
 - <?php echo $this->_tpl_vars['index_table_requirements_tag']; ?>
</td></tr>
                    <?php endif; ?>
                    
                    <tr>
                    <td><?php echo $this->_tpl_vars['index_table_duration']; ?>
</td><td><?php echo $this->_tpl_vars['index_table_duration_tag']; ?>
</td>
                    </tr>
                </table>
           
        </div>
    </div>
</div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>