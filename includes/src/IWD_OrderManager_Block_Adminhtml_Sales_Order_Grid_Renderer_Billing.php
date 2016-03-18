<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Billing extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        $address = '<span class="nowrap">' . $row["billing_street"] . "<br>";
        if ($row["billing_city"]) $address .=  $row["billing_city"] . ", ";
        if ($row["billing_region"]) $address .=  $row["billing_region"] . ", ";
        $address .=  $row["billing_postcode"] . "<br>" .
            Mage::getModel('directory/country')->load($row["billing_country_id"])->getName();
        if ($row["billing_telephone"]) $address .= "<br>T:&nbsp;" . $row["billing_telephone"];
        if ($row["billing_fax"]) $address .= "<br>F:&nbsp;" . $row["billing_fax"];
        return $address . '</span>';
    }

    private function Export($row)
    {
        $address = $row["billing_street"] . ", " .
            $row["billing_city"] . ", " .
            $row["billing_region"] . ", " .
            $row["billing_postcode"] . ", " .
            Mage::getModel('directory/country')->load($row["billing_country_id"])->getName();
        if ($row["billing_telephone"]) $address .= " (T: " . $row["billing_telephone"] . ") ";
        if ($row["billing_fax"]) $address .= "(F: " . $row["billing_fax"] . ")";
        return $address;
    }
}
