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
 *  Sharing horizontalsharing source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Model_Source_HorizontalSharing which return horizontal sharing theme options
 */
class Loginradius_Sharing_Model_Source_HorizontalSharing
{
    /**
     * function return array of horizontal themes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/horizonSharing32.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => '16', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/horizonSharing16.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'responsive', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/responsive-icons.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_large', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/singleImageThemeLarge.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_small', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/singleImageThemeSmall.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/vertical.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img style="width:375px;" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sharing/images/Sharing/Horizontal/horizontal.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');

        return $result;
    }
}