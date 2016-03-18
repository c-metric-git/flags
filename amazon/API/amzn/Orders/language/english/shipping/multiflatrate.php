<?php
//==============================================================================
// Multi Flat Rate Shipping v154.1
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$version = 'v154.1';

// Heading
$_['heading_title']					= 'Multi Flat Rate Shipping';

// Buttons
$_['button_save_exit']				= 'Save & Exit';
$_['button_save_keep_editing']		= 'Save & Keep Editing';
$_['button_show_examples']			= 'Show Examples';
$_['button_add_row']				= 'Add Rate';

// Entries
$_['entry_status']					= 'Status:';
$_['entry_sort_order']				= 'Sort Order:';
$_['entry_heading']					= 'Heading:<br /><span class="help">Set the heading under which these shipping options will appear. HTML is supported.</span>';
$_['entry_general_settings']		= 'General Settings';
$_['entry_cost']					= 'Cost';

// Help
$_['help_examples']					= '
	<h2 class="selected">Example 1</h2><h2>Example 2</h2><h2>Example 3</h2>
	<div id="example1">
	Suppose you want to charge a $3.00 flat rate for your Default customer group and customers that do not have an account. For your Wholesale customer group, you want to charge 10% of the taxed sub-total. Then you would enter:<br /><br />
	<strong>RATE #1</strong>
	<ul>
		<li><strong>Customer Groups:</strong> Not Logged In, Default</li>
		<li><strong>Cost:</strong> 3.00, Flat Rate</li>
	</ul>
	<strong>RATE #2</strong>
	<ul>
		<li><strong>Value for Total:</strong> Taxed Sub-Total</li>
		<li><strong>Customer Groups:</strong> Wholesale</li>
		<li><strong>Cost:</strong> 10%, Flat Rate</li>
	</ul>
	</div>
	<div id="example2" style="display: none">
	Suppose within the U.S. you want to charge a $4.00 flat rate for shipping. You want foreign currencies to be automatically calculated using your currency rates. For international locations, you want to charge 5.00 per item, in whatever currency the customer has selected. Then you would enter:<br /><br />
	<strong>RATE #1</strong>
	<ul>
		<li><strong>Currencies:</strong> Convert Unselected and US Dollar</li>
		<li><strong>Geo Zones:</strong> United States</li>
		<li><strong>Cost:</strong> 4.00, Flat Rate</li>
	</ul>
	<strong>RATE #2</strong>
	<ul>
		<li><strong>Currencies:</strong> (all except Convert Unselected)</li>
		<li><strong>Geo Zones:</strong> All Other Zones (or your international geo zones)</li>
		<li><strong>Cost:</strong>5.00, Per Item</li>
	</ul>
	</div>
	<div id="example3" style="display: none">
	Suppose you want to give three options to your customers: Free Shipping (5-10 days), Regular Shipping (3-5 days) charged at $1.50 per item, and Express Shipping (1-2 days) charged at 5% of the sub-total per item. Then you would enter:<br /><br />
	<strong>RATE #1</strong>
	<ul>
		<li><strong>Title:</strong> Free Shipping (5-10 days)</li>
		<li><strong>Cost:</strong>0.00, Flat Rate</li>
	</ul>
	<strong>RATE #2</strong>
	<ul>
		<li><strong>Title:</strong> Regular Shipping (3-5 days)</li>
		<li><strong>Cost:</strong> 1.50, Per Item</li>
	</ul>
	<strong>RATE #3</strong>
	<ul>
		<li><strong>Title:</strong> Express Shipping (1-2 days)</li>
		<li><strong>Value for Total:</strong> Sub-Total</li>
		<li><strong>Cost:</strong> 5%, Per Item</li>
	</ul>
	</div>';
$_['help_general_settings']			= '
	<em>Title</em><br />
	Set the rate title for each language. HTML is supported.<br /><br />
	<em>Tax Class</em><br />
	Optionally select a tax class for the rate. If applying a tax class, be sure to set the Sort Order for the "Shipping" Order Total to something lower than the "Taxes" Order Total.<br /><br />
	<em>Value for Total</em><br />
	Select the value used for percentage charges: the cart\'s Sub-Total, Taxed Sub-Total, or Total (at the relative Sort Order of the "Shipping" Order Total). Products that do not require shipping are NOT included in the total.';
$_['help_stores']					= 'Select the stores for which the rate is available.';
$_['help_currencys']				= 'Select the currencies for which the rate is available. "Convert Unselected" will automatically convert the charge for unselected currencies, based on your currency rates.';
$_['help_customer_groups']			= 'Select the customer groups for which the rate is available. "Not Logged In" will make the rate available to any customer not currently logged in.';
$_['help_geo_zones']				= 'Select the geo zones for which the rate is available. "All Other Zones" will make the rate available to all locations not within a geo zone.';
$_['help_cost']						= 'Enter the shipping charge, as a decimal (for example, 5.75) or as a percentage of the total (for example, 15%). Also select whether to charge the cost as a Flat Rate, or Per Item.';
$_['help_actions']					= '<em>Remove</em><br />Click the red minus button to remove the rate. Note that if it is the last rate remaining, it will instead be cleared of all values.<br /><br /><em>Copy</em><br />Click the gray and blue button to copy the rate to the end of the list.';

// General Settings
$_['text_title']					= 'Title';
$_['text_tax_class']				= 'Tax Class';
$_['text_value_for_total']			= 'Value for Total';
$_['text_subtotal']					= 'Sub-Total';
$_['text_taxed_subtotal']			= 'Taxed Sub-Total';
$_['text_total']					= 'Total';

// Order Criteria
$_['text_stores']					= 'Stores';
$_['text_currencys']				= 'Currencies';
$_['text_convert_unselected']		= '<em>Convert Unselected</em>';
$_['text_customer_groups']			= 'Customer Groups';
$_['text_not_logged_in']			= '<em>Not Logged In</em>';
$_['text_geo_zones']				= 'Geo Zones';
$_['text_all_other_zones']			= '<em>All Other Zones</em>';

// Cost
$_['text_flat_rate']				= 'Flat Rate';
$_['text_per_item']					= 'Per Item';

// Copyright
$_['copyright']						= '<div style="text-align: center" class="help">' . $_['heading_title'] . ' ' . $version . ' &copy; <a target="_blank" href="http://www.getclearthinking.com">Clear Thinking, LLC</a></div>';

// Standard Text
$_['standard_module']				= 'Modules';
$_['standard_shipping']				= 'Shipping';
$_['standard_payment']				= 'Payments';
$_['standard_total']				= 'Order Totals';
$_['standard_feed']					= 'Product Feeds';
$_['standard_success']				= 'Success: You have modified ' . $_['heading_title'] . '!';
$_['standard_error']				= 'Warning: You do not have permission to modify ' . $_['heading_title'] . '!';
?>