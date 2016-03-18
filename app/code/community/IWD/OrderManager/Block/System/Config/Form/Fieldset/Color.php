<?php
class IWD_OrderManager_Block_System_Config_Form_Fieldset_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const XML_PATH_ORDER_GRID_STATUS_COLOR = 'iwd_ordermanager/grid_order/status_color';

    protected $status_color_element = "";

    public function getStatusColor()
    {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_GRID_STATUS_COLOR);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->prependColorElement();
        $this->addItemsToColorElement();
        $this->appendColorElement();

        return $this->status_color_element;
    }

    protected function addItemsToColorElement()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();

        foreach ($statuses as $code => $label) {
            $this->addListItemToColorElement($code, $label);
        }
    }

    protected function addListItemToColorElement($code, $label)
    {
        $clear_button = $this->getClearColorButton();
        $this->status_color_element .= '<li id="' . $code . '"><span class="color-box">' . $label . '</span>' . $clear_button . '</li>';
    }

    protected function getClearColorButton()
    {
        $clear_text = Mage::helper('iwd_ordermanager')->__('Clear color');
        return '<span class="clear-color" title="' . $clear_text . '">X<span>';
    }

    protected function prependColorElement()
    {
        $this->status_color_element .= '<ul id="order_status_color">';
    }

    protected function appendColorElement()
    {
        $this->status_color_element .= '</ul><input type="hidden" id="iwd_ordermanager_grid_order_status_color"
        name="groups[grid_order][fields][status_color][value]" value="' . $this->getStatusColor() . '" />';
    }
}