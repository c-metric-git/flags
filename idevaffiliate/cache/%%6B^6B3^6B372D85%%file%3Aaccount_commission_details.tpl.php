<?php /* Smarty version 2.6.14, created on 2016-04-09 12:49:31
         compiled from file:account_commission_details.tpl */ ?>

<h4><?php echo $this->_tpl_vars['comdetails_title']; ?>
</h4>
<table class="table table-bordered">
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_date']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_date']; ?>
</td>
</tr>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_time']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_time']; ?>
</td>
</tr>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_type']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_type']; ?>
</td>
</tr>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_status']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_status']; ?>
</td>
</tr>
<tr>
<td width="25%" ><b>&nbsp;<?php echo $this->_tpl_vars['comdetails_amount']; ?>
</b></td>
<td width="75%" >&nbsp;<b><?php echo $this->_tpl_vars['commission_details_payment']; ?>
</b></td>
</tr>
</table>
<?php if (isset ( $this->_tpl_vars['commission_details_show_extras'] )): ?>
<h4><?php echo $this->_tpl_vars['comdetails_additional_title']; ?>
</h4>

<table class="table table-bordered">
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_additional_ordnum']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_extras_ordernum']; ?>
</td>
</tr>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['comdetails_additional_saleamt']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_extras_saleamount']; ?>
</td>
</tr>
<?php if (isset ( $this->_tpl_vars['commission_details_optional_one'] )): ?>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['commission_details_optional_name_one']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_optional_value_one']; ?>
</td>
</tr>
<?php endif;  if (isset ( $this->_tpl_vars['commission_details_optional_two'] )): ?>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['commission_details_optional_name_two']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_optional_value_two']; ?>
</td>
</tr>
<?php endif;  if (isset ( $this->_tpl_vars['commission_details_optional_three'] )): ?>
<tr>
<td width="25%"><?php echo $this->_tpl_vars['commission_details_optional_name_three']; ?>
</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_optional_value_three']; ?>
</td>
</tr>
<?php endif; ?>
</table>
<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['sub_affiliates_enabled'] ) || isset ( $this->_tpl_vars['custom_links_enabled'] )): ?>
<h4><?php echo $this->_tpl_vars['sub_tracking_title']; ?>
</h4>
<table class="table table-bordered">
<?php if (isset ( $this->_tpl_vars['sub_affiliates_enabled'] )): ?>
<tr>
<td width="25%" ><b><?php echo $this->_tpl_vars['sub_tracking_id']; ?>
</b></td>
<td width="75%" ><b><?php echo $this->_tpl_vars['commission_details_subid']; ?>
</b></td>
</tr>
<?php endif;  if (isset ( $this->_tpl_vars['custom_links_enabled'] )): ?>
<tr>
<td width="25%">TID1</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_tid1']; ?>
</td>
</tr>
<tr>
<td width="25%">TID2</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_tid2']; ?>
</td>
</tr>
<tr>
<td width="25%">TID3</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_tid3']; ?>
</td>
</tr>
<tr>
<td width="25%">TID4</td>
<td width="75%"><?php echo $this->_tpl_vars['commission_details_tid4']; ?>
</td>
</tr>
<?php endif; ?>
</table>
<?php endif; ?>