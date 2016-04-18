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
<div class="container" style="margin-top:15px;">
    <div class="row-fluid">        
        {if isset($use_faq) && ($faq_location == 1)}   
         <table class="table table-striped">                  
            {section name=nr loop=$faq_results}               
                <tr >
                    <td width="100%"><font size="4"><b>{$faq_results[nr].faq_question}</b></font><BR />{$faq_results[nr].faq_answer}</td>
                </tr>                           
            {/section}
           </table>   
         {else}
<p class="well">Our Frequently Asked Questions Are Not Made Public</p>
         {/if}
    </div>    
</div>

    {include file='file:footer.tpl'}
