<style>
    table th, table td { padding: 8px; vertical-align: middle; font-size:12px; font-family:Arial, Helvetica, sans-serif; border-bottom:none; }
    .icoachinput input,select,textarea{ width:250px; border:1px solid #CCCCCC; }
</style>

<section class="column" id="main" style="height: 634px;">
    <div class="clear"></div>
    <form enctype="multipart/form-data" name="editprofile" id="editprofile" method="post" accept-charset="utf-8" action="<?=$this->config->item('base_url')?>editprofile/update_admin" >
        <article class="module width_full">
            <header>
                <h3 class="tabs_involved"><?=$this->lang->line('edit_ownprofile_tableheading')?></h3>
                <ul class="uadd" >
                    <li style="background-color:#666; color:#FFF; ">
                        <a href="javascript:history.back();" style="text-decoration:none; color:#FFF;"> <?=$this->lang->line('admin_general_back')?></a>
                    </li>
                </ul>
            </header>
            <div id="div_msg">
        <?php 
            if(isset($msg)) echo '<label class="error">'.urldecode ($msg).'</label>';
        ?>
            <center><input type="text" style="width:500px; background: transparent; color: #F00;font-size: 12px;border: none;" readonly="true" id="nameexist" value="" /></center>
            </div>
            
            <div class="tab_container">
                <div class="tab_content" id="tab1">
                    <div id="userlist_table_ajx">
                        <div  style="float:right; padding-right:20px; padding-top:10px; color:#069; font-size:14px;">
                            <a id="div_light" href="javascript:void(0);" onclick="hide_show('light','userlist_table_ajx',1000);"><?=$this->lang->line('change_password_text');?></a>
                        </div>
                        <table border="0" width="790" cellspacing="0" cellpadding="0" class="icoachinput">
                            <tbody>
                                <tr>
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td valign="top">
                                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                                            <tbody>
                                                                <tr align="center" style="color:#F00; font-weight:bold;">
                                                                    <td colspan="4">
                                                                    </td>    
                                                                </tr>
                                                                
                                                                <tr>
                                                                    <input type="hidden" name="id" id="id" value="<?=$profile[0]['id']?>"  />
                                                                    <td ><?=$this->lang->line('edit_profile_labelfname')?> <span style="color:#F00">*</span></td>
                                                                    <td> <input type="text" data-bvalidator="alpha,required" name="fname" id="fname" value="<?=$profile[0]['name']?>"  /></td>
                                                                </tr> 
                                                                
                                                                <tr>
                                                                    <td width="150"><?=$this->lang->line('edit_profile_labelemail')?> <span style="color:#F00">*</span> </td>
                                                                    <td><input type="text" value="<?=$profile[0]['email']?>" id="email" name="email" data-bvalidator="required,email"></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                    <input type="submit" style="width:auto;" class="reg-btn" value="Save" name="type"></td>
                                </tr>
                            </tbody>
                        </table>

                    </div><!-- userlist_table_ajx End -->
                </div><!-- end of #tab1 -->
            </div><!-- end of .tab_container -->
        </article><!-- end of content manager article -->
    </form>	
    <div class="spacer"></div><div align="center" id="map" style="width: 600px; height: 400px; display:none;"><br/></div>
</section>

    <div id="light" class="white_content">
         <div style="padding:20px;"><font style="color:#000; font-size:18px; font-weight:bold; "><?=$this->lang->line('change_password_text');?></font>
         </div>
        <div >
             <form name="change_pw" id="change_pw" action="<?=$this->config->item('base_url')?>editprofile/change_pw/<?php echo $profile[0]['id']; ?>" method="post">
                 <table>
                     <tr>
                         <td width="100">Old Password</td>
                         <td><input type="password" value="" id="oldpw" name="oldpw" data-bvalidator="minlength[6],required" ></td>				
                     </tr>
                     <tr>
                         <td width="100">New Password </td>
                         <td><input type="password" value="" id="newpw" name="newpw" data-bvalidator="minlength[6],required"></td>				
                     </tr>
                     <tr>
                         <td width="100">Confirm Password</td>
                         <td><input type="password" value="" id="cpw" name="cpw" data-bvalidator="equalto[newpw],minlength[6],required"></td>				
                     </tr>
                     <tr>
                         <td width="100">&nbsp;</td>
                         <td><input type="submit" style="width:auto;" class="reg-btn" value="Save" name="pwchange"></td>														
                     </tr>
                 </table>
             </form>
         </div>
         <a id="div_botton" href="javascript:void(0);" onclick="hide_show('userlist_table_ajx','light',1000);" class="cl">  
             <img src="<?=base_url()?>images/admin/close-btn1.png">
         </a>
     </div>
<div id="fade" class="black_overlay"></div>

<script type="text/javascript">
    $(document).ready(function (){
              $('#change_pw').bValidator();
      });

    $(document).ready(function (){
                $('#editprofile').bValidator();
                $("#div_msg").fadeOut(4000);
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
    function getemail()
    {
        var name = $("#email").val();
        $.ajax({
            type: "post",
            data: {'name':name,
            },
            url: '<?=$this->config->item('admin_base_url')?>getemail', 
            success: function(msg1) 
            {
                if(msg1 != '')
                {
                    $("#nameexist").val(msg1);
                    $("#email").focus();
                }
                else
                    $("#nameexist").val(msg1);
            }
        });	
        return false;
    }
</script>
   