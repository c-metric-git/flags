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
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Coupongenerator_Block_Adminhtml_Coupons_Grid_Column_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    protected $_row;
    protected $_separator = '&nbsp;|&nbsp;';

    public function render(Varien_Object $row)
    {
        $this->_row = $row;
        $actions = array_filter( (array) $this->getColumn()->getActions(), array($this, '_filterAction'));
        array_walk($actions, array($this, '_actionToLink'), $row);
        return implode($this->_separator, $actions);
    }

    /**
     * Used as callback
     * @param $action array
     * @param $key int
     * @param $row Varien_Object
     *
     * @return $this
     */
    protected function _actionToLink(&$action, $key, $row)
    {
        $action = $this->_toLinkHtml($action, $row);
        return $this;
    }

    protected function _filterAction($action)
    {
        if ( ! is_array($action)) {
            return false;
        }
        if (array_key_exists('filter', $action) && method_exists($this, $action['filter'])) {
            return call_user_func(array($this, $action['filter']));
        }
        return true;
    }

    protected function _isNotExpired()
    {
        return ( ! $this->_row->getExpirationDate())
         || ($this->_row->getExpirationDate() > Mage::getSingleton('core/date')->gmtDate())
        ;
    }

}
