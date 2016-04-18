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

<div id="push"></div>
</div> <!--Wrapper for sticky footer-->
<footer class="footer">
		<div class="container">
        <div class="container-fluid">
		
        
        <div class="row-fluid">
		<div class="float-left footerLink">
		<a href="http://www.idevdirect.com{$affiliate_account}" target="_blank">{$footer_tag}</a>
		</div>
        
		<div class="float-right footerLink">
		{$footer_copyright} {php} echo date("Y"); {/php} <a href="{$siteurl}" target=_blank><b>{$sitename}</b></a> - {$footer_rights}
		</div>
		{if isset($social_enabled) && isset($social_location_footer)}
		<div class="float-right" style="margin:8px 5px; 0 0px;" align="center">
		{section name=nr loop=$social_icons}<a href="{$social_icons[nr].link}" target="_blank" style="padding-right:5px;"><img src="{$social_icons[nr].image}" width="32" height="32" style="border:none;"></a>{/section}
		</div>
		{/if}
		
		
		
		</div>
		
          </div>
          </div>
        </div>
</footer>
{literal}
<script>
$(function ()
{ $(".example").popover({trigger: 'hover', html:true});
});
</script>
{/literal} 
</body>
</html>