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

{if isset($vanity_codes)}
{*
Enabling this HTML code without having the addon module will produce the form for the affiliate
but processing will not work.  The addon module is required for this feature.
*}
<legend style="color:{$legend};">{$cc_vanity_title}</legend>
{if isset($cc_display_edit_errors)}
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
{$cc_error_list}
</div>
{/if}
{if isset($cc_creation_success)}
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
{$cc_creation_success}
</div>
{/if}
<form class="form-horizontal" action="account.php" method="POST">
<input type="hidden" name="page" value="44">
<fieldset>
              <div class="control-group">
                <label class="control-label">{$cc_vanity_field}</label>
                <div class="controls">           
                  <input type="text" class="input-xlarge span8" name="coupon_code_request" value="">
                </div>
</div>
        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
               <input type="submit" class="btn-primary btn" value="{$cc_vanity_button}"/>
            </div>            
        </div> 
</fieldset>
</form>
{/if}
{if isset($coupon_query_exists)}
<legend style="color:{$legend};">{$coupon_title}</legend>
<p>{$coupon_desc}</p>
<br />
<table class="table table-bordered tier">
<thead>
<tr>
<th><b>{$coupon_head_1}</b></th>
<th><b>{$coupon_head_2}</b></th>
<th><b>{$coupon_head_3}</b></th>
</tr>
</thead>
<tbody>
{section name=nr loop=$coupon_results}
<tr>
<td><font color="#CC0000">{$coupon_results[nr].coupon_code}</font></td>
<td>{$coupon_results[nr].discount_amount}</td>
<td>{$coupon_results[nr].coupon_amount}</td>
</tr>
{/section}
</tbody>
</table>
{else}
<legend style="color:{$legend};">{$coupon_title}</legend>
{$coupon_none}
{/if}