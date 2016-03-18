<?php
class IWD_OrderManager_Helper_Downloadable extends Mage_Core_Helper_Abstract
{
    public function getSupportPeriodDate($order_item)
    {
        if (!Mage::getConfig()->getModuleConfig('IWD_Downloadable')->is('active', 'true'))
            return null;

        $purchased_items = Mage::getModel("downloadable/link_purchased_item")->getCollection()
            ->addFieldToFilter('order_item_id', $order_item->getId())
            ->addFieldToFilter('link_title',
                array('in' => array(
                    IWD_Downloadable_Block_Customer_Products_List::COMMUNITY,
                    IWD_Downloadable_Block_Customer_Products_List::ENTERPRISE
                ))
            );

        if (!empty($purchased_items)) {
            $support_date = $purchased_items->getFirstItem();
            if (isset($support_date))
                $support_date = $support_date->getSupportAt();
            if (isset($support_date)) {
                $support_date = Mage::getModel('core/date')->timestamp($support_date);
                return date('Y-m-d', $support_date);
            }
        }

        return null;
    }

    public function getSupportPeriod($order_item)
    {
        if (!Mage::getConfig()->getModuleConfig('IWD_Downloadable')->is('active', 'true'))
            return null;

        $purchased_items = Mage::getModel("downloadable/link_purchased_item")->getCollection()
            ->addFieldToFilter('order_item_id', $order_item->getId())
            ->addFieldToFilter('link_title',
                array('in' => array(
                    IWD_Downloadable_Block_Customer_Products_List::COMMUNITY,
                    IWD_Downloadable_Block_Customer_Products_List::ENTERPRISE
                ))
            );

        if (!empty($purchased_items)) {

            $support_period = array();

            foreach ($purchased_items as $item) {
                $support_at_date = $item->getSupportAt();
                $support_date = Mage::getModel('core/date')->timestamp($support_at_date);

                $dNow = new DateTime('now');
                $dSupport = new DateTime(date('Y-m-d', $support_date));
                $dDiff = $dNow->diff($dSupport);

                $style = $dDiff->format('%R') == '-' ? "color:red" : "color:green";

                $support_period[] =
                    '<span style="' . $style . '">'
                    . date('Y-m-d', $support_date) . " (" . $dDiff->format('%R') . $dDiff->days . " days)"
                    . '<span>';
            }
            return implode(', ', $support_period);
        }

        return null;
    }

    public function getCountOfDownloads($order_item)
    {
        if (!Mage::getConfig()->getModuleConfig('IWD_Downloadable')->is('active', 'true'))
            return null;

        $_items = Mage::getModel("iwd_downloadable/downloadcount")->getCollection()
            ->addFieldToFilter('order_item_id', $order_item->getId());

        $versions = Mage::getModel('iwd_downloadable/versions');
        if (!empty($_items)) {
            $counts = array();
            foreach ($_items as $item) {
                $counts[] = $versions->load($item->getVersionId())->getVersion() . " (" . $item->getCount() . ")";
            }

            return implode(', ', $counts);
        }

        return "";
    }

    public function updateSupportPeriod($order_item_id, $support_date){
        if (!Mage::getConfig()->getModuleConfig('IWD_Downloadable')->is('active', 'true'))
            return null;

        $purchased_items = Mage::getModel("downloadable/link_purchased_item")->getCollection()
            ->addFieldToFilter('order_item_id', $order_item_id)
            ->addFieldToFilter('link_title',
                array('in' => array(
                    IWD_Downloadable_Block_Customer_Products_List::COMMUNITY,
                    IWD_Downloadable_Block_Customer_Products_List::ENTERPRISE
                ))
            );

        if (!empty($purchased_items)) {
            foreach ($purchased_items as $item) {
                $support_date = Mage::getModel('core/date')->timestamp($support_date);
                $item->setSupportAt($support_date)->save();
            }
        }
    }
}