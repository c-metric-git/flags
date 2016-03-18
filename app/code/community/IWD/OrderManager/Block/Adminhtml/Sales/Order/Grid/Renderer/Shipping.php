<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $path = $_SERVER['PATH_INFO'];
        $exportCsv = (strstr($path, 'exportCsv') !== false);
        $exportExcel = (strstr($path, 'exportExcel') !== false);

        if ($exportCsv || $exportExcel)
            return $this->Export($row);

        return $this->Grid($row);
    }

    private function Grid($row)
    {
        $address = '<span class="nowrap">' .  $row["shipping_street"] . "<br>";
        if ($row["shipping_city"]) $address .=  $row["shipping_city"] . ", ";
        if ($row["shipping_region"]) $address .=  $row["shipping_region"] . ", ";
        $address .= $row["shipping_postcode"] . "<br>" .
            Mage::getModel('directory/country')->load($row["shipping_country_id"])->getName();
        if ($row["shipping_telephone"]) $address .= "<br>T:&nbsp;" . $row["shipping_telephone"];
        if ($row["shipping_fax"]) $address .= "<br>F:&nbsp;" . $row["shipping_fax"];
        return $address . '</span>';
    }

    private function Export($row)
    {
        $address = $row["shipping_street"] . ", " .
            $row["shipping_city"] . ", " .
            $row["shipping_region"] . ", " .
            $row["shipping_postcode"] . ", " .
            Mage::getModel('directory/country')->load($row["shipping_country_id"])->getName();
        if ($row["shipping_telephone"]) $address .= " (T: " . $row["shipping_telephone"] . ") ";
        if ($row["shipping_fax"]) $address .= "(F: " . $row["shipping_fax"] . ")";
        return $address;
    }
}
