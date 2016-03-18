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
 *  Sharing socialsharing block
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Block_Help which is responsible for generating help section on configuration page
 */
class Loginradius_Sharing_Block_Help extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Function responsible for rendering this block
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= $this->_getFieldHtml($element);
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Actual function for adding important links
     *
     * @param $element
     *
     * @return string
     */
    protected function _getFieldHtml($element)
    {
        return '<tr><td><a href="http://ish.re/RPMU" target="_blank">Extension Installation, Configuration and Troubleshooting</a></td></tr>
            <tr><td><a href="http://ish.re/ROSR" target="_blank">Advance Feature</a></td></tr>
            <tr><td><a href="http://ish.re/ROVE" target="_blank">Social Plugins</a></td></tr>';
    }
}