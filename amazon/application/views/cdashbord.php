<section class="column" id="main" style="height: 634px;">
    <article class="module width_full">
        <div class="tab_container">
            <div class="tab_content" id="tab1">
                <div id="userlist_table_ajx">
                    <table border="0" width="790" cellspacing="0" cellpadding="0" class="icoachinput">
                        <tbody>
                            <div style="height: 10px;">
                                <div id="div_msg">
                                    <?php if(isset($msg)) echo '<label class="error">'.urldecode ($msg).'</label>'; ?>
                                </div>
                            </div>
                            <tr>
                                <td colspan='4' align='center'>
                                    <!--<img src=<?=$this->config->item('image_path')?>ID-10079460.jpg />-->
                                    <br><h3>Dashboard</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </article>
</section>
<script>
    $(document).ready(function(){
        $("#div_msg").fadeOut(4000); 
    });
</script>
</body>
</html>