<?php
class IWD_OrderManager_Model_Backup_Sales extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_ordermanager/backup_sales');
    }

    public function SaveBackup($obj, $items, $type)
    {
        $items_array = array();
        foreach ($items as $item) {
            $itemArray = $item->getData();
            unset($itemArray["product"]);
            $items_array[] = $itemArray;
        }

        $obj_serialize = serialize($obj->getData());
        $items_serialize = serialize($items_array);

        $user = Mage::getSingleton('admin/session')->getUser();

        $this->setDeletionAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $this->setObjectType($type);
        $this->setObject($obj_serialize);
        $this->setObjectItems($items_serialize);
        $this->setAdminUserId($user->getId());
        return $this->save();
    }
}
