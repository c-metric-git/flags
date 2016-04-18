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

<div class="accordion admin_note" id="accordion1">
<div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne">
    <i class="icon-minus-sign"></i> {$general_notes_title}
    </a>
</div>
<div id="collapseOne" class="accordion-body collapse in">
{section name=nr loop=$note_results}
    <table class="table table-bordered">        
        <tr>
        <td width="50%">{$general_notes_date}: {$note_results[nr].note_date}</td>
        <td width="50%">{$general_notes_to}: {$note_results[nr].note_to}</td>
        </tr>      
        <tr>
        <td width="100%" colspan="2"><b>{$note_results[nr].note_subject}</b></td>
        </tr>
        <tr>
        <td width="100%" colspan="2">{$note_results[nr].note_content}</td>
        </tr>    
    </table>
{sectionelse}
<table class="table table-bordered">
    <tr><td width="100%" >{$general_notes_none}</td></tr>    
</table>
{/section}
</div>
</div>