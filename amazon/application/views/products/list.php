<style>
    table th, table td { padding: 8px; vertical-align: middle; font-size:12px; font-family:Arial, Helvetica, sans-serif; border-bottom:none; }
    .icoachinput input,select,textarea{ width:250px; border:1px solid #CCCCCC; }
</style>
<script>
$(document).ready(function()
    {
        $(".test").colorbox({ width:"80%", height:"80%"});
    });
</script>    
<section class="column" id="main" style="height: 634px;">
    <div class="clear"></div>
    <?php
        if($src!="")
            $val=$src;
        else
            $val='Search';
    ?>
    <?php
        if(isset($flag))
        {
            if($flag == 1)
            {
                $f = "products";
            }
            else
            {
               $f = "products/ready_for_amazon";
            }
        }
    ?>
    <form enctype="multipart/form-data" name="editprofile" id="editprofile" method="post" accept-charset="utf-8" action="<?=$this->config->item('base_url')?><?=$f?>">
        <article class="module width_full">
            <header>
                <h3 class="tabs_involved"><?=$this->lang->line('admin_left_menu_product_list')?></h3>
                
                <ul class="uadd" >
                    <li style="background-color:#666; color:#FFF; ">
                        <a href="javascript:history.back();" style="line-height: 21px; text-decoration:none; color:#FFF;"> <?=$this->lang->line('admin_general_back')?></a>
                    </li>                    
                </ul>                
<!--                <ul class="uadd" id="sync_live_ul">
                    <li style="background-color:#666; color:#FFF; ">
                        <a href="javascript:sync_live_db();" style="text-decoration:none; color:#FFF;">Synchronize Live Database</a>
                    </li>
                </ul>-->
                
            </header>
            <div id="div_msg">
        <?php        
        if(isset($msg)) echo '<label class="error">'.urldecode ($msg).'</label>'; ?>
            <center><input type="text" style="width:500px; background: transparent; color: #F00;font-size: 12px;border: none;" readonly="true" id="nameexist" value="" /></center>
            </div> 
            <div>
                <ul class="sarch">
                    <input class="psearch" type="text" name="search" id="search" value="" style="color:#000;">
                <li>
                    <input type="submit" name="submit" value="submit" style="background-color:#666; color:#FFF;">
                </li>
                </ul>
            </div>
            <div class="tab_container">
                <div class="tab_content" id="tab1">
                    <div id="userlist_table_ajx">                        
                        <table border="0" width="790" cellspacing="0" cellpadding="0" class="icoachinput">
                        <tbody>
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td valign="top">
                                        <table border="1" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #E0E0E3;">
                                        <tbody>
                                            <tr align="center" style="background:#666666; font-weight:bold;color:#fff;">
                                                <td>SKU</td>
                                                <td>Amazon Title</td>
                                                <td>ASIN</td>
                                                <td>Description</td>
                                                <td>Item Type</td>
                                                <td>Item Price</td>                                                
                                                <td>Post Product</td>
                                            </tr>
                                            <?php
                                            
                                                if(count($result) > 0)
                                                {
                                                    foreach ($result as $key=>$value)
                                                    {                                                        
                                                        echo '<tr>';
                                                        echo '<td>',isset($value['sku']) ? $value['sku'] : '-','</td>';
                                                        echo '<td>',isset($value['Amazon Title']) ? $value['Amazon Title'] : '-','</td>';
                                                        echo '<td>',isset($value['ASIN']) ? $value['ASIN'] : '-','</td>';
                                                        echo '<td>',isset($value['description']) ? $value['description'] : '-','</td>';
                                                        echo '<td>',isset($value['item-type']) ? $value['item-type'] : '-','</td>';
                                                        echo '<td>',isset($value['item-price']) ? $value['item-price'] : '-','</td>';                                                        
                                                        echo '<td style="color:#337100;">';
                                                        if($value['ASIN'] =='')
                                                            echo '<a target="_blank" href="'.$this->config->item('base_url').'cronjobs/amazon_cron.php?id='.$value['@id'].'">Post To Amazon</a>';
                                                        else
                                                            echo '<a target="_blank" href="'.$this->config->item('base_url').'cronjobs/amazon_cron_delete.php?id='.$value['@id'].'">Remove From Amazon</a>';
                                                        echo '</td></tr>';
                                                    }
                                                }
                                                else
                                                {
                                                    echo '<tr><td colspan="6" align="center">No Records Found</td></tr>';
                                                }
                                            ?>
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
                            </td>
                        </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </form>	
     <div id="pager" class="pager1" style=" margin-top: -9px;">
                <ul>
                    <?=$this->pagination->create_links(); ?>
                </ul>
    </div>
    <div class="spacer"></div><div align="center" id="map" style="width: 600px; height: 400px; display:none;"><br/></div>
</section>
<script language="javascript" type="text/javascript">
    function sync_live_db()
    {
        $('#sync_live_ul').html('<img src="<?php echo base_url();?>/images/loading.gif" alt="Loading.."/>');
        $.ajax({
            type: "post",            
            url: "<?php echo base_url().'products/sync_live_db';?>",            
            success: function(msg)
            {
                if(msg == 'Success')
                    window.location.reload();
            }
        });
    }
</script>