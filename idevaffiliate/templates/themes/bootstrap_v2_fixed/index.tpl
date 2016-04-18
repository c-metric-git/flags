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

{include file='file:header.tpl'}
<div class="container">
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span8">
            <h4>{$index_heading_1}</h4>
            <p>{$index_paragraph_1}</p>
            <h4>{$index_heading_2}</h4>
            <p>{$index_paragraph_2}</p>
            <h4>{$index_heading_3}</h4>
            <p>{$index_paragraph_3}</p>
        </div>
        <div class="span4">
		
            <div class="well"><legend>{$index_login_title}</legend>
{if !isset($affiliateUsername)}
                <form method="POST" action="login.php">       
                    <fieldset>                        
                        <div class="control-group">
                                <div class="controls">                           
                                    <input class="span12" placeholder="Username" type="text" name="userid" size="10" value="{$index_login_username_field}"/>
                                </div>
                        </div> 
                        <div class="control-group">
                                <div class="controls">                           
                                    <input class="span12" type="password" placeholder="Password" name="password" size="10" value="{$index_login_password_field}" autocomplete="off"/>
                                </div>
                        </div> 
                        <input class="btn btn-primary btn-block" type="submit" value="{$index_login_button}"/>     
<input name="token_affiliate_login" value="{$login_token}" type="hidden" />				
                    </fieldset>       
                </form>
{else}
<a href="account.php" class="btn btn-primary btn-block">{$header_accountLink}</a>
{/if}
            </div>
        </div>
        </div>
        
        <div class="padding"></div>
        <div class="row-fluid">                        
                <table class="table table-bordered" style="color:{$text_color};">
                <thead>
                    <tr>
                      <th>{$index_table_title}</th>
                      <th></th>
                    </tr>
                 </thead>                    
                    <tr><td>{$index_table_commission_type}</td>
                    <td>{$commission_type_info}</td>
                    </tr>                    
                    {* The following IF statements are only used if allowing affiliates to choose commission type.
                       ------------------------------------------------------------------------------------------- *}
                    {if isset($choose_percentage_payout)}
                    <tr><td>{$index_table_sale}:</td><td>{$bot1}% {$index_table_sale_text}</td></tr>
                    {/if}
                    
                    {if isset($choose_flatrate_payout)}
                    <tr><td>{$index_table_sale}:</td><td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$bot2}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency} {$index_table_sale_text}</td></tr>
                    {/if}
                    
                    {if isset($choose_perclick_payout)}
                    <tr><td>{$index_table_click}:</td><td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$bot3}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency} {$index_table_click_text}</td></tr>
                    {/if}
                    
                    {if isset($payout_add_small_row)}
                    
                    {/if}
                    
                    {* ---------------------------------------------------------------------------------------
                       The above IF statements are only used if allowing affiliates to choose commission type. *}
                    
                    {if isset($add_balance_row)}
                    <tr><td>{$index_table_initial_deposit}</td><td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$init_deposit}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency} - <font color="#CC0000"><b>{$index_table_deposit_tag}</b></font></td></tr>
                    {/if}
                    
                    {if isset($add_requirements_row)}
                    <tr><td>{$index_table_requirements}</td><td>{if $cur_sym_location == 1}{$cur_sym}{/if}{$init_req}{if $cur_sym_location == 2} {$cur_sym}{/if} {$currency} - {$index_table_requirements_tag}</td></tr>
                    {/if}
                    
                    <tr>
                    <td>{$index_table_duration}</td><td>{$index_table_duration_tag}</td>
                    </tr>
                </table>
           
        </div>
    </div>
</div>
    {include file='file:footer.tpl'}