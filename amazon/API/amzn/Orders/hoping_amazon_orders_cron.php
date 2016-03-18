<?php

echo __FILE__;

exit;


$RUBY = '/usr/local/rvm/rubies/default/bin/ruby';

$BASEDIR = '/home/seanno/public_html/admin/';
//$BASEDIR = 'c:/dev/src/php/amzordermails/amzordermails';

set_include_path(get_include_path() . PATH_SEPARATOR . $BASEDIR . 'hoping.amazon.mws');

/** your amazon mws credentials **/
define('AMAZON_MWS_MERCHANT_ID', 'A1TBXT81I6OFLY');
define('AMAZON_MWS_MARKETPLACE_ID', 'ATVPDKIKX0DER');
define('AMAZON_MWS_ACCESS_KEY', 'AKIAJ27HFTJITXJPSKHA');
define('AMAZON_MWS_SECRET_KEY', 'oOVhQMDZ5kt2UEBmCLT9d3D4YtVn6cLMvNOduavK');

define('AMAZON_CBA_MARKETPLACE_ID', 'AZ4B0ZS3LGLX');

/** your CSV file location **/
define('AMAZON_CSV_UNSHIPPED_ORDERS_URI', '/home/seanno/public_html/admin/amazon-unshipped-orders.csv');

/** your e-mail template file locations **/
define('AMAZON_MAIL_TEMPLATE_DAY_8_BEFORE', '/home/seanno/public_html/admin/amazon-order-mail-template-8-days-before.htm');
define('AMAZON_MAIL_TEMPLATE_DAY_16_BEFORE', '/home/seanno/public_html/admin/amazon-order-mail-template-16-days-before.htm');
define('AMAZON_MAIL_TEMPLATE_DAY_24_BEFORE', '/home/seanno/public_html/admin/amazon-order-mail-template-24-days-before.htm');

$RUBY_SCRIPT = $BASEDIR . '/generateExcelFiles.rb';

$SUPPLIERS = array(
    'ovation' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-ovation.htm",
            'subject' => 'Ovation - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'bhaines@ovationmed.com',
            'attachment' => "$BASEDIR/amazon-templates/Ovation-{{datestamp}}.xlsx",
            'order-file' => "$BASEDIR/amazon-templates/ovation.csv",
            ),
    'patterson' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-patterson.htm",
            'subject' => 'Patterson - New Drop Ship Orders (Please Review Instructions)',
            'address' => array('customersupport@patterson-medical.com', 'roger.shackelford@pattersonmedical.com'),
            'attachment' => "$BASEDIR/amazon-templates/Patterson-{{datestamp}}.xlsx",
            'order-file' => "$BASEDIR/amazon-templates/patterson.csv",
            ),
    'isg' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-isg.htm",
            'subject' => 'ISG - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'emailorders.isg@invacare.com',
            'attachment' => "$BASEDIR/amazon-templates/ISG-{{datestamp}}.xlsx",
            'order-file' => "$BASEDIR/amazon-templates/isg.csv",
            ),
    'medline' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-medline.htm",
            'subject' => 'MEDLINE - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'service@medline.com',
            'attachment' => "$BASEDIR/amazon-templates/Medline-{{datestamp}}.xlsx",
            'order-file' => "$BASEDIR/amazon-templates/medline.csv",
            ),
    'indemed' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-indemed.htm",
            'subject' => 'INDEMED - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'imcs@indemed.com',
            'attachment' => "$BASEDIR/amazon-templates/Indemed-{{datestamp}}.xlsx",
            'order-file' => "$BASEDIR/amazon-templates/indemed.csv",
            ),
    'owner' => array(
            'template' => "$BASEDIR/amazon-templates/amazon-order-mail-template-owner.htm",
            'subject' => 'Amazon Orders',
            'address' => 'sean@examed.org',
            'attachment' => array(
                "$BASEDIR/amazon-templates/Ovation-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/Patterson-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/ISG-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/Medline-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/Indemed-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/NBS-{{datestamp}}.xlsx",
                "$BASEDIR/amazon-templates/AMZ-{{datestamp}}.xlsx"),
            'order-file' => "$BASEDIR/amazon-templates/all.csv",
            ),
    );

