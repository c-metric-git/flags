<?php /* Smarty version 2.6.14, created on 2016-04-09 12:25:14
         compiled from file:account_commission_list.tpl */ ?>

<?php if (isset ( $this->_tpl_vars['commission_group_chosen'] )): ?>
<legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['commission_group_name']; ?>
</legend>
<?php if (isset ( $this->_tpl_vars['commission_results_exist'] )): ?>
<table class="table table-bordered tier">
<thead>
<tr>
<th><strong><?php echo $this->_tpl_vars['details_date']; ?>
</strong></th>
<th><strong><?php echo $this->_tpl_vars['details_status']; ?>
</strong></th>
<th data-hide="phone"><strong><?php echo $this->_tpl_vars['details_commission']; ?>
</strong></th>
<th data-hide="phone"><strong><?php echo $this->_tpl_vars['details_details']; ?>
</strong></th>
</tr>
</thead>
<tbody>
<?php unset($this->_sections['nr']);
$this->_sections['nr']['name'] = 'nr';
$this->_sections['nr']['loop'] = is_array($_loop=$this->_tpl_vars['commission_group_results']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?>
<tr>
<td><?php echo $this->_tpl_vars['commission_group_results'][$this->_sections['nr']['index']]['commission_results_date']; ?>
</td>
<td><?php echo $this->_tpl_vars['commission_group_results'][$this->_sections['nr']['index']]['commission_results_type']; ?>
</td>
<td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['commission_group_results'][$this->_sections['nr']['index']]['commission_results_amount'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
</td>
<td><a href="account.php?page=22&type=<?php echo $this->_tpl_vars['commission_group_results'][$this->_sections['nr']['index']]['commission_results_record_type']; ?>
&id=<?php echo $this->_tpl_vars['commission_group_results'][$this->_sections['nr']['index']]['commission_results_record_id']; ?>
" class="btn btn-mini btn-primary"><?php echo $this->_tpl_vars['details_details']; ?>
</a></td>
</tr>
<?php endfor; endif; ?>
</tbody>
</table>
    <?php else: ?>    
    <p><?php echo $this->_tpl_vars['details_none']; ?>
</p>
    <?php endif; ?>
	<?php else: ?>
	<p><?php echo $this->_tpl_vars['details_choose']; ?>
</p>
<?php endif; ?>