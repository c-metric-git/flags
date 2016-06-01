<?php

class Potato_Core_Block_System_Config_Form_Fieldset_AboutUs extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_prepareElement($element);
        return parent::render($element);
    }

    protected function _prepareElement(Varien_Data_Form_Element_Abstract $element)
    {
        $text = new Varien_Data_Form_Element_Note(
            array(
                'text' => $this->__('We are <a href="%s" target="_blank" title="PotatoCommerce Team">'
                    . 'PotatoCommerce Team</a>. We have huge experience in development.</br>'
                    . 'We have been developing Magento extensions since 2010.</br>'
                    . 'Now we develop under <a href="%s" target="_blank" title="PotatoCommerce">PotatoCommerce</a> brand.</br>'
                    . 'Our core is four Magento Certified Developers.</br>'
                    . 'Our slogan is "Excellent Products & Services by Excellent Engineers"</br></br>'
                    . 'Visit our <a href="%s" target="_blank" title="Magento Extensions store">Magento Extensions store</a>',
                        Potato_Core_Model_Source_Feed::POTATOCOMMERCE_URL, Potato_Core_Model_Source_Feed::POTATOCOMMERCE_URL,
                        Potato_Core_Model_Source_Feed::POTATOCOMMERCE_URL
                    )
            )
        );
        $text->setId('about_us_text');
        $element->addElement($text);
        return $this;
    }
}