define('AMAZON_MAIL_SUPPLIER_ADDRESS_BCC', 'sean@examed.org'); // all supplier mails will be sent to this email as well
define('AMAZON_MAIL_SUPPLIER_ADDRESS_REPLYTO', 'sean@examed.org'); // all supplier mails will be sent with this reply-to address
define('AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO', ''); // all supplier mails will be sent to this email (!!! DEBUG !!!)

/** your e-mail configuration **/
define('AMAZON_MAIL_FROM', 'amazon-mail@lifehousemedical.com');
define('AMAZON_MAIL_BCC', '');
define('AMAZON_MAIL_SUBJECT', 'Your recent order from Nationwide Surgical on Amazon.com');

/** (!!!) debug setting (!!!) **/
//define('AMAZON_SEND_MAIL_TO', 'amazon-mail@lifehousemedical.com'); // set this value to a valid e-mail address if you would like to send all e-mails to this address
define('AMAZON_SEND_MAIL_TO', ''); // set this value to a valid e-mail address if you would like to send all e-mails to this address
define('AMAZON_DEBUG', 1); // set this value to 0 if no verbose logging statements should be written, only errors and exceptions are logged, set to 1 otherwise

/** your database credentials **/
define('AMAZON_DB_NAME', 'seanno_amazon_orders');
define('AMAZON_DB_HOST', 'localhost');
define('AMAZON_DB_USERNAME', 'seanno_amz');
define('AMAZON_DB_PASSWORD', 'pass1pass');

//require_once 'amazon.mws/Amazon.php';

class AmazonDatabase
{
    private $_link;
    
    public function init()
    {
        $this->_link = mysql_connect(AMAZON_DB_HOST, AMAZON_DB_USERNAME, AMAZON_DB_PASSWORD);
        mysql_select_db(AMAZON_DB_NAME);
        if (!$this->_link) {
            throw new Exception('No database');
        }
        $sql = 'CREATE TABLE IF NOT EXISTS order_items (
            order_id varchar(19),
            order_item_id varchar(14),
            purchase_date datetime,
            sku varchar(90),
            title varchar(250),
            ship_to_name varchar(90),
            ship_service_level varchar(45),
            buyer_email varchar(90),
            primary key(order_id, order_item_id)
        )';
        mysql_query($sql);
    }
    
    public function dispose()
    {
        if ($this->_link) {
            mysql_close($this->_link);
        }
    }
    
    public function getOrdersShippedBefore($nrOfDays)
    {
        $this->init();
        $date_after = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(0, 0, 0, date("m"), date("d")-$nrOfDays, date("Y")));
        $date_before = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(23, 59, 59, date("m"), date("d")-$nrOfDays, date("Y")));
        $sql = 'SELECT * FROM order_items WHERE purchase_date >= ' . $date_after . ' AND purchase_date <= ' . $date_before;
        //App::debug($sql);
        $result = mysql_query($sql, $this->_link);
        $orders = array();
        if (!$result) {
            throw new Exception('Unable to read shipped orders from database');
        } else {
            while ( ($row = mysql_fetch_assoc($result)) !== false) {
                $order = array();
                foreach($row as $name => $value) {
                    $order[$name] = $value;
                }
                $orders[] = $order;
            }
        }
        $this->dispose();
        return $orders;
    }

    public function insertShippedOrders($orders, $orderItems)
    {
        if (!$orderItems) {
            return;
        }
        $this->init();
        $orderMap = App::toMap($orders, 'orderId');
        $orderItemsGroups = App::splitArray($orderItems, 20);
        foreach($orderItemsGroups as $orderItemsGroup) {
            $values = '';
            $orderItemsMap = App::toMapArray($orderItemsGroup, 'orderId');
            foreach($orderItemsMap as $amazonOrderId => $orderItems) {
                $order = $orderMap[$amazonOrderId];
    $order->purchaseDate = str_ireplace('Z', '', $order->purchaseDate);
                foreach($order as $name => $value) {
                    $order->$name = mysql_escape_string($value);
                }
                foreach($orderItems as $orderItem) {
                    foreach($orderItem as $name => $value) {
                        $orderItem->$name = mysql_escape_string($value);
                    }
                    if ($values) {
                        $values .= ',';
                    }
                    $values .= "('{$orderItem->orderId}', '{$orderItem->orderItemId}', '{$order->purchaseDate}', '{$orderItem->sku}', '{$orderItem->title}', '{$order->shipToName}', '{$order->shipmentServiceLevelCategory }', '{$order->buyerEmail}')";
                }
            }
            $sql = 'INSERT IGNORE INTO order_items VALUES ' . $values; // purchase date in utc
            //App::debug($sql);
            if (!mysql_query($sql)) {
                throw new Exception('Unable to add shipped orders to database');
            }
        }
        $this->dispose();
    }
    
    public function maintain()
    {
        $this->init();
        $date_before = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(0, 0, 0, date("m"), date("d")-60, date("Y")));
        $sql = 'DELETE FROM order_items WHERE purchase_date <= ' . $date_before;
        mysql_query($sql);
  $this->dispose();
    }
    
}

