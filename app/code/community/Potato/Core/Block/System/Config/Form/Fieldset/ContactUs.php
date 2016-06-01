<?php

class Potato_Core_Block_System_Config_Form_Fieldset_ContactUs extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_prepareElement($element);
        return parent::render($element);
    }

    protected function _prepareElement(Varien_Data_Form_Element_Abstract $fieldset)
    {
        $contactFromBlock = Mage::app()->getLayout()->createBlock('core/template')
            ->setTemplate('po_core/system/config/contact_us.phtml')
        ;
        $labelElement = new Varien_Data_Form_Element_Label(
            array(
                 'value' => '',
                 'after_element_html' => $contactFromBlock->toHtml()
            )
        );
        $labelElement->setId('po-contactus-container');
        $fieldset->addElement($labelElement);
        return $this;



        $text = new Varien_Data_Form_Element_Note(
            array(
                'text' => '<img class="po-core-help" src="' . Mage::getDesign()->getSkinUrl('po_core/images/help.png') . '"/>'
                    . $this->__(
                        'Need some <strong>help</strong> or <strong>customization</strong>? Submit a ticket from this form.'
                    ),
                'after_element_html' => '<div class="po-core-two-rows"><div>',
                'no_span' => true
            )
        );
        $text->setId('contact_us_text');
        $name = new Varien_Data_Form_Element_Text(
            array(
                'name'  => 'contact_us_name',
                'label' => $this->__('Name'),
                'after_element_html' => '</div><div>'
            )
        );
        $name->setId('contact_us_name');
        $email = new Varien_Data_Form_Element_Text(
            array(
                'class' => 'validate-email',
                'name'  => 'contact_us_email',
                'label' => $this->__('Email'),
                'after_element_html' => '</div></div><div style="clear:both"></div>'
            )
        );
        $email->setId('contact_us_email');
        $subject = new Varien_Data_Form_Element_Text(
            array(
                'name'  => 'contact_us_subject',
                'label' => $this->__('Subject'),
            )
        );
        $subject->setId('contact_us_subject');
        $textArea = new Varien_Data_Form_Element_Textarea(
            array (
                'class'              => 'contact-us-area',
                'cols'               => 15,
                'rows'               => 2,
                'label'              => $this->__('Message'),
                'name'               => 'contact_message',
                'after_element_html' => '<script type="text/javascript">var contactForm = new PotatoContactForm();'
                    . '</script><div class="buttons">',
            )
        );
        $textArea->setId('contact_us_area');
        $button = new Varien_Data_Form_Element_Button(
            array (
                'onclick' => 'contactForm.submit()',
                'class'   => 'form-button contact-button-submit',
                'value'   => $this->__('Send'),
                'no_span' => true,
                'after_element_html' => '</div>'

            )
        );
        $button->setId('contact_button_submit');
        $element
            ->addElement($text)
            ->addElement($name)
            ->addElement($email)
            ->addElement($subject)
            ->addElement($textArea)
            ->addElement($button)
        ;
        return $this;
    }
}