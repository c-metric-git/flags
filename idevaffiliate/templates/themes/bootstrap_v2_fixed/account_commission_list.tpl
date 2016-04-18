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

{if isset($commission_group_chosen)}
<legend style="color:{$legend};">{$commission_group_name}</legend>
{if isset($commission_results_exist)}
<table class="table table-bordered tier">
<thead>
<tr>
<th><strong>{$details_date}</strong></th>
<th><strong>{$details_status}</strong></th>
<th data-hide="phone"><strong>{$details_commission}</strong></th>
<th data-hide="phone"><strong>{$details_details}</strong></th>
</tr>
</thead>
<tbody>
{section name=nr loop=$commission_group_results}
<tr>
<td>{$commission_group_results[nr].commission_results_date}</td>
<td>{$commission_group_results[nr].commission_results_type}</td>
<td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$commission_group_results[nr].commission_results_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
<td><a href="account.php?page=22&type={$commission_group_results[nr].commission_results_record_type}&id={$commission_group_results[nr].commission_results_record_id}" class="btn btn-mini btn-primary">{$details_details}</a></td>
</tr>
{/section}
</tbody>
</table>
    {else}    
    <p>{$details_none}</p>
    {/if}
	{else}
	<p>{$details_choose}</p>
{/if}