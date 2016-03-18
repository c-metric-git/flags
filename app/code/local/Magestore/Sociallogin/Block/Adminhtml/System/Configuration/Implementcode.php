<?php

class Magestore_Sociallogin_Block_Adminhtml_System_Configuration_Implementcode extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
       // $layout  =  Mage::helper('sociallogin')->returnlayout();
        //$block = Mage::helper('sociallogin')->returnblock();
        //$text =  Mage::helper('sociallogin')->returntext();
       // $template = Mage::helper('sociallogin')->returntemplate();
        return '
<div class="entry-edit-head collapseable"><a onclick="Fieldset.toggleCollapse(\'sociallogin_template\'); return false;" href="#" id="sociallogin_template-head" class="open">Code Implementation</a></div>
<input id="sociallogin_template-state" type="hidden" value="1" name="config_state[sociallogin_template]">
<fieldset id="sociallogin_template" class="config collapseable" style="">
<h4 class="icon-head head-edit-form fieldset-legend">Code for Social Login</h4>
<div id="messages">
    <ul class="messages">
        <li class="success-msg">
            <ul>
                <li>'.Mage::helper('sociallogin')->__('You can put social login button block in any preferred position by using these following codes. Please note that social login buttons still work normally according to your settings in General Configuration tab if codes are not implemented.').'</li>				
            </ul>
        </li>
    </ul>
</div>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('sociallogin')->__('Add code below to a template file').'</li>				
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li>
		<code>
			&lt;?php echo $this->getLayout()->createBlock("sociallogin/buttons")->setTemplate("sociallogin/buttons.phtml")->setNumberButtonShow(4)->toHtml(); ?&gt;
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('sociallogin')->__('You can put a social login button block on a CMS page. Here is an example that we put a login block with 4 buttons. Replace "4" in this code with the number of buttons you want to show.').'</li>				
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li>
		<code>
			{{block type="sociallogin/buttons" name="buttons.sociallogin" template="sociallogin/buttons.phtml" number_button_show="4"}}
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('sociallogin')->__('Please copy and paste the code below to one of xml layout files where you want to show the social button block. Replace "4" in this code with the number of buttons you want to show.').'</li>				
            </ul>
        </li>
    </ul>
</div>

<ul>
	<li>
		<code>
		 &lt;block type="sociallogin/buttons" name="buttons.sociallogin" template="sociallogin/buttons.phtml"&gt;<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;action method="setNumberButtonShow"&gt;&lt;number&gt;4&lt;/number&gt;&lt;/action&gt;<br>
		&lt;/block&gt;
		</code>	
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>			
                <li>'.Mage::helper('sociallogin')->__('Below is a code example of a block with 4 social login buttons shown on the left of the category page. Replace "4" in this code with the number of buttons you want to show.').'</li>				
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li>
		<code>
&lt;?xml version="1.0"?&gt;<br>
&lt;layout version="0.1.0"&gt;<br>
&nbsp;&nbsp;&lt;catalog_category_default&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;reference name="left"&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;block type="catalog/navigation" name="catalog.leftnav" after="currency" template="catalog/navigation/left.phtml"/&gt;<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;block type="sociallogin/buttons" name="buttons.sociallogin" template="sociallogin/buttons.phtml"&gt;<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;action method="setNumberButtonShow"&gt;&lt;number&gt;4&lt;/number&gt;&lt;/action&gt;<br>
		   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/block&gt; <br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;/reference><br>
&nbsp;&nbsp;&lt;/catalog_category_default&gt;<br>
&lt;/layout&gt;
</code>	
	</li>
</ul>
<br>

</fieldset>'. $this->addPreview().
'<style>#black_overlay{
    display: none;
    position: fixed;
    top: 0%;
    left: 0%;
    width: 100%;
    height: 100%;
    background-color: black;
    z-index:1001;
    -moz-opacity: 0.8;
    opacity:.80;
    filter: alpha(opacity=80);
}
 
