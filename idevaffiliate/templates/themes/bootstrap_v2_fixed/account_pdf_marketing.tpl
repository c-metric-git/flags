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

<legend style="color:{$legend};">{$pdf_title} {$pdf_marketing}</legend>
{$pdf_description_1} {$pdf_description_2}<br /><br />
<a target="_blank" href="http://www.adobe.com/products/acrobat/readstep2.html"><img border="0" src="{$theme_folder}/images/get_adobe_reader.gif" width="112" height="33"></a>
<br /><br />
<table class="table table-bordered">
<thead>
<tr>
<th><b>{$pdf_file_name}</b></th>
<th><b>{$pdf_file_size}</b></th>
<th><b>{$pdf_file_description}</b></th>
</tr>
</thead>
<tbody>
{section name=nr loop=$pdf_results}
    <tr>
      <td><a href="media/pdf/{$pdf_results[nr].pdf_filename}" target="_blank">{$pdf_results[nr].pdf_filename}</a></td>
      <td>{$pdf_results[nr].pdf_size} {$pdf_bytes}</td>
      <td>{$pdf_results[nr].pdf_desc}&nbsp;&nbsp;</td>
    </tr>
{/section}
</tbody>
</table>