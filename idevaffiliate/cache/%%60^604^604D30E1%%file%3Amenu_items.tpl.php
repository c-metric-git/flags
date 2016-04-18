<?php /* Smarty version 2.6.14, created on 2016-04-09 11:51:52
         compiled from file:menu_items.tpl */ ?>

<div class="span12">
<div class="row-fluid">
    <div class="navbar clearfix">
         <a data-target=".menu2" data-toggle="collapse" class="btn btn-navbar">
            <span class="navi-res">Other Links</span>
          </a>
     
        <div class="nav-collapse menu2 collapse">
               <div class="navbar-inner">
            <ul class="nav">
			<?php if (isset ( $this->_tpl_vars['tier_enabled'] )): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo $this->_tpl_vars['menu_drop_heading_stats']; ?>

                    <span class="caret"></span>
                    </a>
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=1"><?php echo $this->_tpl_vars['menu_drop_general_stats']; ?>
</a></li>
                        <li><a href="account.php?page=2"><?php echo $this->_tpl_vars['menu_drop_tier_stats']; ?>
</a></li>
                    </ul>		
                </li>
                    <?php else: ?>
					<li><a href="account.php?page=1"><?php echo $this->_tpl_vars['menu_drop_general_stats']; ?>
</a></li>
					<?php endif; ?>
                <li class="dropdown ">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <?php echo $this->_tpl_vars['menu_drop_heading_commissions']; ?>

                        <span class="caret"></span>
                    </a>
                  
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=4&report=1"><?php echo $this->_tpl_vars['menu_drop_current']; ?>
</a></li>
                        <?php if (isset ( $this->_tpl_vars['tier_enabled'] )): ?>
                            <li><a href="account.php?page=4&report=2"><?php echo $this->_tpl_vars['menu_drop_tier']; ?>
</a></li>
                        <?php endif; ?>
                        <?php if (isset ( $this->_tpl_vars['pending_enabled'] )): ?>
                            <li><a href="account.php?page=4&report=3"><?php echo $this->_tpl_vars['menu_drop_pending']; ?>
</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset ( $this->_tpl_vars['delayed_enabled'] )): ?>
                            <li><a href="account.php?page=4&report=6"><?php echo $this->_tpl_vars['menu_drop_delayed']; ?>
</a></li>
                        <?php endif; ?>
                    
                        <li><a href="account.php?page=4&report=4"><?php echo $this->_tpl_vars['menu_drop_paid']; ?>
</a></li>
                        
                        <?php if (isset ( $this->_tpl_vars['tier_enabled'] )): ?>
                            <li><a href="account.php?page=4&report=5"><?php echo $this->_tpl_vars['menu_drop_paid_rec']; ?>
</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset ( $this->_tpl_vars['recurring_enabled'] )): ?>
                            <li><a href="account.php?page=5"><?php echo $this->_tpl_vars['menu_drop_recurring']; ?>
</a></li>
                        <?php endif; ?>
                        
                    </ul>
                </li>
                <li><a href="account.php?page=3"><?php echo $this->_tpl_vars['menu_drop_heading_history']; ?>
</a></li>
                <li><a href="account.php?page=6">
                             <?php echo $this->_tpl_vars['menu_drop_heading_traffic']; ?>
            
                    </a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <?php echo $this->_tpl_vars['menu_drop_heading_account']; ?>

                        <span class="caret"></span>
                    </a>     
                   <ul class="dropdown-menu opentoggle">
                        <li><a href="account.php?page=17"><?php echo $this->_tpl_vars['menu_drop_edit']; ?>
</a></li>
                        <li><a href="account.php?page=18"><?php echo $this->_tpl_vars['menu_drop_password']; ?>
</a></li>
                        <?php if (isset ( $this->_tpl_vars['change_commission'] )): ?>
                            <li><a href="account.php?page=19"><?php echo $this->_tpl_vars['menu_drop_change']; ?>
</a></li>
                        <?php endif; ?>
                        <?php if (isset ( $this->_tpl_vars['logos_enabled'] )): ?>
                            <li><a href="account.php?page=27"><?php echo $this->_tpl_vars['menu_drop_heading_logo']; ?>
</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset ( $this->_tpl_vars['use_faq'] ) && ( $this->_tpl_vars['faq_location'] == 2 )): ?>
                            <li><a href="account.php?page=21"><?php echo $this->_tpl_vars['menu_drop_heading_faq']; ?>
</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset ( $this->_tpl_vars['testimonials'] )): ?>
                            <li><a href="account.php?page=41">Offer A Testimonial</a></li>
                        <?php endif; ?>
						
                        <?php if (isset ( $this->_tpl_vars['pic_upload'] )): ?>
                            <li><a href="account.php?page=43">Upload Your Picture</a></li>
                        <?php endif; ?>
                    </ul>	
                </li>
             </ul>             
            </div><!--navcollaps-collaps-->
        </div><!--navbar-inner-->
    </div><!--navbar-->
    </div>