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


class AW_Coupongenerator_Model_Source_EmailTemplate extends Varien_Object
{
    /**
     * @return array
     */
    public function toOptionHash()
    {
        // toOptionHash has not been implemented yet
        // in Mage_Core_Model_Resource_Email_Template_Collection
        $options = array();
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = Mage::getResourceModel('core/email_template_collection')->toOptionArray();
        $templateName = Mage::helper('coupongenerator')->__('Default Template from Locale');
        $nodeName = $this->getNodeName();
        $templateLabelNode = Mage::app()->getConfig()->getNode("global/template/email/{$nodeName}/label");
        if ($templateLabelNode) {
            $templateName = Mage::helper('coupongenerator')->__( (string) $templateLabelNode);
            $templateName = Mage::helper('coupongenerator')->__('%s (Default Template from Locale)', $templateName);
        }
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
        return $options;
    }

    /**
     * @return string
     */
    public function getNodeName()
    {
        return 'coupongenerator_notifications_coupon_generation_template';
    }
}