class AmazonOrders
{
    private $_db;
    
    public static function test() {
        $instance = new AmazonOrders();
        $instance->_init();
        
        try {
            $orders = $instance->_getUnshippedOrders(AMAZON_CBA_MARKETPLACE_ID);
            print_r($orders);
            
            $orders = $instance->_getShippedOrders(AMAZON_CBA_MARKETPLACE_ID);
            print_r($orders);       
            
            
        } catch(exception $ex) {
        }
        
      
    }

    public static function shipCbaOrders()
    {
        $instance = new AmazonOrders();
        $instance->_init();
        
        try {
            $orders = $instance->_getUnshippedOrders(AMAZON_CBA_MARKETPLACE_ID);
            if ($orders && $orders['orders']) {
                $cbaOrders = array();
                foreach($orders['orders'] as $order) {
                    if (str_ireplace(' ', '', strtolower($order->salesChannel)) == 'amazoncheckout') {
                        $cbaOrders[] = $order;
                    }
                }
                if ($cbaOrders) {
                    App::debug($cbaOrders);
                    $instance->_ship($cbaOrders);
                }
            }
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
    }
 
    public static function saveCsv()
    {
        $instance = new AmazonOrders();
        $instance->_init();
        
        try {
            $orders = $instance->_getUnshippedOrders(AMAZON_MWS_MARKETPLACE_ID);
            $instance->_saveToCsv($orders['orders'], $orders['orderitems'], AMAZON_CSV_UNSHIPPED_ORDERS_URI);
            $instance->_shipSupplierOrders();
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
   }
    
    public static function sendMails()
    {
        $instance = new AmazonOrders();
        $instance->_init();
        
        try {
            $orders= $instance->_getShippedOrders(AMAZON_MWS_MARKETPLACE_ID);
            $instance->_db->insertShippedOrders($orders['orders'], $orders['orderitems']);
            $instance->_db->maintain();
            
            $stat = array(
                'eight_days_before' => 0,
                'sixteen_days_before' => 0,
                'twentyfour_days_before' => 0,
                );
            
            $orders = $instance->_db->getOrdersShippedBefore(8); 
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_8_BEFORE);
            $stat['eight_days_before'] = count($orders);
            
            $orders = $instance->_db->getOrdersShippedBefore(16);
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_16_BEFORE);
            $stat['sixteen_days_before'] = count($orders);
            
            $orders = $instance->_db->getOrdersShippedBefore(24);
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_24_BEFORE);
            $stat['twentyfour_days_before'] = count($orders);

            $instance->_sendMail('sean@examed.org', 'sean@examed.org', 'Amazon Order Feedback Mails', print_r($stat, true));
            
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
    }
    
    private function _saveToCsv($orders, $orderItems, $uri)
    {
        if (!$orders) {
            return;
        }
        if (!$orderItems) {
            App::warning('No order items for orders: ', $orders);
            return;
        }
        if (!$uri) {
            throw new Exception('No unshipped orders csv');
        }
        $orderMap = App::toMap($orders, 'orderId');
        $orderItemMap = App::toMapArray($orderItems, 'orderId');
        
        $fp = fopen($uri, 'w');
        if (!$fp) {
            throw new Exception('Unable to open CSV file location');
        }
        
        $header = array(
            'recipient-name',
            'ship-address-1',
            'ship-address-2',
            'ship-city',
            'ship-state',
            'ship-postal-code',
            'sku',
            'product-name',
            'quantity-to-ship',
            'ship-service-level',
            'order-id',
            );
        
        fputcsv($fp, $header);
        
        foreach($orderMap as $orderId => $order) {
            if (!isset($orderItemMap[$orderId])) {
                App::warning('No order items for order id: ', $orderId);
                continue;
            }
            $orderItems = $orderItemMap[$orderId];
            foreach($orderItems as $orderItem) {
                if ($orderItem->quantityOrdered - $orderItem->quantityShipped < 1) { // partially shipped orders
                    continue;
                }
                $row = array();
                $row[] = $order->shipToName;
                $row[] = $order->shipToAddressLine1;
                $row[] = $order->shipToAddressLine2 . $order->shipToAddressLine3;
                $row[] = $order->shipToCity;
                $row[] = $order->shipToStateOrRegion;
                $row[] = $order->shipToPostalCode;
                $row[] = $orderItem->sku;
                $row[] = $orderItem->title;
                $row[] = $orderItem->quantityOrdered - $orderItem->quantityShipped;
                $row[] = $order->shipmentServiceLevelCategory;
                $row[] = "'".str_ireplace('-', '', $orderId);
                
                fputcsv($fp, $row);
            }
        }
        
        fflush($fp);
        fclose($fp);
    }

