<script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?=$this->config->item('js_path')?>jquery-migrate-1.1.0.min.js"></script>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Amazon API - Log In</title>
        <link rel="stylesheet" type="text/css" media="screen" href="<?= $this->config->item('css_path') ?>login.css"/>
    </head>
    <body>        
        <form id="login" method="post" action="">
            <div id="div_login">
                <h1>Login</h1>
                <?php 
                    if(isset($msg)) echo '<fieldset id="error_fieldset"><label class="error">'.$msg.'</label></fieldset>';                    
                ?>
                <fieldset id="inputs">
                    <input id="email" placeholder="Email" autofocus="" required="" type="text" name="email">
                    <input id="password" placeholder="Password" required="" type="password" name="password">
                </fieldset>                    
            </div>
            <fieldset id="actions">
                <input id="submit" value="Login" type="submit">
                <a id="div_forgotpwdlink" href="javascript:void(0);" onClick="hide_show('div_forgotpwd','div_login',1000);">Forgot Password</a>
            </fieldset>
        </form>        
        <form id="forgotpwd" method="post" action="">
            <h1>Forgot Password?</h1>
            <?php if(isset($forgotpwd_msg)) echo '<fieldset id="error_fieldset"><label class="error">'.$forgotpwd_msg.'</label></fieldset>'; ?>
            <fieldset id="inputs_email">
                <input id="email_forgot" placeholder="Email" autofocus="" required="" type="text" name="email">
                <input id="forgotpwd" value="1" name="forgotpwd" type="hidden"/>
            </fieldset>            
            <fieldset id="actions">
                <input id="submit_new" value="Submit" type="submit">                
                <a id="div_loginlink" href="javascript:void(0);" onClick="hide_show('div_login','div_forgotpwd',1000);">Login</a>
            </fieldset>                
        </form>
    </body>
</html>
<script>
    $(document).ready(function()
    {        
        var forgotpwd_msg = '<?php echo (isset($forgotpwd_msg)) ? $forgotpwd_msg : '';?>';
        
        if(forgotpwd_msg)
        {
            $('#forgotpwd').css('display','inline');
            $('#login').css('display','none');
        }
        else
        {
            $('#forgotpwd').css('display','none');
            $('#login').css('display','inline');
        }
    });
    function hide_show(showdiv,hidediv,timer)
    {
        var show = showdiv.split('div_');
        var hide = hidediv.split('div_');
        hide[1] ? $('#'+hide[1]).hide(timer) : '';
        show[1] ? $('#'+show[1]).show(timer) : '';
        $('#'+showdiv).show(timer); 
        $('#'+hidediv).hide(timer);        
        $('#'+showdiv+'link').hide();
        $('#'+hidediv+'link').show();        
    }
</script>