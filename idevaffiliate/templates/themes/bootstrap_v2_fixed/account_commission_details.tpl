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

<h4>{$comdetails_title}</h4>
<table class="table table-bordered">
<tr>
<td width="25%">{$comdetails_date}</td>
<td width="75%">{$commission_details_date}</td>
</tr>
<tr>
<td width="25%">{$comdetails_time}</td>
<td width="75%">{$commission_details_time}</td>
</tr>
<tr>
<td width="25%">{$comdetails_type}</td>
<td width="75%">{$commission_details_type}</td>
</tr>
<tr>
<td width="25%">{$comdetails_status}</td>
<td width="75%">{$commission_details_status}</td>
</tr>
<tr>
<td width="25%" ><b>&nbsp;{$comdetails_amount}</b></td>
<td width="75%" >&nbsp;<b>{$commission_details_payment}</b></td>
</tr>
</table>
{if isset($commission_details_show_extras)}
<h4>{$comdetails_additional_title}</h4>

<table class="table table-bordered">
<tr>
<td width="25%">{$comdetails_additional_ordnum}</td>
<td width="75%">{$commission_details_extras_ordernum}</td>
</tr>
<tr>
<td width="25%">{$comdetails_additional_saleamt}</td>
<td width="75%">{$commission_details_extras_saleamount}</td>
</tr>
{if isset($commission_details_optional_one)}
<tr>
<td width="25%">{$commission_details_optional_name_one}</td>
<td width="75%">{$commission_details_optional_value_one}</td>
</tr>
{/if}
{if isset($commission_details_optional_two)}
<tr>
<td width="25%">{$commission_details_optional_name_two}</td>
<td width="75%">{$commission_details_optional_value_two}</td>
</tr>
{/if}
{if isset($commission_details_optional_three)}
<tr>
<td width="25%">{$commission_details_optional_name_three}</td>
<td width="75%">{$commission_details_optional_value_three}</td>
</tr>
{/if}
</table>
{/if}

{if isset($sub_affiliates_enabled) || isset($custom_links_enabled)}
<h4>{$sub_tracking_title}</h4>
<table class="table table-bordered">
{if isset($sub_affiliates_enabled)}
<tr>
<td width="25%" ><b>{$sub_tracking_id}</b></td>
<td width="75%" ><b>{$commission_details_subid}</b></td>
</tr>
{/if}
{if isset($custom_links_enabled)}
<tr>
<td width="25%">TID1</td>
<td width="75%">{$commission_details_tid1}</td>
</tr>
<tr>
<td width="25%">TID2</td>
<td width="75%">{$commission_details_tid2}</td>
</tr>
<tr>
<td width="25%">TID3</td>
<td width="75%">{$commission_details_tid3}</td>
</tr>
<tr>
<td width="25%">TID4</td>
<td width="75%">{$commission_details_tid4}</td>
</tr>
{/if}
</table>
{/if}