    private function _ship($orders)
    {
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, AMAZON_CBA_MARKETPLACE_ID, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createFeeds($sec);
        
        $shipments = array();
        foreach($orders as $order) {
            if (is_string($order)) {
        if (count($order) >= 10) {
          $orderId = $order;
        } else {
          continue;
        }
      } else {
        $orderId = $order->orderId;
      }
      $shippingCarrier = 'FedEx';
      $amazonShipment = new AmazonShipment($orderId, $shippingCarrier);
            $shipments[] = $amazonShipment;
        }
        
        $summary = $mws->updateShipments($shipments);
        App::debug($summary);
        if ($summary->totalErrors) {
            throw new Exception('Unable to update ' . $summary->totalErrors . ' shipments');
        }
    }
    
    public function _getUnshippedOrders($marketplaceId)
    {
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, $marketplaceId, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createOrders($sec);
        
        $sometime  = mktime(0, 0, 0, 1, 1, 2012);
        $sometimeStr = strftime("%Y-%m-%dT%H:%M:%S", $sometime);
        
        $twoDaysAgo  = mktime(date("h"), date("i"), 0, date("m")  , date("d")-2, date("Y"));
        $twoDaysAgoStr = strftime("%Y-%m-%dT%H:%M:%S-0500", $twoDaysAgo);
        
        
        $lastUpdatedAfter = $twoDaysAgoStr;
        $lastUpdatedBefore = false;
        $orderStatus = array('Unshipped', 'PartiallyShipped');
        
        $orders = $mws->listOrders($lastUpdatedAfter, $lastUpdatedBefore, $orderStatus);
        
        $orderItems = false;
        if ($orders) {
            $orderIds = App::toArrayFromObjectMembers($orders, 'orderId');
            $orderItems = $mws->listOrderItems($orderIds);
        }

        return array('orders' => $orders, 'orderitems' => $orderItems);
    }
    
    private function _getShippedOrders($marketplaceId)
    {
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, $marketplaceId, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createOrders($sec);
        
        $twoDaysAgo  = mktime(date("h"), date("i"), 0, date("m")  , date("d")-2, date("Y"));
        $twoDaysAgoStr = strftime("%Y-%m-%dT%H:%M:%S-0500", $twoDaysAgo);
        
        $lastUpdatedAfter = $twoDaysAgoStr;
        $lastUpdatedBefore = false;
        $orderStatus = 'Shipped';
        
        $orders = $mws->listOrders($lastUpdatedAfter, $lastUpdatedBefore, $orderStatus);

        $orderItems = false;
        if ($orders) {
            $orderIds = App::toArrayFromObjectMembers($orders, 'orderId');
            $orderItems = $mws->listOrderItems($orderIds);
        }
        
        return array('orders' => $orders, 'orderitems' => $orderItems);
    }
    
