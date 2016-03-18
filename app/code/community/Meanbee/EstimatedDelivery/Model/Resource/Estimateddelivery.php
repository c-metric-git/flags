<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery extends Mage_Core_Model_Mysql4_Abstract {

    protected $_serializableFields = array(
        'deliverable_days'  => array(null, array()),
        'dispatchable_days' => array(null, array())
    );
    protected $_methodTable;

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery', 'entity_id');
        $this->_methodTable = $this->getTable('meanbee_estimateddelivery/estimateddelivery_method');
    }

    public function loadByShippingMethod(Meanbee_EstimatedDelivery_Model_Estimateddelivery $object, $shippingMethod) {
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->joinLeft($this->_methodTable, 'entity_id = estimated_delivery_id', '')
                ->where("shipping_method = ?", $shippingMethod);

            $data = $read->fetchRow($select);
            $object->setData($data);
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

    public function addShippingMethods(Meanbee_EstimatedDelivery_Model_Estimateddelivery $object) {
        $read = $this->_getReadAdapter();
        if ($read) {
            $methodSelect = $this->_getReadAdapter()->select()
                ->from($this->_methodTable)
                ->where("estimated_delivery_id = ?", $object->getId());

            $methodData = $read->fetchCol($methodSelect, array('shipping_method'));
            $object->setData('shipping_methods', $methodData);
        }

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        parent::_afterSave($object);

        $id = $object->getId();
        $insert = $object->getData('shipping_methods');

        $adapter = $this->_getWriteAdapter();

        // Perform deletes
        $cond = array('estimated_delivery_id=?' => $id);
        $adapter->delete($this->_methodTable, $cond);

        // Perform inserts
        if ($insert && $adapter) {
            $data = array();
            foreach ($insert as $shipping_method) {
                $data[] = array(
                    'shipping_method'       => $shipping_method,
                    'estimated_delivery_id' => (int)$id
                );
            }
            $adapter->insertMultiple($this->_methodTable, $data);
        }

        return $this;
    }

    protected function _getExistingShippingMethods($object) {
        $read = $this->_getReadAdapter();
        $methods = $object->getData('shipping_methods');
        $result = false;

        if ($read) {
            $select = $read->select()
                ->from($this->_methodTable)
                ->where('shipping_method IN (?)', $methods)
                ->where('estimated_delivery_id <> ?', $object->getId());

            $result = $read->fetchAll($select);
        }

        return $result;
    }

    public function handleExistingShippingMethods($object) {
        if ($existingMethods = $this->_getExistingShippingMethods($object)) {
            // Collect output into a 1D array
            $markupList = '<ul>';
            foreach ($existingMethods as $method) {
                $markupList .= sprintf('<li>%s - <a href="%s">rule %s</a></li>',
                    $method['shipping_method'],
                    Mage::helper('adminhtml')->getUrl('adminhtml/estimateddelivery/edit/', array('id' => $method['estimated_delivery_id'])),
                    $method['estimated_delivery_id']
                );
            }
            $markupList .= '</ul>';
            $warningMessage = sprintf('You already have information stored in the database for some of these methods. We are unable to determine which of these will show on the frontend - we recommend only storing one. %s', $markupList);
            Mage::getSingleton('adminhtml/session')->addWarning($warningMessage);
        }
    }

}
