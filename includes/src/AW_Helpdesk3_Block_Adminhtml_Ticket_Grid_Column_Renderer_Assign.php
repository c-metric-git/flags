<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Helpdesk3
 * @version    3.3.7
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Helpdesk3_Block_Adminhtml_Ticket_Grid_Column_Renderer_Assign
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $id = $row->getId();
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/awhdu3_ticket/ajaxGetAssigneePopupProperties',
            array('id' => $id)
        );
        return "<a href='{$url}'"
            . "onclick='return awHDU3TicketGrid.openAssigneePopup(\"{$url}\", {$id});'"
            . ">"
            . $this->__('Manage')
            . "</a>"
            . "<script>var awHDU3TicketGridAssigneePopupList = awHDU3TicketGridAssigneePopupList || {};</script>"
        ;
    }

    /**
     * Render column for export
     *
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        $result = parent::renderExport($row);
        return strip_tags($result);
    }
}