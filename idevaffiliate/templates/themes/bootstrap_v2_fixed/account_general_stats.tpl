{*
-------------------------------------------------------
	iDevAffiliate HTML Front-End Template
-------------------------------------------------------
	Template   : Bootstrap 2 - Fixed Width Responsive
-------------------------------------------------------
	Copyright  : iDevDirect.com LLC
	Website    : www.idevdirect.com
-------------------------------------------------------
*}

<div class="span6">
<table class="table table-bordered">
<tr>
<td width="55%" colspan="2"><strong>{$general_title}</strong></td>
</tr>
<tr>
<td width="30%">{$general_transactions}</td>
<td width="25%">{$current_transactions}</td>
</tr>
<tr>
<td width="30%">{$general_standard_earnings}</td>
<td width="25%">{$current_approved_commissions}
</td>
</tr>
<tr>
<td width="30%">{$account_second_tier}</td>
<td width="25%">{$current_tier_commissions}</td>
</tr>
<tr>
<td width="30%">{$account_recurring}</td>
<td width="25%">{$current_recurring_commissions}</td>
</tr>
<tr>
<td width="30%">{$general_current_earnings}</td>
<td width="25%">{$current_total_commissions}</td>
</tr>
<tr>
<td width="55%" colspan="2"><strong>{$general_traffic_title}</strong></td>
</tr>
<tr>
<td width="30%">{$general_traffic_visitors}</td>
<td width="25%">{$hin}</td>
</tr>
<tr>
<td width="30%">{$general_traffic_unique}</td>
<td width="25%">{$unchits}</td>
</tr>
<tr>
<td width="30%">{$general_traffic_sales}</td>
<td width="25%">{$salenum}</td>
</tr>
<tr>
<td width="30%">{$general_traffic_ratio}</td>
<td width="25%">{$perc}%</td>
</tr>
</table>
</div>
<div class="span6 chart-height">
{if isset($traffic_exists)}
	{literal}
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="templates/themes/bootstrap_v2_fixed/flot/jquery.flot.categories.js"></script>
	{/literal}
	

	{$chart_unique_hits}
	{$chart_total_sales}
	
	{literal}
	<script type="text/javascript">
		$(function() {
			var data = [ [" ",0] ,["Unique Hits", $(".uniqueHits").text()], ["Total Sales", $(".totalSales").text()], ["&nbsp;",0]];
			$.plot("#placeholder", [ data ], {
				series: {
					bars: {
						show: true,
						barWidth: 0.8,
						lineWidth:1,
						align:'center',
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
	{/literal}
{/if}
<div id="placeholder" style="width:100%; height:100%;"  ></div>
</div>
<table class="table table-bordered">
<tr>
<td width="30%">{$general_traffic_pay_type}</td>
<td width="70%">{$general_traffic_pay_level}</td>
</tr>
<tr>
<td width="30%">{$current_style}</td>
<td width="70%">{$current_level}</td>
</tr>
</table>
{include file='file:account_notes.tpl'}