#white_content {
    display: none;
    position: fixed;
    top: 25%;
    left: 25%;
    width: 50%;
    height: 50%;
    padding: 16px;
    border: 16px solid orange;
    background-color: white;
    z-index:1002;
    overflow: auto;
}</style>
<img id="white_content" src="https://demo.magestore.com/social-login/dev/media/magestore/sociallogin/fb_appid.png"/>
<div id="black_overlay" onclick="return hidePopupImg();"></div>';
    }
    
    protected function addPreview(){
        $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/sociallogin';  
        $html = '<script>document.observe("dom:loaded", function() {';
        //facebook
        $html .= 'addNewImageElement("sociallogin_fblogin_app_id","'.$mediaUrl.'/fb_appid.png");';
        $html .= 'addNewImageElement("sociallogin_fblogin_app_secret","'.$mediaUrl.'/fb_appsecret.png");';
        //instagram
        $html .= 'addNewImageElement("sociallogin_instalogin_consumer_key","'.$mediaUrl.'/insta_clientid.png");';
        $html .= 'addNewImageElement("sociallogin_instalogin_consumer_secret","'.$mediaUrl.'/insta_clientsecret.png");';
        $html .= 'addNewImageElement("sociallogin_instagramlogin_redirecturl","'.$mediaUrl.'/insta_redirecturl.png");';
        //twitter
        $html .= 'addNewImageElement("sociallogin_twlogin_consumer_key","'.$mediaUrl.'/tw_apikey.png");';
        $html .= 'addNewImageElement("sociallogin_twlogin_consumer_secret","'.$mediaUrl.'/tw_apisecret.png");';
        $html .= 'addNewImageElement("sociallogin_twitterlogin_redirecturl","'.$mediaUrl.'/tw_callbackurl.png");';
        //google
        $html .= 'addNewImageElement("sociallogin_gologin_consumer_key","'.$mediaUrl.'/go_clientid.png");';
        $html .= 'addNewImageElement("sociallogin_login_redirecturl","'.$mediaUrl.'/go_redirect_uris.png");';
        $html .= 'addNewImageElement("sociallogin_gologin_consumer_secret","'.$mediaUrl.'/go_clientsecret.png");';
        //yahoo
        $html .= 'addNewImageElement("sociallogin_yalogin_app_id","'.$mediaUrl.'/yh_appid.png");';
        $html .= 'addNewImageElement("sociallogin_yahoologin_redirecturl","'.$mediaUrl.'/yh_appurl.png");';
        $html .= 'addNewImageElement("sociallogin_yalogin_consumer_key","'.$mediaUrl.'/yh_consumerkey.png");';
        $html .= 'addNewImageElement("sociallogin_yalogin_consumer_secret","'.$mediaUrl.'/yh_consumersecret.png");';
        //linked
        $html .= 'addNewImageElement("sociallogin_linklogin_app_id","'.$mediaUrl.'/linked_clientapi.png");';
        $html .= 'addNewImageElement("sociallogin_linklogin_secret_key","'.$mediaUrl.'/linked_clientsecret.png");';
        $html .= 'addNewImageElement("sociallogin_lklogin_redirecturl","'.$mediaUrl.'/linked_clientsecret.png");';
        //foursquare
        $html .= 'addNewImageElement("sociallogin_fqlogin_consumer_key","'.$mediaUrl.'/foursquare_clientid.png");';
         $html .= 'addNewImageElement("sociallogin_fqlogin_consumer_secret","'.$mediaUrl.'/foursquare_clientsecret.png");';
         $html .= 'addNewImageElement("sociallogin_fqlogin_redirecturl","'.$mediaUrl.'/foursquare_redirecturl.png");';
         //windows
        $html .= 'addNewImageElement("sociallogin_livelogin_consumer_key","'.$mediaUrl.'/windows_clientid.png");';
        $html .= 'addNewImageElement("sociallogin_livelogin_consumer_secret","'.$mediaUrl.'/windows_clientsecret.png");';
        $html .= 'addNewImageElement("sociallogin_livelogin_redirecturl","'.$mediaUrl.'/windows_url.png");';
        //myspace
        $html .= 'addNewImageElement("sociallogin_mplogin_consumer_key","'.$mediaUrl.'/fb_appid.png");';
        $html .= 'addNewImageElement("sociallogin_mplogin_consumer_secret","'.$mediaUrl.'/fb_appid.png");';
        //vk
        $html .= 'addNewImageElement("sociallogin_vklogin_app_id","'.$mediaUrl.'/vk_appid.png");';
        $html .= 'addNewImageElement("sociallogin_vklogin_secure_key","'.$mediaUrl.'/vk_secretkey.png");';
        $html .= 'addNewImageElement("sociallogin_vklogin_redirecturl","'.$mediaUrl.'/vk_url.png");';
        //amazon
         $html .= 'addNewImageElement("sociallogin_amazonlogin_consumer_key","'.$mediaUrl.'/amazon_clientid.png");';
        $html .= 'addNewImageElement("sociallogin_amazonlogin_consumer_secret","'.$mediaUrl.'/amazon_clientsecret.png");';
        $html .= 'addNewImageElement("sociallogin_amlogin_redirecturl","'.$mediaUrl.'/amazon_url.png");';
        
		$html .= 'addNewImageElement("yahoo_create_abcxyz","'.$mediaUrl.'/yahoodirection.png");';
        $html .= '});</script>';
        return $html;
    }
}
