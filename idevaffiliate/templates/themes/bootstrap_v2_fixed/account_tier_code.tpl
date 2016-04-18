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

{if isset($tier_enabled)}
<h3>{$tlinks_title}</h3>
{if isset($forced_links)}
<div class="well" style="color:{$gb_text_color};">{$tlinks_forced_two}</div>
{else}
<div class="well" style="color:{$gb_text_color};">{$tlinks_embedded_two}</div>
<table class="table table-bordered">
    <tr>
      <td width="35%"><strong>{$tlinks_forced_code}</strong></td>
      <td width="65%">{$tlinks_embedded_one}</td>
    </tr>
  </table>
{/if}
{if isset($forced_links)}
{if isset($seo_links)}
<textarea rows="1" style="background-color:#f2f6ff;" class="input-block-level"><a href="{$seo_url}signup-{$textads_link}{$textads_link_html_added}">{$tlinks_forced_money}</a></textarea>
{else}
<textarea rows="1" style="background-color:#f2f6ff;" class="input-block-level"><a href="{$base_url}/index.php?ref={$link_id}">{$tlinks_forced_money}</a></textarea>
{/if}
{$tlinks_forced_paste}
{/if}
<h4 style="padding-top:15px;">{$tlinks_payout_structure}<span class="pull-right">{$tlinks_active}: {$tier_numbers}</span></h4>
<table class="table table-bordered">
{if isset($tier_1_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 1</strong></td>
      <td width="75%">{$tier_1_amount}{$tier_1_type}</td>
    </tr>
{/if}
{if isset($tier_2_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 2</strong></td>
      <td width="75%">{$tier_2_amount}{$tier_2_type}</td>
    </tr>
{/if}
{if isset($tier_3_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 3</strong></td>
      <td width="75%">{$tier_3_amount}{$tier_3_type}</td>
    </tr>
{/if}
{if isset($tier_4_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 4</strong></td>
      <td width="75%">{$tier_4_amount}{$tier_4_type}</td>
    </tr>
{/if}
{if isset($tier_5_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 5</strong></td>
      <td width="75%">{$tier_5_amount}{$tier_5_type}</td>
    </tr>
{/if}
{if isset($tier_6_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 6</strong></td>
      <td width="75%">{$tier_6_amount}{$tier_6_type}</td>
    </tr>
{/if}
{if isset($tier_7_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 7</strong></td>
      <td width="75%">{$tier_7_amount}{$tier_7_type}</td>
    </tr>
{/if}
{if isset($tier_8_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 8</strong></td>
      <td width="75%">{$tier_8_amount}{$tier_8_type}</td>
    </tr>
{/if}
{if isset($tier_9_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 9</strong></td>
      <td width="75%">{$tier_9_amount}{$tier_9_type}</td>
    </tr>
{/if}
{if isset($tier_10_active)}
    <tr>
      <td width="25%"><strong>{$tlinks_level} 10</strong></td>
      <td width="75%">{$tier_10_amount}{$tier_10_type}</td>
    </tr>
{/if}
</table>
{/if}