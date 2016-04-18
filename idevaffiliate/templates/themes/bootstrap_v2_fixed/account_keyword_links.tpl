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

{if isset($custom_links_enabled)}
<legend style="color:{$legend};">{$keyword_title}</legend>
{$keyword_info}<br /><br />
<table class="table table-bordered">
    <tr>
      <td width="25%" colspan="2"><strong>{$keyword_heading}</strong></td>
    </tr>
    <tr>
      <td width="25%"><strong>{$keyword_tracking} 1</strong></td>
      <td width="75%">tid1</td>
    </tr>
    <tr>
      <td width="25%"><strong>{$keyword_tracking} 2</strong></td>
      <td width="75%">tid2</td>
    </tr>
    <tr>
      <td width="25%"><strong>{$keyword_tracking} 3</strong></td>
      <td width="75%">tid3</td>
    </tr>
    <tr>
      <td width="25%"><strong>{$keyword_tracking} 4</strong></td>
      <td width="75%">tid4</td>
    </tr>
</table>
  
{$keyword_build}<br />
<input class="input-block-level" style="background-color:#f2f6ff;" type="text" name="sub_link" value="{$custom_keyword_linkurl}"/><br />
{$keyword_example}: {$custom_keyword_linkurl}&tid1=<b>google</b><br />
<a href="http://www.idevlibrary.com/docs/Custom_Links.pdf" target="_blank" class="btn btn-mini btn-primary"><b>{$keyword_tutorial}</b></a>
{/if}