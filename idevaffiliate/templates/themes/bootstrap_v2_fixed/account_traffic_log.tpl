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

<legend style="color:{$legend};">{$traffic_title}</legend>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="page" value="6">
<div class="row-fluid">
<div class="span9">
    <div class="control-group pull-right">
        <label class="control-label" >{$traffic_display}</label>
        <div class="controls">                           
            <select class="input-mini" name="cut">
            <option value="10"{$cut_10}>10</option>
            <option value="25"{$cut_25}>25</option>
            <option value="50"{$cut_50}>50</option>
            <option value="100"{$cut_100}>100</option>
    	  <option value="250"{$cut_250}>250</option>
    	  <option value="500"{$cut_500}>500</option>
    	</select> {$traffic_display_visitors}
        </div>
    </div>
    </div>
    <div class="span3">
        <input class="btn btn-primary" type="submit" value="{$traffic_button}">
    </div>
</div>
{if isset($traffic_logs_exist)}
<table class="table table-bordered tier">
<thead>
<tr>
<th><b>{$traffic_ip}</b></th>
<th><b>{$traffic_refer}</b></th>
<th data-hide="phone"><b>{$traffic_date}</b></th>
<th data-hide="phone"><b>{$traffic_time}</b></th>
</tr>
</thead>
<tbody>
{section name=nr loop=$traffic_results}
<tr>
<td>{$traffic_results[nr].traffic_ip}</td>
<td>{$traffic_results[nr].traffic_refer}</td>
<td>{$traffic_results[nr].traffic_date}</td>
<td>{$traffic_results[nr].traffic_time}</td>
</tr>
{/section}
</tbody>
<tfoot>
	<tr>
    	<td colspan="4"><center><b>{$traffic_bottom_tag_one} {$search_limit} {$traffic_bottom_tag_two} {$search_total} {$traffic_bottom_tag_three}</b></center></td>
    </tr>
</tfoot>
</table>
{else}
<p>{$traffic_none}</p>
{/if}