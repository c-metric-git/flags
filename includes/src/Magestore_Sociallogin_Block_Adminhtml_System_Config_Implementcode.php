<?php

class Magestore_Sociallogin_Block_Adminhtml_System_Config_Implementcode extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $layout  =  Mage::helper('sociallogin')->returnlayout();
        $block = Mage::helper('sociallogin')->returnblock();
        $text =  Mage::helper('sociallogin')->returntext();
        $template = Mage::helper('sociallogin')->returntemplate();
        return '
<!-- <div class="entry-edit-head collapseable"><a onclick="Fieldset.toggleCollapse(\'sociallogin_template\'); return false;" href="#" id="sociallogin_template-head" class="open">Implement Code</a></div> -->
<input id="sociallogin_template-state" type="hidden" value="1" name="config_state[sociallogin_template]">
<fieldset id="sociallogin_template" class="config collapseable" style="">
    <div id="messages" class="div-mess-sociallogin">
        <ul class="messages mess-megamennu">
            <li class="notice-msg notice-sociallogin">
                <ul>
                    <li>
                    '.$text.'
                    </li>				
                </ul>
            </li>
        </ul>
    </div>
    <br/>  
    <div id="messages" class="div-mess-sociallogin">
        <ul class="messages mess-megamennu">
            <li class="notice-msg notice-sociallogin">
                <ul>
                    <li>
                    '.Mage::helper('sociallogin')->__('Option 1: Add the code below to a CMS Page or a Static Block').'
                    </li>
                </ul>
            </li>
        </ul>
    </div>
        <ul>
            <li>
                <code>
                '.$block.'
                </code>	
            </li>
        </ul>     
    <br/>
    <div id="messages" class="div-mess-sociallogin">
       <ul class="messages mess-megamennu">
            <li class="notice-msg notice-sociallogin">
                <ul>
                    <li>
                    '.Mage::helper('sociallogin')->__('Option 2: Add the code below to a template file').'
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <ul>
        <li>
            <code>
            '.$template.'
            </code>	
        </li>
    </ul>
    <br/>
    <div id="messages" class="div-mess-sociallogin">
        <ul class="messages mess-megamennu">
            <li class="notice-msg notice-sociallogin">
                <ul>
                    <li>
                    '.Mage::helper('sociallogin')->__('Option 3: Add the code below to a layout file').'
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <ul>
        <li>
            <code>
            '.$layout.'
            </code>	
        </li>
    </ul>
</fieldset>
'. $this->addPreview();
    }
    
    protected function addPreview(){
        $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/sociallogin';  
        $html = '<script>document.observe("dom:loaded", function() {';
        $html .= 'addNewImageElement("sociallogin_fblogin_app_id","'.$mediaUrl.'/blank_appstore_registration_mini.png");';
        $html .= 'addNewImageElement("sociallogin_fblogin_app_secret","'.$mediaUrl.'/blank_appstore_registration_mini.png");';
        $html .= 'addNewImageElement("sociallogin_twlogin_consumer_key","'.$mediaUrl.'/blank_appstore_registration_mini.png");';
        $html .= '});</script>';
        return $html;
    }
}
