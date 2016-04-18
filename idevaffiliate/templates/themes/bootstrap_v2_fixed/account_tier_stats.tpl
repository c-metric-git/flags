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

{if isset($tier_enabled)}
<h3>{$tier_stats_title}</h3>
<table class="table table-bordered">
    <tr>
        <td>{$tier_stats_accounts}</td>
        <td>{$number_of_tier_accounts}</td>
        <td align="center"><a href="account.php?page=12" class="btn btn-mini btn-primary">{$tier_stats_grab_link}</a></td>
    </tr>
</table>
{if isset($tier_accounts_exist)}
<table class="table table-bordered tier">
   <thead>
    <tr>
        <th><strong>{$tier_stats_username}</strong></th>
        <th><strong>{$tier_stats_current}</strong></th>
        <th data-hide="phone"><strong>{$tier_stats_previous}</strong></th>
        <th data-hide="phone"><strong>{$tier_stats_totals}</strong></th>
    </tr>
    
</thead>
<tbody>
{section name=nr loop=$tier_results}
{if isset($display_tier_contact_info)}
    <tr>
        <td><a href="mailto:{$tier_results[nr].tier_email}">{$tier_results[nr].tier_username}</a>&nbsp;</td>
        <td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_current_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
        <td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_archived_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
        <td><strong>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_total_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</strong></td>
    </tr>
{else}
    <tr>
        <td>{$tier_results[nr].tier_username}&nbsp;</td>
        <td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_current_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
        <td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_archived_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
        <td><strong>{if $cur_sym_location == 1}{$cur_sym}{/if}{$tier_results[nr].tier_total_payments}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</strong></td>
    </tr>
{/if}
{/section}
</tbody>
</table>
{/if}
{/if}