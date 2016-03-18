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
 *  Sharing system config info model
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */
class Loginradius_Sharing_Model_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        // Get LoginRadius Module Thanks message container..
        $this->render_module_thanks_message_container();

        // Get LoginRadius Module information container..
        $this->render_module_info_container();

        // Get LoginRadius Module Support Us container..
        $this->render_module_admin_script_container();
    }

    /**
     * Get LoginRadius Module Thanks message container..
     */
    public function render_module_thanks_message_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_thank_message_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Thank you for installing Social9.com social sharing Extension!') ?></strong></h4>

            <p>
                Social9 provides Social Sharing solutions to your Magento website. We also offer Social Plugins for <a href="http://ish.re/RPLD" target="_blank">Wordpress</a>, <a href="http://ish.re/RPME" target="_blank">Drupal</a>, <a href="http://ish.re/RPMR" target="_blank">Joomla</a>, <a href="http://ish.re/RPN0" target="_blank">Zen-Cart</a>, <a href="http://ish.re/RPMW" target="_blank">Expression Engine</a> and <a href="http://ish.re/RPMS" target="_blank">Typo3</a>! Please visit <a href="http://www.social9.com" target="blank">www.social9.com</a> for more info.
            </p>
        </fieldset>
    <?php
    }

    /**
     * Get LoginRadius Module information container..
     */
    public function render_module_info_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_info_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Extension Information!') ?></strong></h4>

            <div style="margin:5px 0">
                <strong>Version: </strong>2.5.0 <br/>
                <strong>Author:</strong> Social9 Team<br/>
                <strong>Website:</strong> <a href="https://www.social9.com" target="_blank">www.social9.com</a>
                <br/>
                <div id="sociallogin_get_update" style="float:left;">To receive updates on new features, releases, etc. </div>
            </div>
        </fieldset>
    <?php
    }

    /**
     * Render script for extension admin configuration options
     */
    public function render_module_admin_script_container()
    {
        ?>
        <script type="text/javascript">var islrsharing = true;
            var islrsocialcounter = true;</script>
        <script type="text/javascript" src="//sharecdn.social9.com/v1/social9.min.js" id="lrsharescript"></script>
        <script type="text/javascript">
            window.onload = function () {
                var sharingType = ['horizontal', 'vertical'];
                var sharingModes = ['Sharing', 'Counter'];
                for (var i = 0; i < sharingType.length; i++) {
                    for (var j = 0; j < sharingModes.length; j++) {
                        if (sharingModes[j] == 'Counter') {
                            var providers = $SC.Providers.All;
                        } else {
                            var providers = $SS.Providers.More;
                        }
                        // populate sharing providers checkbox
                        loginRadiusCounterHtml = "<ul class='checkboxes'>";
                        // prepare HTML to be shown as Vertical Counter Providers
                        for (var ii = 0; ii < providers.length; ii++) {
                            loginRadiusCounterHtml += '<li><input type="checkbox" id="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '" ';
                            loginRadiusCounterHtml += 'value="' + providers[ii] + '"> <label for="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '">' + providers[ii] + '</label></li>';
                        }
                        loginRadiusCounterHtml += "</ul>";
                        var tds = document.getElementById('row_sharing_options_' + sharingType[i] + 'Sharing_' + sharingType[i] + sharingModes[j] + 'Providers').getElementsByTagName('td');
                        tds[1].innerHTML = loginRadiusCounterHtml;
                    }
                    document.getElementById('row_sharing_options_' + sharingType[i] + 'Sharing_' + sharingType[i] + 'CounterProvidersHidden').style.display = 'none';
                }
                loginRadiusSharingPrepareAdminUI();
            }
            // toggle sharing/counter providers according to the theme and sharing type
            function loginRadiusToggleSharingProviders(element, sharingType) {
                var sharingContainer = document.getElementById('row_sharing_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProviders');
                var countercontainer = document.getElementById('row_sharing_options_' + sharingType + 'Sharing_' + sharingType + 'CounterProviders');
                var sharingContainerHidden = document.getElementById('row_sharing_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden');
                if (element.value == '32' || element.value == '16' || element.value == 'responsive') {
                    sharingContainer.style.display = 'table-row';
                    countercontainer.style.display = 'none';
                    sharingContainerHidden.style.display = 'table-row';
                } else if (element.value == 'single_large' || element.value == 'single_small') {
                    sharingContainer.style.display = 'none';
                    countercontainer.style.display = 'none';
                    sharingContainerHidden.style.display = 'none';
                } else {
                    sharingContainer.style.display = 'none';
                    countercontainer.style.display = 'table-row';
                    sharingContainerHidden.style.display = 'none';
                }
            }
        </script>
    <?php
    }
}