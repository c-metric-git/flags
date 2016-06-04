<?php

/**
 * Import product block
 *
 * @category   Mageworks
 * @package    Mageworks_Import
 * @author     Mageworks <magentoworks.net@gmail.com>
 */
class Stripedsocks_AttributeController_Block_Index extends Mage_Adminhtml_Block_Abstract
{
	public function getTitle() {     
        $types = $this->__('Crud Operation For Attribute Options');
        if (isset($types)) return $types;
        return '';
    }
}
