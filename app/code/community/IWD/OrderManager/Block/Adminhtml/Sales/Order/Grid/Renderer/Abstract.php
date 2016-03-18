<?php
abstract class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $row;

    public function render(Varien_Object $row)
    {
        $this->row = $row;

        $export = Mage::helper('iwd_ordermanager')->isGridExport();
        if ($export) {
            return $this->Export();
        }

        return $this->Grid();
    }

    protected function getOrderId()
    {
        if (!isset($this->row['entity_id']) || empty($this->row['entity_id'])) {
            if (!isset($this->row['increment_id']) || empty($this->row['increment_id'])) {
                return null;
            }
            return Mage::getModel('sales/order')->loadByIncrementId($this->row['increment_id'])->getEntityId();
        }

        return $this->row['entity_id'];
    }

    protected function formatBigData(array $data)
    {
        $result = '<div style="position:relative"><ul class="iwd_order_items_in_grid">';
        $count = 0;

        foreach ($data as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $result .= '<li style="margin:3px;">&bull;&nbsp;' . $item . '</li>';
                $count++;
            }
        }

        $id = $this->getOrderId();
        $result .= $count > 4 ? sprintf('</ul><a class="iwd_order_grid_more show row-%s" href="javascript:void(0);" data-row-id="%s" title="%s"></a>',
            $id, $id, Mage::helper('iwd_ordermanager')->__('Show/hide'))
            : '</ul></div>';

        return $result;
    }

    abstract protected function Grid();

    abstract protected function Export();
}