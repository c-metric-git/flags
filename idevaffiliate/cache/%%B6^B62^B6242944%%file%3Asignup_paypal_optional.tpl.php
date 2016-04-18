<?php /* Smarty version 2.6.14, created on 2016-04-09 11:21:05
         compiled from file:signup_paypal_optional.tpl */ ?>

<legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['signup_paypal_optional_title']; ?>
</legend>     
<div class="control-group">
<label for="<?php echo $this->_tpl_vars['signup_paypal_optional_payme']; ?>
" class="control-label"><?php echo $this->_tpl_vars['signup_paypal_optional_payme']; ?>
</label>
<div class="controls">              
 <input type="checkbox" name="pp" value="1"<?php echo $this->_tpl_vars['postcheck']; ?>
> (<?php echo $this->_tpl_vars['signup_paypal_optional_checkbox']; ?>
)
</div>
</div>
<div class="control-group">
<label for="<?php echo $this->_tpl_vars['signup_paypal_optional_account']; ?>
" class="control-label"><?php echo $this->_tpl_vars['signup_paypal_optional_account']; ?>
</label>
<div class="controls">              
 <input type="text" name="pp_account" size="25" value="<?php echo $this->_tpl_vars['pp_account']; ?>
" class="input-large span12">
 <p><?php echo $this->_tpl_vars['signup_paypal_optional_notes']; ?>
</p>
</div>
</div>