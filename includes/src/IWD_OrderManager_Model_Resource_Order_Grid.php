<?php
class IWD_OrderManager_Model_Resource_Order_Grid extends Varien_Data_Collection
{
    private $collection;

    public function setMyCollection($collection)
    {
        $this->collection = $collection;
    }

    public function getSize()
    {
        return $this->collection->getSize();
    }
}
