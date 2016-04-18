<?php /* Smarty version 2.6.14, created on 2016-04-09 11:19:37
         compiled from file:footer.tpl */ ?>

<div id="push"></div>
</div> <!--Wrapper for sticky footer-->
<footer class="footer">
		<div class="container">
        <div class="container-fluid">
		
        
        <div class="row-fluid">
		<div class="float-left footerLink">
		<a href="http://www.idevdirect.com<?php echo $this->_tpl_vars['affiliate_account']; ?>
" target="_blank"><?php echo $this->_tpl_vars['footer_tag']; ?>
</a>
		</div>
        
		<div class="float-right footerLink">
		<?php echo $this->_tpl_vars['footer_copyright']; ?>
 <?php  echo date("Y");  ?> <a href="<?php echo $this->_tpl_vars['siteurl']; ?>
" target=_blank><b><?php echo $this->_tpl_vars['sitename']; ?>
</b></a> - <?php echo $this->_tpl_vars['footer_rights']; ?>

		</div>
		<?php if (isset ( $this->_tpl_vars['social_enabled'] ) && isset ( $this->_tpl_vars['social_location_footer'] )): ?>
		<div class="float-right" style="margin:8px 5px; 0 0px;" align="center">
		<?php unset($this->_sections['nr']);
$this->_sections['nr']['name'] = 'nr';
$this->_sections['nr']['loop'] = is_array($_loop=$this->_tpl_vars['social_icons']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['nr']['show'] = true;
$this->_sections['nr']['max'] = $this->_sections['nr']['loop'];
$this->_sections['nr']['step'] = 1;
$this->_sections['nr']['start'] = $this->_sections['nr']['step'] > 0 ? 0 : $this->_sections['nr']['loop']-1;
if ($this->_sections['nr']['show']) {
    $this->_sections['nr']['total'] = $this->_sections['nr']['loop'];
    if ($this->_sections['nr']['total'] == 0)
        $this->_sections['nr']['show'] = false;
} else
    $this->_sections['nr']['total'] = 0;
if ($this->_sections['nr']['show']):

            for ($this->_sections['nr']['index'] = $this->_sections['nr']['start'], $this->_sections['nr']['iteration'] = 1;
                 $this->_sections['nr']['iteration'] <= $this->_sections['nr']['total'];
                 $this->_sections['nr']['index'] += $this->_sections['nr']['step'], $this->_sections['nr']['iteration']++):
$this->_sections['nr']['rownum'] = $this->_sections['nr']['iteration'];
$this->_sections['nr']['index_prev'] = $this->_sections['nr']['index'] - $this->_sections['nr']['step'];
$this->_sections['nr']['index_next'] = $this->_sections['nr']['index'] + $this->_sections['nr']['step'];
$this->_sections['nr']['first']      = ($this->_sections['nr']['iteration'] == 1);
$this->_sections['nr']['last']       = ($this->_sections['nr']['iteration'] == $this->_sections['nr']['total']);
?><a href="<?php echo $this->_tpl_vars['social_icons'][$this->_sections['nr']['index']]['link']; ?>
" target="_blank" style="padding-right:5px;"><img src="<?php echo $this->_tpl_vars['social_icons'][$this->_sections['nr']['index']]['image']; ?>
" width="32" height="32" style="border:none;"></a><?php endfor; endif; ?>
		</div>
		<?php endif; ?>
		
		
		
		</div>
		
          </div>
          </div>
        </div>
</footer>
<?php echo '
<script>
$(function ()
{ $(".example").popover({trigger: \'hover\', html:true});
});
</script>
'; ?>
 
</body>
</html>