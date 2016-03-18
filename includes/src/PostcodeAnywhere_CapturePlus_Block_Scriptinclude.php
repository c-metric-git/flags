<?php

class PostcodeAnywhere_CapturePlus_Block_ScriptInclude extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
		/**
		 * Minified files used by default.
		 * Full files are available (without .min) for debugging.
		 * Only use the minified version if you use JS merging!
		 */
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
		    if (Mage::getStoreConfig('captureplus/settings/minify_script')) {
    			$head->addCss('captureplus/address-3.40.min.css');
				$head->addJs('captureplus/address-3.40.min.js');			
			}
			else {
    			$head->addCss('captureplus/address-3.40.css');
				$head->addJs('captureplus/address-3.40.js');	
			}
        }
    }
}
