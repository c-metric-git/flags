<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *  Sharing horizontalsharing block
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Block_Horizontalsharing which is responsible for horizontal social sharing interface generation.
 */
class Loginradius_Sharing_Block_Horizontalsharing extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    private $_loginRadiusHorizontalSharing;

    public function __construct()
    {
        $this->_loginRadiusHorizontalSharing = new Loginradius_Sharing_Block_Sharing();
    }

    /**
     * Used to get horizontal social sharing container
     *
     * @return string
     */
    protected function _toHtml()
    {
        $content = "";
        if ($this->_loginRadiusHorizontalSharing->horizontalShareEnable() == "1") {
            $content = "<div class='loginRadiusHorizontalSharing'></div>";
        }

        return $content;
    }

    /**
     * Override  _prepareLayout method
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
}