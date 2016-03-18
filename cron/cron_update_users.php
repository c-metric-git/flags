<?php

    // ini_set("display_errors",1);
    echo date("c")." - Cron job started\n";

    require_once("cron_init.php");
    require_once(BASE_PATH."lib/Teamdesk/class.export_teamdesk_user.php");
    /**
    * @desc code to update the is_correct_shipping_address in quickbase
    */
    $export_new_user = "Yes";
    $objTeamDeskUser = new TeamDeskUser($db);           
    $strReturn = $objTeamDeskUser->exportUserToTeamDesk($export_new_user);  
    echo "<br>";
    echo date("c")." - Cron job ended\n";
    $end_time =  date("c")." - Cron job ended\n";
   // mail(CRON_EMAIL_NOTIFICATION,"HalloweenMakeup - Update Users Cron","Clownantics - Users Updated Successfully In TeamDesk");
    $db->done();
?>



