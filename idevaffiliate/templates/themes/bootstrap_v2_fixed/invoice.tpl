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

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$char_set}">
<title>{$sitename} - {$header_title}</title>
<link rel="stylesheet" type="text/css" href="{$theme_folder}/css/style.css">
<link rel="shortcut icon" href="{$theme_folder}/images/favicon.ico">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="600" align="center">
<tr>
<td width="100%" align="right"><b><font size="4">{$invoice_header}</font></b></td>
</tr>
<tr>
<td width="100%" height="5"></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice" align="center">
<tr>
<td width="50%"><b>{$invoice_aff_info}</b></td>
<td width="50%"><b>{$sitename} {$invoice_co_info}</b></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice_data" align="center">
<tr>
<td width="50%" valign="top">{$invoice_affiliate_payto}{$invoice_affiliate_fname}{$invoice_affiliate_lname}{$invoice_affiliate_address1}{$invoice_affiliate_address2}{$invoice_affiliate_city}{$invoice_affiliate_state}{$invoice_affiliate_zip}Country: {$invoice_affiliate_country}</td>
<td width="50%" valign="top">{$invoice_our_company}{$invoice_our_address1}{$invoice_our_address2}{$invoice_our_city}{$invoice_our_state}{$invoice_our_zip}Country: {$invoice_our_country}</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice" align="center">
<tr>
<td width="100%"><b>{$invoice_acct_info}</b></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice_data" align="center">
<tr>
<td width="25%">{$invoice_aff_id}</td>
<td width="25%">{$invoice_affiliate_id}</td>
<td width="50%" rowspan="4" align="center" valign="middle"><a href="#" onClick="window.print();return false;"><img border="0" src="{$theme_folder}/images/print_invoice.gif" width="31" height="31"></a><br /><a href="#" onClick="window.print();return false;">{$invoice_print}</a></td>
</tr>
<tr>
<td width="25%">{$invoice_aff_user}</td>
<td width="25%">{$invoice_username}</td>
</tr>
<tr>
<td width="25%">{$edit_personal_phone}</td>
<td width="25%">{$invoice_phone}</td>
</tr>
<tr>
<td width="25%">{$edit_standard_taxinfo}</td>
<td width="25%">{$invoice_taxinfo}</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice" align="center">
<tr>
<td width="100%"><b>{$invoice_payment_info}</b></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice_data" align="center">
<tr>
<td width="25%">{$comdetails_date}</td>
<td width="75%">{$invoice_date}</td>
</tr>
<tr>
<td width="25%">{$invoice_comm_amt}</td>
<td width="75%"><font color="#CC0000">{if $cur_sym_location == 1}{$cur_sym}{/if}{$invoice_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</font></td>
</tr>
{if isset($vat_amount)}
<tr>
<td width="25%">VAT Amount</td>
<td width="75%">{if $cur_sym_location == 1}{$cur_sym}{/if}{$vat_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
</tr>
{/if}
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice" align="center">
<tr>
<td width="25%"><b>{$comdetails_date}</b></td>
<td width="25%"><b>{$invoice_comm_amt}</b></td>
<td width="50%"><b>{$invoice_comm_type}</b></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice_data" align="center">
{section name=nr loop=$payment_results}
<tr>
<td width="25%">{$payment_results[nr].payment_individual_date}</td>
<td width="25%">{if $cur_sym_location == 1}{$cur_sym}{/if}{$payment_results[nr].payment_individual_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</td>
<td width="50%">{$payment_results[nr].payment_individual_type}</td>
</tr>
{/section}
<tr>
<td width="25%">{$invoice_comm_amt}</td>
<td width="75%" colspan="2"><font color="#CC0000">{if $cur_sym_location == 1}{$cur_sym}{/if}{$invoice_amount}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</font></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice" align="center">
<tr>
<td width="100%"><b>{$invoice_admin_note}</b></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" class="invoice_data" align="center">
<tr>
<td width="100%">{$invoice_note}</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="600" align="center">
<tr>
<td width="100%" align="right">{$invoice_footer}</td>
</tr>
</table>
</body>
</html>