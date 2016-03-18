<?php
class IWD_OrderManager_Block_System_Config_Form_Fieldset_Runarchive extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $archive_title = Mage::helper('iwd_ordermanager')->__("Archive All");
        $restore_title = Mage::helper('iwd_ordermanager')->__("Restore All");

        $_secure = Mage::app()->getStore()->isCurrentlySecure();
        $archive_link = Mage::helper("adminhtml")->getUrl('adminhtml/sales_archive_order/archivemanually', array('_secure' => $_secure));
        $restore_link = Mage::helper("adminhtml")->getUrl('adminhtml/sales_archive_order/restoremanually', array('_secure' => $_secure));

        return '<button style="margin-right:120px;" type="button" onclick="setLocation(\''.$archive_link.'\')">'.$archive_title.'</button>'.
               '<button type="button" onclick="setLocation(\''.$restore_link.'\')">'.$restore_title.'</button>';
    }
}