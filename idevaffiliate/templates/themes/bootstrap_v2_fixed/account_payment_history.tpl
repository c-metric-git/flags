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

<legend style="color:{$legend};">{$payment_title}</legend>
{if isset($payment_history_exists)}

<table class="table table-bordered tier">
<thead>
<tr>
<th><b>{$payment_date}</b></th>
<th><b>{$payment_commissions}</b></th>
<th data-hide="phone"><b>{$payment_amount}</b></th>
{if $invoice_enabled}<th data-hide="phone"></th>{/if}
</tr>
</thead>
<tbody>
{section name=nr loop=$payment_results}
<!--{if $smarty.section.nr.iteration is even} {else}bgcolor="#dfe8ff"{/if}-->

<form method="post" action="invoice.php" target="_blank">
<tr>
<td>{$payment_results[nr].payment_date}</td>
<td>{$payment_results[nr].payment_total}</td>
<td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$payment_results[nr].payment_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
{if $invoice_enabled}
<td align="center">
<input type="hidden" name="stamp" value="{$payment_results[nr].payment_stamp}">
<input class="btn btn-mini" type="submit" value="{$invoice_button}" name="print_invoice">
</td>
{/if}
</tr>
</form>

{/section}
<tr>
<td><b>{$payment_totals}</b></td>
<td><b>{$payments_total}</b></td>
<td><font color="#CC0000"><b>{if $cur_sym_location == 1}{$cur_sym}{/if}{$payments_archived}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</b></font></td>
{if $invoice_enabled}<td></td>{/if}
</tr>
</tbody>
</table>

{else}
<p>{$payment_none}</p>
{/if}