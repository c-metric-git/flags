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

<legend style="color:{$legend};">{$signup_commission_title}</legend>        
<div class="control-group">
<label for="{$signup_commission_howtopay}" class="control-label">{$signup_commission_howtopay}</label>
<div class="controls">              
 <select size="1" name="payme" class="input-large span12">
{if isset($commission_option_percentage)}
<option value="1"{$payme_selected_1}>{$signup_commission_style_PPS}: {$bot1}%</option>
{/if}
{if isset($commission_option_flatrate)}
<option value="2"{$payme_selected_2}>{$signup_commission_style_PPS}: {if $cur_sym_location == 1}{$cur_sym}{/if}{$bot2}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</option>
{/if}
{if isset($commission_option_perclick)}
<option value="3"{$payme_selected_3}>{$signup_commission_style_PPC}: {if $cur_sym_location == 1}{$cur_sym}{/if}{$bot3}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</option>
{/if}
</select>
</div>
</div>