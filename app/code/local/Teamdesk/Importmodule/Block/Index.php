<?php

/**
 * Import product block
 *
 * @category   Mageworks
 * @package    Mageworks_Import
 * @author     Mageworks <magentoworks.net@gmail.com>
 */
class Teamdesk_Importmodule_Block_Index extends Mage_Adminhtml_Block_Abstract
{
	public function getTitle() {     
        $types = $this->__('Import Data From Teamdesk');
        if (isset($types)) return $types;
        return '';
    }
}
