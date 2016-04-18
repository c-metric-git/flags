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

<div class="span12">
<div class="row-fluid">
    <div class="navbar clearfix">
         <a data-target=".menu2" data-toggle="collapse" class="btn btn-navbar">
            <span class="navi-res">Other Links</span>
          </a>
     
        <div class="nav-collapse menu2 collapse">
               <div class="navbar-inner">
            <ul class="nav">
			{if isset($tier_enabled)}
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    {$menu_drop_heading_stats}
                    <span class="caret"></span>
                    </a>
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=1">{$menu_drop_general_stats}</a></li>
                        <li><a href="account.php?page=2">{$menu_drop_tier_stats}</a></li>
                    </ul>		
                </li>
                    {else}
					<li><a href="account.php?page=1">{$menu_drop_general_stats}</a></li>
					{/if}
                <li class="dropdown ">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        {$menu_drop_heading_commissions}
                        <span class="caret"></span>
                    </a>
                  
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=4&report=1">{$menu_drop_current}</a></li>
                        {if isset($tier_enabled)}
                            <li><a href="account.php?page=4&report=2">{$menu_drop_tier}</a></li>
                        {/if}
                        {if isset($pending_enabled)}
                            <li><a href="account.php?page=4&report=3">{$menu_drop_pending}</a></li>
                        {/if}
                        
                        {if isset($delayed_enabled)}
                            <li><a href="account.php?page=4&report=6">{$menu_drop_delayed}</a></li>
                        {/if}
                    
                        <li><a href="account.php?page=4&report=4">{$menu_drop_paid}</a></li>
                        
                        {if isset($tier_enabled)}
                            <li><a href="account.php?page=4&report=5">{$menu_drop_paid_rec}</a></li>
                        {/if}
                        
                        {if isset($recurring_enabled)}
                            <li><a href="account.php?page=5">{$menu_drop_recurring}</a></li>
                        {/if}
                        
                    </ul>
                </li>
                <li><a href="account.php?page=3">{$menu_drop_heading_history}</a></li>
                <li><a href="account.php?page=6">
                             {$menu_drop_heading_traffic}            
                    </a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        {$menu_drop_heading_account}
                        <span class="caret"></span>
                    </a>     
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=17">{$menu_drop_edit}</a></li>
                        <li><a href="account.php?page=18">{$menu_drop_password}</a></li>
                        {if isset($change_commission)}
                            <li><a href="account.php?page=19">{$menu_drop_change}</a></li>
                        {/if}
                        {if isset($logos_enabled)}
                            <li><a href="account.php?page=27">{$menu_drop_heading_logo}</a></li>
                        {/if}
                        
                        {if isset($use_faq) && ($faq_location == 2)}
                            <li><a href="account.php?page=21">{$menu_drop_heading_faq}</a></li>
                        {/if}
                        
                        {if isset($testimonials)}
                            <li><a href="account.php?page=41">Offer A Testimonial</a></li>
                        {/if}
						
                        {if isset($pic_upload)}
                            <li><a href="account.php?page=43">Upload Your Picture</a></li>
                        {/if}
                    </ul>	
                </li>
             </ul>             
            </div><!--navcollaps-collaps-->
        </div><!--navbar-inner-->
    </div><!--navbar-->
    </div>