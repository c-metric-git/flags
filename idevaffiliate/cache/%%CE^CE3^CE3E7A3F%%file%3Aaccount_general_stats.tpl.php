<?php /* Smarty version 2.6.14, created on 2016-04-09 12:14:56
         compiled from file:account_general_stats.tpl */ ?>

<div class="span6">
<table class="table table-bordered">
<tr>
<td width="55%" colspan="2"><strong><?php echo $this->_tpl_vars['general_title']; ?>
</strong></td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_transactions']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['current_transactions']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_standard_earnings']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['current_approved_commissions']; ?>

</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['account_second_tier']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['current_tier_commissions']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['account_recurring']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['current_recurring_commissions']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_current_earnings']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['current_total_commissions']; ?>
</td>
</tr>
<tr>
<td width="55%" colspan="2"><strong><?php echo $this->_tpl_vars['general_traffic_title']; ?>
</strong></td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_traffic_visitors']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['hin']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_traffic_unique']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['unchits']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_traffic_sales']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['salenum']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_traffic_ratio']; ?>
</td>
<td width="25%"><?php echo $this->_tpl_vars['perc']; ?>
%</td>
</tr>
</table>
</div>
<div class="span6 chart-height">
<?php if (isset ( $this->_tpl_vars['traffic_exists'] )): ?>
	<?php echo '
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/jquery.flot.categories.js"></script>
	'; ?>

	

	<?php echo $this->_tpl_vars['chart_unique_hits']; ?>

	<?php echo $this->_tpl_vars['chart_total_sales']; ?>

	
	<?php echo '
	<script type="text/javascript">
		$(function() {
			var data = [ [" ",0] ,["Unique Hits", $(".uniqueHits").text()], ["Total Sales", $(".totalSales").text()], ["&nbsp;",0]];
			$.plot("#placeholder", [ data ], {
				series: {
					bars: {
						show: true,
						barWidth: 0.8,
						lineWidth:1,
						align:\'center\',
						fillColor: "#c8d7ff",
					}
					
				}, 
				xaxis: {
					mode: "categories",
					tickLength: 0
				},grid: {
                    borderColor: "#DDDDDD", 
                    borderWidth: 1
                }
			});
		});
	</script>
	'; ?>

<?php endif; ?>
<div id="placeholder" style="width:100%; height:100%;"  ></div>
</div>
<table class="table table-bordered">
<tr>
<td width="30%"><?php echo $this->_tpl_vars['general_traffic_pay_type']; ?>
</td>
<td width="70%"><?php echo $this->_tpl_vars['general_traffic_pay_level']; ?>
</td>
</tr>
<tr>
<td width="30%"><?php echo $this->_tpl_vars['current_style']; ?>
</td>
<td width="70%"><?php echo $this->_tpl_vars['current_level']; ?>
</td>
</tr>
</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'file:account_notes.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>