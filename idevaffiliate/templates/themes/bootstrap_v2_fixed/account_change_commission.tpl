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

{if isset($change_commission)}
<legend style="color:{$legend};">{$change_comm_page_title}</legend>  
{if isset($commission_updated)}
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>    
{$commission_updated}
</div> 
{else}
{/if}
<table class="table table-bordered">
<tr>
<td width="25%">{$change_comm_page_curr_comm}</td>
<td width="75%">{$current_style}</td>
</tr>
<tr>
<td width="25%">{$change_comm_page_curr_pay}</td>
<td width="75%">{$current_level}</td>
</tr>
</table>
<form method="POST" action="account.php" class="form-horizontal">
<input type="hidden" name="changec" value="1">
<input type="hidden" name="page" value="19">
{if isset($available)}
    <div class="control-group">
        <label for="{$change_comm_page_new_comm}" class="control-label">{$change_comm_page_new_comm}</label>
        <div class="controls">                  
         <select size="1" name="type">
        {if isset($type_perc)}<option value="1">{$index_table_sale}: {$bot1}%</option>{/if}
        {if isset($type_flat)}<option value="2">{$index_table_sale}: {if $cur_sym_location == 1}{$cur_sym}{/if}{$bot2}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</option>{/if}
        {if isset($type_clck)}<option value="3">{$index_table_click}: {if $cur_sym_location == 1}{$cur_sym}{/if}{$bot3}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency}</option>{/if}
        </select>
        </div>
    </div>  
     <div class="control-group">
    <label for="" class="control-label"></label>
    <div class="controls">
       <input class="btn btn-primary" type="submit" value="{$change_comm_page_button}"/>
    </div>
    <div class="control-group" style="padding-top:20px;">
        <div class="well">{$change_comm_page_warning}</well>
    </div> 
</div>
    </form>
{else}
                
         <div class="well">{$no_styles_available}</div>
{/if}
{/if}