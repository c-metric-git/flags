<?php /* Smarty version 2.6.14, created on 2016-04-09 11:21:05
         compiled from file:signup_terms.tpl */ ?>

 <legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['signup_terms_title']; ?>
</legend>     
 <div class="control-group">
    <label for="terms" class="control-label"><img border="0" src="<?php echo $this->_tpl_vars['theme_folder']; ?>
/images/cp_terms.gif" width="32" height="32"></label>
    <div class="controls">
        <textarea class="input-xxlarge span12" name="terms" rows="10" readonly><?php echo $this->_tpl_vars['terms_t']; ?>
</textarea>
        <?php if (isset ( $this->_tpl_vars['terms_required'] )): ?>
        <p><input type="checkbox" name="accepted" value="1"<?php echo $this->_tpl_vars['terms_checked']; ?>
>&nbsp;<?php echo $this->_tpl_vars['signup_terms_agree']; ?>
</p>
        <?php endif; ?>
    </div>
 </div>