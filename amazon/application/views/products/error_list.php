<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    table th, table td { padding: 8px; vertical-align: middle; font-size:12px; font-family:Arial, Helvetica, sans-serif; border-bottom:none; }
    .icoachinput input,select,textarea{ width:250px; border:1px solid #CCCCCC; }
    .cl {position:absolute; right:-10px; top:-10px;}
    .box-body input{ text-transform:none !important; }
    .red{ color:#FF0000; }
    div.paging1 { padding: 3px; margin: 3px; }
    div.paging1 a { padding: 2px 5px 2px 5px; margin: 2px; border: 1px solid #AAAADD; text-decoration: none; /* no underline */ color: #000099; }
    div.paging1 a:hover, div.paging1 a:active { border: 1px solid #000099; color: #000; }
    div.paging1 span.current { padding: 2px 5px 2px 5px; margin: 2px; border: 1px solid #000099; font-weight: bold; background-color: #993300; color: #FFF; }
    div.paging1 span.disabled { padding: 2px 5px 2px 5px; margin: 2px; border: 1px solid #EEE; color: #DDD; }
    div.paging1 b{margin-left: 4px !important;}
    .vertical_list li {	display: inline;	margin: 0;	padding:0px 0px; list-style: none outside none; float:left;	}
    ul.vertical_list { list-style: none outside none; }
    .vertical_list label { font-size: 12px; font-weight:bold; padding-right:10px; }
    .serchtextbox{ width:20px; height:25px !important; }
</style>

<section id="main" class="column" style="width: 95% !important;" >
    <div class="clear"></div>
    <article class="module width_full">
        <header>
            <h3 class="tabs_involved">
                Error for SKU [ <?php echo $error_sku; ?> ]
                <span id="span_msg" style="font-size:10px;display:block;"></span>
            </h3>
            <div style="float:right; margin-right:20px; margin-top:10px;" id="msg"></div>
            <input type="hidden" id="update_status_hidden" value="0" />
        </header>
        <div class="tab_container">
            <div id="tab1" class="tab_content">
                
                <div id="userlist_table_ajx">
                        <?php
                        if ($error_list != 0) 
                        {
                                ?>
                                <table id="insured_list" class="tablesorter  table_insured_list" cellspacing="1">
                                    <thead>
                                        <tr>
                                            <th width="10%">Error Code</th>
                                            <th width="70%">Error Message</th>
                                            <th width="20%">Prodcut SKU</th>
                                        </tr>
                                    </thead>
                                    <tfoot><tr><td colspan="8"><div class="inner"> </div></td></tr></tfoot>
                                    <tbody>
                            <?php 
                            for($i=0;$i<count($error_list);$i++)
                            { ?>          
                            <tr>
                                <td width="10%"><?php echo $error_list[$i]['ResultMessageCode']?></td>
                                <td width="90%"><?php echo $error_list[$i]['ResultDescription']?></td>
                                <td width="90%"><?php echo $error_list[$i]['SKU']?></td>
                            </tr>
                   <?php    }   
                        }
                        
                        ?>
                       </tbody>
                    </table>
                </div><!-- userlist_table_ajx End -->
            </div><!-- Tab1 End -->
        </div><!-- Tab_container End -->
    </article><!-- end of content manager article -->
    <style>
        .editbox{display:none ; width:100% !important;}
        td{padding:5px;}
        .editbox{font-size:14px;width:auto; background-color:#ffffcc; border:solid 1px #000; padding:4px;}
        .tooltip{background-color:#000; border:2px solid #fff; padding:10px 15px; width:auto; display:none; color:#fff; text-align:left; font-size:12px; -moz-box-shadow:0 0 10px #000; -webkit-box-shadow:0 0 10px #000;}
    </style>
    <!--[if lt IE 7]><style>.tooltip { background-image:url(<?= base_url() ?>js/tooltips/black_arrow.gif);}</style><![endif]-->