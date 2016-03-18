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


class AW_Helpdesk3_Block_Adminhtml_Ticket_Grid_Column_Renderer_Datetime
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    /**
     * @var null|Zend_Date
     */
    protected $_dateInstance = null;

    public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            $dateInstance = $this->getDateInstance();
            $dateInstance->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
            $dateInstance->set($data, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateInstance->setTimezone(
                Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
            );
            $dateFormat = Mage::app()->getLocale()->getDateFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
            );
            $timeFormat = Mage::app()->getLocale()->getTimeFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
            );
            $date = $dateInstance->toString($dateFormat);
            $time = $dateInstance->toString($timeFormat);
            return $date . '<br/>' . $time;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * @return Zend_Date
     */
    public function getDateInstance()
    {
        if (is_null($this->_dateInstance)) {
            $this->_dateInstance = Mage::app()->getLocale()->date();
        }
        return $this->_dateInstance;
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
        return str_replace('<br/>', ' ', $result);
    }
}