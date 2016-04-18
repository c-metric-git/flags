<?php /* Smarty version 2.6.14, created on 2016-04-09 12:24:57
         compiled from file:account_traffic_log.tpl */ ?>

<legend style="color:<?php echo $this->_tpl_vars['legend']; ?>
;"><?php echo $this->_tpl_vars['traffic_title']; ?>
</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="6">
<div class="row-fluid">
<div class="span9">
    <div class="control-group pull-right">
        <label class="control-label" ><?php echo $this->_tpl_vars['traffic_display']; ?>
</label>
        <div class="controls">                           
            <select class="input-mini" name="cut">
            <option value="10"<?php echo $this->_tpl_vars['cut_10']; ?>
>10</option>
            <option value="25"<?php echo $this->_tpl_vars['cut_25']; ?>
>25</option>
            <option value="50"<?php echo $this->_tpl_vars['cut_50']; ?>
>50</option>
            <option value="100"<?php echo $this->_tpl_vars['cut_100']; ?>
>100</option>
    	  <option value="250"<?php echo $this->_tpl_vars['cut_250']; ?>
>250</option>
    	  <option value="500"<?php echo $this->_tpl_vars['cut_500']; ?>
>500</option>
    	</select> <?php echo $this->_tpl_vars['traffic_display_visitors']; ?>

        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="<?php echo $this->_tpl_vars['traffic_button']; ?>
">
    </div>
</div>
<?php if (isset ( $this->_tpl_vars['traffic_logs_exist'] )): ?>
<table class="table table-bordered tier">
<thead>
<tr>
<th><b><?php echo $this->_tpl_vars['traffic_ip']; ?>
</b></th>
<th><b><?php echo $this->_tpl_vars['traffic_refer']; ?>
</b></th>
<th data-hide="phone"><b><?php echo $this->_tpl_vars['traffic_date']; ?>
</b></th>
<th data-hide="phone"><b><?php echo $this->_tpl_vars['traffic_time']; ?>
</b></th>
</tr>
</thead>
<tbody>
<?php unset($this->_sections['nr']);
$this->_sections['nr']['name'] = 'nr';
$this->_sections['nr']['loop'] = is_array($_loop=$this->_tpl_vars['traffic_results']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<td><?php echo $this->_tpl_vars['traffic_results'][$this->_sections['nr']['index']]['traffic_ip']; ?>
</td>
<td><?php echo $this->_tpl_vars['traffic_results'][$this->_sections['nr']['index']]['traffic_refer']; ?>
</td>
<td><?php echo $this->_tpl_vars['traffic_results'][$this->_sections['nr']['index']]['traffic_date']; ?>
</td>
<td><?php echo $this->_tpl_vars['traffic_results'][$this->_sections['nr']['index']]['traffic_time']; ?>
</td>
</tr>
<?php endfor; endif; ?>
</tbody>
<tfoot>
	<tr>
    	<td colspan="4"><center><b><?php echo $this->_tpl_vars['traffic_bottom_tag_one']; ?>
 <?php echo $this->_tpl_vars['search_limit']; ?>
 <?php echo $this->_tpl_vars['traffic_bottom_tag_two']; ?>
 <?php echo $this->_tpl_vars['search_total']; ?>
 <?php echo $this->_tpl_vars['traffic_bottom_tag_three']; ?>
</b></center></td>
    </tr>
</tfoot>
</table>
<?php else: ?>
<p><?php echo $this->_tpl_vars['traffic_none']; ?>
</p>
<?php endif; ?>