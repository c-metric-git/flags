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

{if isset($faq_enabled)}
<legend style="color:{$legend};">{$faq_page_title}</legend>
{section name=nr loop=$faq_results}
<div class="well" style="color:{$bg_text_color};">
<strong>{$faq_results[nr].faq_question}</strong><br />
{$faq_results[nr].faq_answer}
</div>
{sectionelse}
<div class="well" style="color:{$bg_text_color};">
{$faq_page_none}
</div>
{/section}
{/if}