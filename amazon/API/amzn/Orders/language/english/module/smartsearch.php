<?php
//==============================================================================
// Smart Search v154.4
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

$version = 'v154.4';

// Heading
$_['heading_title']					= 'Smart Search';

// Buttons
$_['button_save_exit']				= 'Save & Exit';
$_['button_save_keep_editing']		= 'Save & Keep Editing';
$_['button_view_report']			= 'View Smart Search History';
	
// Entries
$_['entry_smartsearch_explanation']	= 'Smart Search always returns the most relevant results, by performing a search of the selected product fields in four phases:<br />&bull; Phase 1: Finding products that contain the keywords as an exact phrase. If nothing is found, it will move on to Phase 2.<br />&bull; Phase 2: Finding products that contain ALL of the keywords, correctly spelled. If nothing is found, it will move on to Phase 3.<br />&bull; Phase 3: Finding products that contain ALL of the keywords, within the misspelling tolerance level. If nothing is found, it will move on to Phase 4.<br />&bull; Phase 4: Finding products that contain ANY of the keywords, within the misspelling tolerance level.';
$_['entry_status']					= 'Status:';
$_['entry_product_fields_searched']	= 'Product Fields Searched:<br /><br /><span class="help">Selecting "Description (Phases 3 & 4)" can slow down search results if you have a lot of products.<br /><br />Attribute fields are only available in OpenCart 1.5.x versions.</span>';
$_['entry_search_options']			= 'Search Options:';
$_['entry_misspelling_settings']	= 'Misspelling Search Settings:';
$_['entry_ajax_search_settings']	= 'AJAX Search Settings:';
$_['entry_pre_search_replacements']	= 'Pre-Search Replacements:<br /><span class="help">Optionally enter replacements for the keywords before they are searched. For example, you could replace hyphens with spaces, or commonly misspelled product names with correct ones.</span>';

// Product Fields Searched
$_['text_name']						= 'Name';
$_['text_description']				= 'Description (Phases 1 & 2)';
$_['text_description_misspelled']	= 'Description (Phases 3 & 4)';
$_['text_meta_description']			= 'Meta Tag Description';
$_['text_meta_keyword']				= 'Meta Tag Keywords';
$_['text_tag']						= 'Tags';
$_['text_model']					= 'Model';
$_['text_sku']						= 'SKU';
$_['text_upc']						= 'UPC';
$_['text_location']					= 'Location';
$_['text_manufacturer']				= 'Manufacturer Name';
$_['text_attribute_group']			= 'Attribute Group';
$_['text_attribute_name']			= 'Attribute Name';
$_['text_attribute_value']			= 'Attribute Value';
$_['text_option_name']				= 'Option Name';
$_['text_option_value']				= 'Option Value';

// Search Options
$_['button_index_database_tables']	= 'Index Database Tables';
$_['help_index_database_tables']	= 'This can help speed up search results, as described in <a target="_blank" href="http://forum.opencart.com/viewtopic.php?f=20&t=39759">this forum topic</a>. Clicking this will automatically run all of these INDEX database queries for you.';
$_['text_indexed_success']			= 'Your database tables are indexed! Running this in the future will have no affect on your database.';
$_['text_include_partial_word']		= 'Include Partial Word Matches:';
$_['help_include_partial_word']		= '(Default: Yes) Select whether searches include results where the key terms only partially match a word. For example, searches for "mac" would match products containing "macbook".';
$_['text_account_for_plurals']		= 'Account For Plurals:';
$_['help_account_for_plurals']		= '(Default: Yes) Select whether searches with simple plurals will also give results for the singular. For example, searches for "macs" would also give results for "mac".';
$_['text_search_in_subcategories']	= 'Search in Sub-categories:';
$_['help_search_in_subcategories']	= '(Default: Yes) Select whether searches limited to a category will also search within its sub-categories.';
$_['text_phase_1_search']			= 'Phase 1 Search:';
$_['help_phase_1_search']			= '(Default: Run by itself) Select whether to run the Phase 1 exact phrase search by itself, to combine its results with Phase 2, or to skip it altogether. If you choose to combine the results with Phase 2, the Phase 1 results will appear first.';
$_['text_run_by_itself']			= 'Run by itself';
$_['text_combine_with_phase_2']		= 'Combine with Phase 2';
$_['text_skip']						= 'Skip';