    private function _shipSupplierOrders()
    {
        global $SUPPLIERS;
        global $RUBY_SCRIPT;
        
        $suppliersWithErrors = array();
        
        $dateStamp = strftime('%Y%m%d');
        foreach($SUPPLIERS as $name => $data) {
            if (!is_array($data['attachment'])) {
                $SUPPLIERS[$name]['attachment'] = str_ireplace('{{datestamp}}', $dateStamp, $data['attachment']);
            } else {
                for ($i = 0; $i < count($data['attachment']); $i++) {
                    $SUPPLIERS[$name]['attachment'][$i] = str_ireplace('{{datestamp}}', $dateStamp, $data['attachment'][$i]);
                }
            }
        }
        
        if (exec($RUBY . ' ' . $RUBY_SCRIPT) === false) {
            throw new Exception('Unable to execute ruby script');
        }
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (!is_array($data['attachment'])) {
                    if (!file_exists($data['attachment'])) {
                        throw new Exception('No attachment for supplier ' . $name);
                    }
                }
                if ($data['order-file']) {
                    if (!file_exists($data['order-file'])) {
                        throw new Exception('No order-file for supplier ' . $name);
                    }
                }
            } catch(exception $ex) {
                $suppliersWithErrors[] = $name;
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        // mark as shipped
        $orderIds = array();
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (array_search($name, $suppliersWithErrors) !== false) {
                    App::error('Unable to mark orders for supplier ', $name, ' as shipped');
                    continue;
                }
                if (!$data['order-file']) {
                    continue;
                }
                $orderFile = $data['order-file'];
                
                $orders = trim(file_get_contents($orderFile));
                
                if ($name == 'owner') {
                    $orderIds = array_merge($orderIds, explode(',', $orders));
                }
                
                $SUPPLIERS[$name]['order-ids'] = $orders;
            } catch(exception $ex) {
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        
        $bcc = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        $replyTo = AMAZON_MAIL_SUPPLIER_ADDRESS_REPLYTO;
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (array_search($name, $suppliersWithErrors) !== false) {
                    App::error('Unable to send mail for supplier ', $name);
                    continue;
                }
                if ($name != 'owner' && !$SUPPLIERS[$name]['order-ids']) {
                    continue;
                }
                if (!is_array($data['attachment'])) {
                    $data['attachment'] = array($data['attachment']);
                }
                $mailTo = $data['address'];
                
                $attachments = array();
                foreach($data['attachment'] as $attachment) {
                    $attachmentPath = $attachment;
                    if (!file_exists($attachmentPath)) {
                        continue;
                    }
                    $attachmentName = substr($attachmentPath, strrpos($attachmentPath, '/')+1);
                    $attachments[] = array('path' => $attachmentPath, 'name' => $attachmentName);
                }
                
                $debugMailTo = AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO;
                $mailTo = (!empty($debugMailTo) ? $debugMailTo : $mailTo);
                $bcc = (!empty($debugMailTo) ? false : $bcc);
                
                $from = $replyTo;
                
                if (!is_array($mailTo)) {
                    $mailTo = array($mailTo);
                }
                
                $content = file_get_contents($data['template']);
                foreach($mailTo as $to) {
                    $this->_sendMail($from, $to, $data['subject'], $content, $bcc, false, $attachments);
                }
            } catch(exception $ex) {
                $suppliersWithErrors[] = $name;
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                $attachments = $data['attachment'];
                if (!is_array($attachments)) {
                    $attachments = array($attachments);
                }
                foreach($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        unlink($attachment);
                    }
                }
                if (file_exists($data['order-file'])) {
                    unlink($data['order-file']);
                }
            } catch(exception $ex) {
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        App::debug($orderIds);
        if (true) {
            foreach($orderIds as &$orderId) {
                $orderId = substr($orderId, 0, 3) . '-' . substr($orderId, 3, 7) . '-' . substr($orderId, 10);
            }
            if ($orderIds) {
                $this->_ship($orderIds);
            }
        }
    }
    
    private function _sendMails($orders, $templateUri)
    {
        if (!file_exists($templateUri)) {
            throw new Exception('No template file at ' . $templateUri);
        }
        if (!$orders) {
            return ;
        }
        $templateContent = file_get_contents($templateUri);
        $templateContent = iconv('UTF-8','UTF-8//IGNORE', $templateContent); 
        $orderMap = App::toMapArray($orders, 'order_id');
        $exceptions = array();
        foreach($orderMap as $orderId => $orders) {
            try {
                $reference = $orders[0];
                $mailContent = $this->_prepareMailContent($reference, $orders, $templateContent);
                if (!$mailContent) {
                    throw new Exception('No valid mail content for ' . $templateUri);
                }
                $subject = AMAZON_MAIL_SUBJECT;
                $bcc = trim(AMAZON_MAIL_BCC);
                $from = trim(AMAZON_MAIL_FROM);
                $this->_sendMail($from, $reference['buyer_email'], $subject, $mailContent, $bcc);
            } catch(exception $ex) {
                App::ex($ex, 'sendMails');
                $exceptions[] = $ex;
            }
        }
        if ($exceptions) {
            throw new Exception($exceptions[0]);
        }
    }
    
    private function _sendMail($from, $to, $subject, $mailContent, $bcc = false, $replyTo = false, $attachments = false)
    {
        if (empty($to)) {
            throw new Exception('No mail to');
        }
        if (empty($mailContent)) {
            throw new Exception('No mail content');
        }
        if (AMAZON_SEND_MAIL_TO) {
            $to = AMAZON_SEND_MAIL_TO;
        }
        $headers  = 'MIME-Version: 1.0' . PHP_EOL;
        $headers .= "From: " . $from . PHP_EOL;
        if ($replyTo) {
            $headers .= "Reply-To: " . $replyTo . PHP_EOL;
        }
        if ($bcc) {
            $headers .= "Bcc: " . $bcc . PHP_EOL;
        }
        $headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        if (!$attachments) {
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
        } else {
            $random_hash = md5(date('r', time()));

            $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
            
            $mailContent = "
--PHP-mixed-$random_hash
Content-type: text/html; charset=iso-8859-1'

$mailContent";

            foreach($attachments as $attachment) {
                $attachmentContent = chunk_split(base64_encode(file_get_contents($attachment['path'])));
                $attachmentName = $attachment['name'];
                
                $mailContent .= "
--PHP-mixed-$random_hash
Content-Type: application/xlsx; name=$attachmentName
Content-Transfer-Encoding: base64
Content-Disposition: attachment

$attachmentContent";
            }
            
            $mailContent .= "
--PHP-mixed-$random_hash--";

        }

        /*
        App::debug('sending mail ... ');
        App::debug('from: ', $from);
        App::debug('to: ', $to);
        App::debug('subject: ', $subject);
        App::debug('content -->>');
        App::debug($mailContent);
        App::debug('<<-- content');
         */
        //'file_put_contents('mail.htm', $mailContent);
        
        if (mail($to, $subject, $mailContent, $headers) === false) {
            throw new Exception('Unable to send mail to ' . $to);
        }
    }
    
    private function _prepareMailContent($order, $orderItems, $templateContent) // all types are from db, order is reference item for set of orders
    {
        if (!$templateContent) {
            throw new Exception('No template content');
        }
        
        $content = $templateContent;
        $content = str_ireplace('{{ORDER-ID}}', $order['order_id'], $content);
        $content = str_ireplace('{{NAME}}', $order['ship_to_name'], $content);
        
        $forEachBeginPos = stripos($content, '{{FOR-EACH-BEGIN}}');
        $forEachEndPos = stripos($content, '{{FOR-EACH-END}}');
        
        $forEachContent = false;
        if ($forEachBeginPos !== false && $forEachEndPos !== false) {
            $forEachContent = substr($content, $forEachBeginPos + strlen('{{FOR-EACH-BEGIN}}'), $forEachEndPos - ($forEachBeginPos + strlen('{{FOR-EACH-BEGIN}}')));
        }
        if (!$forEachContent) {
            App::warning('No valid for each content in template file');
        } else {
            $itemContent = '';
            foreach($orderItems as $orderItem) {
                $itemContent .= str_ireplace('{{TITLE}}', $orderItem['title'], $forEachContent);
            }
            $content = substr($content, 0, $forEachBeginPos) . $itemContent . substr($content, $forEachEndPos + strlen('{{FOR-EACH-END}}'));
        }
        return $content;
    }
    
    private function _init()
    {
        App::init(AMAZON_DEBUG);
        
        $this->_db = new AmazonDatabase();
    }
    
    private function _dispose()
    {
        $from = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        $to  = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        if (AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO) {
            $from = $to = AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO;
        }
        App::mailOnErorrs($from, $to, 'Amazon CRON Job Errors');
    }

}

AmazonOrders::test();

exit;


if ($_SERVER['argc'] > 1) {
    if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'send-mails') {
        AmazonOrders::sendMails();
    } else if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'save-csv') {
        AmazonOrders::saveCsv();
    } else if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'ship-cba-orders') {
        AmazonOrders::shipCbaOrders();
    }
}

