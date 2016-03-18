<style>        
    .accordion2 {
        width: 100%;
    }
    .accordion2 h4 {
        padding: 7px 15px;
        margin: 0;
        font: bold 12px Arial, Helvetica, sans-serif;
        border-bottom: none;
        cursor: pointer;
        color:#FFF;
    }
    .accordion2 h4:hover {
        background-color: #CCC;
    }
    .accordion2 h4.active {
        background-position: right 5px;
    }
    .accordion2 div {
        margin: 0;
        padding:0px;
        display: none;
    }

    .accordion2 div a{
        display:block;
        color:#666;
        text-decoration:none;
        line-height:29px; font-weight:bold;
        padding: 0px 0px 0 15px;

    }

    .accordion2 div a:hover{
        color:#333;
        text-decoration:underline;
        line-height:29px; font-weight:bold;
        display:block;
        background:#757575;
    }
    .accordion2 div a.active{background:#757575;color:#333; cursor:default; text-decoration:none;}
</style>
<aside id="sidebar" class="column">	
    <?php
    $admin_session = $this->session->userdata('admin_session');
    ?>
    <div class="accordion2">
        <?php
        $grouptype = $admin_session['group_type'];
        switch ($grouptype)
        {
            case "administrator":
            default:
                ?>
                <h4  class="<?php if ($this->uri->segment(1) == "cdashbord" ) echo 'active'; ?>"><a href="<?= $this->config->item('base_url') ?>cdashbord"><?= $this->lang->line('admin_left_menu_home') ?></a></h4>
                
                
                <h4 class="<?php if ($this->uri->segment(1) == "editprofile") echo 'active'; ?>"><a href="<?= $this->config->item('base_url') ?>editprofile"><?= $this->lang->line('admin_left_menu_user') ?></a></h4>
                <h4 class="<?php if ($this->uri->segment(1) == "products" || $this->uri->segment(1) == "add_products" ) echo 'active'; ?>"><?= $this->lang->line('admin_left_menu_product') ?></h4>
                <div> 
                    <ul>    
                        <li class="icn_jump_back"><a href="<?= $this->config->item('base_url') ?>products/"><?= $this->lang->line("admin_left_menu_product_list") ?></a></li>
                        <li class="icn_jump_back"><a href="<?= $this->config->item('base_url') ?>products/ready_for_amazon">Ready For Amazon</a></li>
                    </ul>
                </div>
                
                
                <h4 class="<?php if ($this->uri->segment(1) == "logout") echo 'active'; ?>"><a href="<?= $this->config->item('base_url') ?>logout"><?= $this->lang->line('admin_left_menu_logout') ?></a></h4>
                
        <?php
            break;
        } ?>
    </div>
    <footer>
        <div><strong>Copyright &copy; <?= date('Y') ?> <?= $this->config->item('sitename'); ?></strong></div>            
    </footer>
</aside>