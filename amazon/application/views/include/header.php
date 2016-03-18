<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <link rel="icon" type="image/ico" href="<?=$this->config->item('base_url')?>/images/logo.ico">
        <title><?=$this->config->item('sitename');?></title>        
        <link rel="stylesheet" type="text/css" media="screen" href="<?=$this->config->item('base_url')?>/css/layout.css"/>        
        <link rel="stylesheet" type="text/css" media="screen" href="<?=$this->config->item('base_url')?>/css/style.css"/>        
        <link rel="stylesheet" type="text/css" href="<?=$this->config->item('css_path')?>bvalidator.css"/>        
        <link rel="stylesheet" type="text/css" href="<?= $this->config->item('css_path') ?>jquery.confirm.css"/>
        <link rel="stylesheet" type="text/css" href="<?= $this->config->item('css_path') ?>buttons.css"/>
       
        
        <script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery-1.9.1.js"></script>
        <script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery-migrate-1.1.0.min.js"></script>
        <script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery-ui.js"></script>
        <script type="text/javascript" src="<?=$this->config->item('js_path') ?>jquery.confirm.js"></script>        
        <script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery.bvalidator.js"></script>        
        
        <!--Css and js file call for color box-->
        <link rel="stylesheet" type="text/css" href="<?= $this->config->item('js_path') ?>colorbox/colorbox.css"/>
        <script type="text/javascript" src="<?= $this->config->item('js_path') ?>colorbox/jquery.colorbox.js"></script>
        <script type="text/javascript" src="<?=$this->config->item('js_path')?>colorbox/jquery.colorbox-min.js"></script>
                
        <!--[if lt IE 9]>
        <link rel="stylesheet" href="<?=$this->config->item('css_path')?>ie.css" type="text/css" media="screen" />
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        
        <script type="text/javascript">
            $(function(){                
                $("#accordion").accordion({ header: "h4" });
            });

            $(document).ready(function()
            {                
                $(".accordion2 h4.active").next("div").slideToggle("slow");
                $(".accordion2 h4").click(function(){
                    $(this).next("div").slideToggle("slow").siblings("div:visible").slideUp("slow");
                    $(this).toggleClass("active");
                    $(this).siblings("h4").removeClass("active");
                });
            });
        </script>
    </head>
  <body>
    <header id="header">
        <hgroup>            
            <h2 class="section_title">Clown Antics</h2>
        </hgroup>
    </header>
    <section id="secondary_bar">
        <div class="user">
            <p> 
                <?php $admin_session = $this->session->userdata('admin_session');
                    echo "<b> Welcome : <span style='color: #30B0C8;' >".ucfirst($admin_session['name'])."</span></b>";
                ?>
            </p>
        </div>
    </section>