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

<legend style="color:{$legend};">{$recurring_title}</legend>
{if isset($recurring_enabled)}
    {if isset($recurring_commissions_exist)}
    <table class="table table-bordered tier">
	<thead>
    <tr>
    <th><b>{$recurring_date}</b></th>
    <th><b>{$recurring_status}</b></th>
    <th data-hide="phone"><b>{$recurring_payout}</b></th>
    <th data-hide="phone"><b>{$sub_tracking_id}</b></th>
    <th data-hide="phone"><b>{$recurring_amount}</b></th>
    </tr>
    </thead>
	<tbody>
    {section name=nr loop=$recurring_list_results}
    <tr {if $smarty.section.nr.iteration is even} {else}{/if}>
    <td>{$recurring_list_results[nr].recurring_results_date}</td>
    <td>{$recurring_every} {$recurring_list_results[nr].recurring_results_duration} {$recurring_days}</td>
    <td>{$recurring_in} {$recurring_list_results[nr].recurring_results_next} {$recurring_days}</td>
    <td>{$recurring_list_results[nr].recurring_results_subid}</td>
    <td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$recurring_list_results[nr].recurring_results_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
    </tr>
    {/section}  
    </tbody>
    <tfoot>  
    <tr>
        <td data-hide="phone">&nbsp;</td><td data-hide="phone">&nbsp; </td><td data-hide="phone">&nbsp; </td>
        <td style="text-align:right;"><b>{$recurring_total}</b></td>
        <td><font color="#CC0000"><b>{if $cur_sym_location == 1}{$cur_sym}{/if}{$recurring_total_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</b></font></td>
    </tr>
	</tfoot>
    </table>
    <table class="table table-bordered onlyMobile">
    	<tr>
        	<td><b>{$recurring_total}</b></td>
              <td><font color="#CC0000"><b>{if $cur_sym_location == 1}{$cur_sym}{/if}{$recurring_total_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</b></font></td>
        </tr>
    </table>
    {else}
    <p>{$recurring_none}</p>
    {/if}
{/if}