// Misspelling Search Settings
$_['text_misspelling_tolerance']	= 'Misspelling Tolerance:';
$_['help_misspelling_tolerance']	= '(Default: 75%) Set the tolerance level when judging misspelled words. A setting of 0% will match anything, while a setting of 100% will match only exact spellings.';
$_['text_use_cache']				= 'Use Cache:';
$_['help_use_cache']				= '(Default: No) Select whether to generate and use cache files during misspelling searches. Using a cache can improve the accuracy of the search results, but will take time when the cache is auto-refreshed. If you have a lot of products and notice the search is slow, you may want to set this to "No".';
$_['text_auto_refresh_cache']		= 'Auto-Refresh Cache:';
$_['help_auto_refresh_cache']		= '(Default: Hourly) Select how often to generate new cache files. This can take a few extra seconds during the search where they are generated, so if you have a lot of products you may want to do this infrequently.<br /><br />To manually refresh the cache, simply save your settings and then perform a search on your site.';
$_['text_hourly']					= 'Hourly';
$_['text_daily']					= 'Daily';
$_['text_weekly']					= 'Weekly';
$_['text_monthly']					= 'Monthly';
$_['text_yearly']					= 'Yearly';
$_['text_results']					= 'Results';
$_['text_speed']					= 'Speed';
$_['text_min_word_length']			= 'Min. Word Length for Cache:';
$_['help_min_word_length']			= '(Default: 3) The minimum number of letters a keyword needs for it to be included in the cache. Set a higher minimum word length if misspelled searches are matching products that include common words.';

// AJAX Search Settings
$_['text_ajax_search']				= 'AJAX Search:';
$_['help_ajax_search']				= '(Default: Enabled) If enabled, products will automatically appear when entering keywords in the header search field.';
$_['text_selector']					= 'Selector:';
$_['help_selector']					= '(Default: <span style="font-family: monospace">#header input[name="filter_name"]</span>)<br />Enter a CSS selector for the search field. You only need to change this if your custom template changes the search field.';
$_['text_display']					= 'Display:';
$_['help_display']					= 'Set how the AJAX dropdown displays:<br />&bull; (Default: 500) Delay: The delay before appearing, in ms<br />&bull; (Default: 5) Limit: The maximum number of products to show<br />&bull; (Default: Show) Price: Whether to show the product price<br />&bull; (Default: Hide) Model: Whether to show the product model<br />&bull; (Default: 100) Description: The number of characters for product descriptions (leave blank to show no description)';
$_['text_delay']					= 'Delay (ms)';
$_['text_limit']					= 'Limit (#)';
$_['text_price']					= 'Price';
$_['text_description_ajax']			= 'Description';
$_['text_show']						= 'Show';
$_['text_hide']						= 'Hide';
$_['text_sizes']					= 'Sizes:';
$_['help_sizes']					= '(Defaults: 292, 50, 50, 13, 11)<br />Set the AJAX dropdown width, image width, image height, product font size, and description font size (all in pixels). Leave image dimensions blank to show no image.';
$_['text_dropdown_width']			= 'Dropdown<br />Width';
$_['text_image_width']				= 'Image<br />Width';
$_['text_image_height']				= 'Image<br />Height';
$_['text_product_font_size']		= 'Product<br />Font Size';
$_['text_description_font_size']	= 'Description<br />Font Size';
$_['text_colors']					= 'Colors:';
$_['help_colors']					= 'Set the colors of the AJAX dropdown:<br />&bull; (Default: #FFFFFF) Background<br />&bull; (Default: #EEEEEE) Borders<br />&bull; (Default: #000000) Font<br />&bull; (Default: #EEFFFF) Highlight<br />&bull; (Default: #000000) Price<br />&bull; (Default: #FF0000) Special';
$_['text_background']				= 'Background';
$_['text_borders']					= 'Borders';
$_['text_font']						= 'Font';
$_['text_highlight']				= 'Highlight';
$_['text_special']					= 'Special';
$_['text_text']						= 'Text:';
$_['help_text']						= '(Default: "View All Results" and "No Results")<br />Set the text for the "View All Results" link that appears at the bottom of the AJAX dropdown, and the text displayed when there are no results for a search. HTML is supported.';
$_['text_view_all_results']			= 'View All Results';
$_['text_no_results']				= 'No Results';

// Pre-Search Replacements
$_['text_replace']					= 'Replace';
$_['text_with']						= 'With';

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