<?php /* Smarty version 2.6.14, created on 2016-04-09 13:01:44
         compiled from file:account_payment_history.tpl */ ?>

<legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['payment_title']; ?>
</legend>
<?php if (isset ( $this->_tpl_vars['payment_history_exists'] )): ?>

<table class="table table-bordered tier">
<thead>
<tr>
<th><b><?php echo $this->_tpl_vars['payment_date']; ?>
</b></th>
<th><b><?php echo $this->_tpl_vars['payment_commissions']; ?>
</b></th>
<th data-hide="phone"><b><?php echo $this->_tpl_vars['payment_amount']; ?>
</b></th>
<?php if ($this->_tpl_vars['invoice_enabled']): ?><th data-hide="phone"></th><?php endif; ?>
</tr>
</thead>
<tbody>
<?php unset($this->_sections['nr']);
$this->_sections['nr']['name'] = 'nr';
$this->_sections['nr']['loop'] = is_array($_loop=$this->_tpl_vars['payment_results']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<!--<?php if (!(1 & $this->_sections['nr']['iteration'])): ?> <?php else: ?>bgcolor="#dfe8ff"<?php endif; ?>-->

<form method="post" action="invoice.php" target="_blank">
<tr>
<td><?php echo $this->_tpl_vars['payment_results'][$this->_sections['nr']['index']]['payment_date']; ?>
</td>
<td><?php echo $this->_tpl_vars['payment_results'][$this->_sections['nr']['index']]['payment_total']; ?>
</td>
<td><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['payment_results'][$this->_sections['nr']['index']]['payment_amount'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
</td>
<?php if ($this->_tpl_vars['invoice_enabled']): ?>
<td align="center">
<input type="hidden" name="stamp" value="<?php echo $this->_tpl_vars['payment_results'][$this->_sections['nr']['index']]['payment_stamp']; ?>
">
<input class="btn btn-mini" type="submit" value="<?php echo $this->_tpl_vars['invoice_button']; ?>
" name="print_invoice">
</td>
<?php endif; ?>
</tr>
</form>

<?php endfor; endif; ?>
<tr>
<td><b><?php echo $this->_tpl_vars['payment_totals']; ?>
</b></td>
<td><b><?php echo $this->_tpl_vars['payments_total']; ?>
</b></td>
<td><font color="#CC0000"><b><?php if ($this->_tpl_vars['cur_sym_location'] == 1):  echo $this->_tpl_vars['cur_sym'];  endif;  echo $this->_tpl_vars['payments_archived'];  if ($this->_tpl_vars['cur_sym_location'] == 2): ?> <?php echo $this->_tpl_vars['cur_sym'];  endif; ?> <?php echo $this->_tpl_vars['currency']; ?>
</b></font></td>
<?php if ($this->_tpl_vars['invoice_enabled']): ?><td></td><?php endif; ?>
</tr>
</tbody>
</table>

<?php else: ?>
<p><?php echo $this->_tpl_vars['payment_none']; ?>
</p>
<?php endif; ?>