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
         {if isset($testimonials) && (isset($testimonials_active))}
             <table class="table table-striped" style="color:{$gb_text_color};">   
                {section name=nr loop=$testi_results}
                    <tr>
                    <td>  
                    <div class="span12">
                        <p style="font-style:italic;">"{$testi_results[nr].testimonial}"</p>
						<p class="pull-right">{$testi_results[nr].affiliate_name}{if isset($show_testimonials_link)} - <a href="{$testi_results[nr].website_url}" target="_blank">{$testi_visit}</a>{/if}</p>
                    </div> 
                    </td>      
                    </tr>
                {/section}
            </table>
         {else}
                <p>{$testi_na}</p>
         {/if}
    </div>
</div>
</div>
    {include file='file:footer.tpl'}