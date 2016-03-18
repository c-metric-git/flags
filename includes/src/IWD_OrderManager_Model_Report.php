<?php
class IWD_OrderManager_Model_Report extends Mage_Reports_Model_Resource_Report_Abstract
{
    protected function _construct()
    {
        $this->_setResource('sales');
    }

    private $arr = array(
        'invoiced'    => array(
            'sourceTable'    => 'sales/report_invoiced',
            'table'          => 'sales/invoiced_aggregated',
            'tableOrder'     => 'sales/invoiced_aggregated_order',
            'createdAt'      => array(),
            'updatedAt'      => array(),
            'orderCreatedAt' => array(),
        ),
        'refunded'    => array(
            'sourceTable'    => 'sales/report_refunded',
            'table'          => 'sales/refunded_aggregated',
            'tableOrder'     => 'sales/refunded_aggregated_order',
            'createdAt'      => array(),
            'updatedAt'      => array(),
            'orderCreatedAt' => array(),
        ),
        'shipping'    => array(
            'sourceTable'    => 'sales/report_shipping',
            'table'          => 'sales/shipping_aggregated',
            'tableOrder'     => 'sales/shipping_aggregated_order',
            'createdAt'      => array(),
            'updatedAt'      => array(),
            'orderCreatedAt' => array(),
        ),
        'order'    => array(
            'sourceTable'    => 'sales/report_order',
            'table'          => 'sales/order_aggregated_created',
            'tableOrder'     => 'sales/order_aggregated_updated',
            'createdAt'      => array(),
            'updatedAt'      => array(),
            'orderCreatedAt' => array(),
        ),
    );

    public function addInvoicedPeriod($createdAt, $updatedAt, $orderCreatedAt)
    {
        $this->addInvoicedCreatedAt($createdAt);
        $this->addInvoicedUpdatedAt($updatedAt);
        $this->addInvoicedOrderCreatedAt($orderCreatedAt);
        return $this;
    }
    public function addRefundedPeriod($createdAt, $updatedAt, $orderCreatedAt)
    {
        $this->addRefundedCreatedAt($createdAt);
        $this->addRefundedUpdatedAt($updatedAt);
        $this->addRefundedOrderCreatedAt($orderCreatedAt);
        return $this;
    }
    public function addShippingPeriod($createdAt, $updatedAt, $orderCreatedAt)
    {
        $this->addShippingCreatedAt($createdAt);
        $this->addShippingUpdatedAt($updatedAt);
        $this->addShippingOrderCreatedAt($orderCreatedAt);
        return $this;
    }
    public function addOrderPeriod($createdAt, $updatedAt)
    {
        $this->addOrderCreatedAt($createdAt);
        $this->addOrderUpdatedAt($updatedAt);
        return $this;
    }

    public function addInvoicedCreatedAt($date)
    {
        $this->arr['invoiced']['createdAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addInvoicedUpdatedAt($date)
    {
        $this->arr['invoiced']['updatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addInvoicedOrderCreatedAt($date)
    {
        $this->arr['invoiced']['orderCreatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addShippingCreatedAt($date)
    {
        $this->arr['shipping']['createdAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addShippingUpdatedAt($date)
    {
        $this->arr['shipping']['updatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addShippingOrderCreatedAt($date)
    {
        $this->arr['shipping']['orderCreatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addRefundedCreatedAt($date)
    {
        $this->arr['refunded']['createdAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addRefundedUpdatedAt($date)
    {
        $this->arr['refunded']['updatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addRefundedOrderCreatedAt($date)
    {
        $this->arr['refunded']['orderCreatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addOrderCreatedAt($date)
    {
        $this->arr['order']['createdAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }
    public function addOrderUpdatedAt($date)
    {
        $this->arr['order']['updatedAt'][date('Y-m-d', strtotime($date))] = date('Y-m-d', strtotime($date));
        return $this;
    }

    public function AggregateSales()
    {
        foreach($this->arr as $a)
        {
            foreach($a['createdAt'] as $from){
                $this->clearTableByDateRange($a['table'], $from);
            }

            foreach($a['orderCreatedAt'] as $from){
                $this->clearTableByDateRange($a['tableOrder'], $from);
            }

            $temp = array_merge($a['createdAt'], $a['updatedAt'], $a['orderCreatedAt']);

            foreach($temp as $from)
            {
                $to = date('Y-m-d', strtotime($from. ' + 1 days'));
                Mage::getResourceModel($a['sourceTable'])->aggregate($from, $to);
            }
        }
   }

    protected function clearTableByDateRange($table, $from, $to = null)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connection->beginTransaction();

        $condition = array();

        if ($from !== null && $to !== null)
        {
            $condition[] = $this->_getWriteAdapter()->quoteInto('period >= ?', $from);
            $condition[] = $this->_getWriteAdapter()->quoteInto('period <= ?', $to);
        }
        if ($from !== null && $to === null){
            $condition[] = $this->_getWriteAdapter()->quoteInto('period = ?', $from);
        }

        $deleteCondition = implode(' AND ', $condition);

        $connection->delete($this->getTable($table), $deleteCondition);
        $connection->commit();
    }
}
