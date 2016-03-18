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
 *  Sharing verticalsharing source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Model_Source_VerticalSharing which return vertical sharing theme options
 */
class Loginradius_Sharing_Model_Source_VerticalSharing
{
    /**
     * function return array of vertical themes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img style="margin:0 5px 0 4px" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Vertical/32VerticlewithBox.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => '16', 'label' => '<img style="margin:0 5px 0 4px" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Vertical/16VerticlewithBox.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img style="margin:0" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Vertical/verticalvertical.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img style="margin:0" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Vertical/verticalhorizontal.png', array('_area' => 'adminhtml')) . '" /><br />');

        return $result;
    }
}