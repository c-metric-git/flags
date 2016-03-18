<?php
class IWD_OrderManager_Helper_Data extends Mage_Core_Helper_Data
{
    const CONFIG_XML_PATH_SHOW_ITEM_IMAGE = 'iwd_ordermanager/edit/show_item_image';
    const CONFIG_XML_CUSTOM_GRID_ENABLE = 'iwd_ordermanager/grid_order/enable';
    const CONFIG_XML_CONFIRM_EDIT_CHECKED = 'iwd_ordermanager/edit/confirm_edit_checked';
    const CONFIG_XML_NOTIFY_CUSTOMER_CHECKED = 'iwd_ordermanager/edit/notify_checked';
    const CONFIG_XML_PATH_RECALCULATE_ORDER_AMOUNT = 'iwd_ordermanager/edit/recalculate_amount_checked';
    const CONFIG_XML_PATH_VALIDATE_INVENTORY = 'iwd_ordermanager/edit/validate_inventory';

    public function isGridExport()
    {
        $path = "";
        if (isset($_SERVER['PATH_INFO'])) {
            $path = $_SERVER['PATH_INFO'];
        } else if(isset($_SERVER['REQUEST_URI'])){
            $path = $_SERVER['REQUEST_URI'];
        }
        $exportCsv = (strstr($path, 'exportCsv') !== false);
        $exportExcel = (strstr($path, 'exportExcel') !== false);
        return $exportCsv || $exportExcel;
    }


    public function isRecalculateOrderAmountChecked()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_RECALCULATE_ORDER_AMOUNT, Mage::app()->getStore()) ? 'checked="checked"' : "";
    }

    public function isValidateInventory()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_VALIDATE_INVENTORY, Mage::app()->getStore()) ? 1 : 0;
    }

    public function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_OrderManager")->version;
    }

    public function CheckTableEngine($table)
    {
        try {
            $dbname = (string)Mage::getConfig()->getResourceConnectionConfig('default_setup')->dbname;
            $sql = "SELECT engine FROM `information_schema`.`tables` WHERE `table_schema`='{$dbname}' AND `table_name`='{$table}'";
            $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
            return $data[0]["engine"] == "InnoDB";
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
        }
        return false;
    }

    public function isShowItemImage()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_SHOW_ITEM_IMAGE, Mage::app()->getStore());
    }

    public function isConfirmEditChecked()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_CONFIRM_EDIT_CHECKED) ? 'checked="checked"' : "";
    }

    public function isNotifyCustomerCheckedDefault()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_NOTIFY_CUSTOMER_CHECKED);
    }

    public function isNotifyCustomerChecked()
    {
        return $this->isNotifyCustomerCheckedDefault() ? 'checked="checked"' : "";
    }

    public function enableCustomGrid()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_CUSTOM_GRID_ENABLE);
    }

    public function CheckOrderTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
        return $this->CheckTableEngine($table);
    }

    public function CheckCreditmemoTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo');
        return $this->CheckTableEngine($table);
    }

    public function CheckInvoiceTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
        return $this->CheckTableEngine($table);
    }

    public function CheckShipmentTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
        return $this->CheckTableEngine($table);
    }

    protected $_version = 'CE';

    public function isEnterpriseMagentoEdition()
    {
        $mage = new Mage();
        $edition = 'Community';
        if (is_callable(array($mage, 'getEdition'))) {
            $edition = Mage::getEdition();
        }
        unset($mage);
        return ($edition == 'Enterprise');
    }

    public function isAvailableVersion()
    {
        $mage = new Mage();
        if (!is_callable(array($mage, 'getEdition'))) {
            $edition = 'Community';
        } else {
            $edition = Mage::getEdition();
        }
        unset($mage);

        if ($edition == 'Enterprise' && $this->_version == 'CE') {
            return false;
        }
        return true;
    }

    public function getCurrentIpAddress()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }

    public function getDataTimeFormat()
    {
        return 'm-d-Y H:i:s';
    }

    public function getDateTime($date)
    {
        $store_id = null;
        $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $store_id);
        $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store_id));
        $date = new Zend_Date(strtotime($date), null, $locale);
        $date->setTimezone($timezone);
        return $date->get('MM-dd-Y H:m:s');
    }

}