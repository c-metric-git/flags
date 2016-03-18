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
 *  Sharing socialsharing block
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Block_Socialsharing which is responsible for generating social sharing script according to configuration!
 */
class Loginradius_Sharing_Block_Sharing extends Mage_Core_Block_Template
{
    /**
     * Calling constructor for parent class
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Get title for social sharing container!
     *
     * @return string
     */
    public function sharingTitle()
    {
        return Mage::getStoreConfig('sharing_options/horizontalSharing/sharingTitle');
    }

    /**
     * @return mixed 1/0 according to option enabled or disabled
     */
    public function horizontalShareEnable()
    {
        return Mage::getStoreConfig('sharing_options/horizontalSharing/horizontalShareEnable');
    }

    /**
     * @return mixed 1/0 according to option enabled or disabled
     */
    public function verticalShareEnable()
    {
        return Mage::getStoreConfig('sharing_options/verticalSharing/verticalShareEnable');
    }

    /**
     * Check if horizontal sharing is enabled on products page or not
     *
     * @return mixed 1/0
     */
    public function horizontalShareProduct()
    {
        return Mage::getStoreConfig('sharing_options/horizontalSharing/horizontalShareProduct');
    }

    /**
     * Check if vertical sharing is enabled on products page or not
     *
     * @return mixed 1/0
     */
    public function verticalShareProduct()
    {
        return Mage::getStoreConfig('sharing_options/verticalSharing/verticalShareProduct');
    }

    /**
     * Check if horizontal sharing is enabled on checkout success page or not
     *
     * @return mixed 1/0
     */
    public function horizontalShareSuccess()
    {
        return Mage::getStoreConfig('sharing_options/horizontalSharing/horizontalShareSuccess');
    }

    /**
     * Check if vertical sharing is enabled on checkout success page or not
     *
     * @return mixed 1/0
     */
    public function verticalShareSuccess()
    {
        return Mage::getStoreConfig('sharing_options/verticalSharing/verticalShareSuccess');
    }

    /**
     * function returns script required for vertical sharing.
     */
    public function getVerticalSharingScript($loginRadiusSettings)
    {
        $size = '';
        $sharingScript = '';
        $type = '';
        $verticalThemvalue = isset($loginRadiusSettings['verticalSharingTheme']) ? $loginRadiusSettings['verticalSharingTheme'] : '';

        switch ($verticalThemvalue) {
            case '32':
                $size = '32';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
                break;

            case '16':
                $size = '16';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
                break;

            case 'counter_vertical':
                $sharingVariable = 'S';
                $ishorizontal = 'false';
                $interface = 'simple';
                $type = 'vertical';
                break;

            case 'counter_horizontal':
                $sharingVariable = 'S';
                $ishorizontal = 'false';
                $interface = 'simple';
                $type = 'horizontal';
                break;

            default:
                $size = '32';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
        }

        $verticalPosition = isset($loginRadiusSettings['verticalAlignment']) ? $loginRadiusSettings['verticalAlignment'] : '';
        switch ($verticalPosition) {
            case "top_left":
                $position1 = 'top';
                $position2 = 'left';
                break;

            case "top_right":
                $position1 = 'top';
                $position2 = 'right';
                break;

            case "bottom_left":
                $position1 = 'bottom';
                $position2 = 'left';
                break;

            case "bottom_right":
                $position1 = 'bottom';
                $position2 = 'right';
                break;

            default:
                $position1 = 'top';
                $position2 = 'left';
        }

        $offset = '$' . $sharingVariable . '.' . $position1 . ' = \'0px\'; $' . $sharingVariable . '.' . $position2 . ' = \'0px\';';

        if (isset($size) && empty($size)) {
            $providers = $this->getCounterProviders('vertical', $loginRadiusSettings);
            $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $type . '\'; ' . $offset . ' $u = LoginRadius.user_settings; if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $S.show( "loginRadiusVerticalSharing" ); } );';
        } else {
            $providers = self:: getSharingProviders('vertical', $loginRadiusSettings);
            // prepare sharing script
            $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
            $sharingScript .= '$i.size = ' . $size . '; ' . $offset . ' if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $i.show( "loginRadiusVerticalSharing" ); } );';
        }

        return $sharingScript;
    }

    /**
     * function returns comma separated counters lists
     */
    public function getCounterProviders($themeType, $loginRadiusSettings)
    {
        $searchOption = $themeType . 'CounterProvidersHidden';
        if (!empty($loginRadiusSettings[$searchOption])) {
            return str_replace(',', '","', $loginRadiusSettings[$searchOption]);
        } else {
            return 'Facebook Like","Google+ +1","Pinterest Pin it","LinkedIn Share","Hybridshare';
        }
    }

    /**
     * function returns comma seperated sharing providers lists
     *
     * global $loginRadiusSettings;
     */
    public static function getSharingProviders($themeType, $loginRadiusSettings)
    {
        $searchOption = $themeType . 'SharingProvidersHidden';
        if (!empty($loginRadiusSettings[$searchOption])) {
            return str_replace(',', '","', $loginRadiusSettings[$searchOption]);
        } else {
            return 'Facebook","Twitter","Pinterest","Print","Email';
        }
    }

    /**
     * Override  _prepareLayout method
     *
     * @return Mage_Core_Block_Abstract|void
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Get all horizontal sharing configuration options
     *
     * @return mixed
     */
    public function getHorizontalSharingSettings()
    {
        return Mage::getStoreConfig('sharing_options/horizontalSharing');
    }

    /**
     * Get all vertical sharing configuration options
     *
     * @return mixed
     */
    public function getVerticalSharingSettings()
    {
        return Mage::getStoreConfig('sharing_options/verticalSharing');
    }

    /**
     * function returns script required for horizontal sharing.
     */
    public function getHorizontalSharingScript($loginRadiusSettings)
    {
        $size = '';
        $sharingScript = '';
        $countertype = '';
        $horizontalThemvalue = isset($loginRadiusSettings['horizontalSharingTheme']) ? $loginRadiusSettings['horizontalSharingTheme'] : '';

        switch ($horizontalThemvalue) {
            case '32':
                $size = '32';
                $interface = 'horizontal';
                break;

            case '16':
                $size = '16';
                $interface = 'horizontal';
                break;

            case 'single_large':
                $size = '32';
                $interface = 'simpleimage';
                break;

            case 'single_small':
                $size = '16';
                $interface = 'simpleimage';
                break;

            case 'responsive':
                $size = '32';
                $interface = 'responsive';
                break;

            case 'counter_vertical':
                $ishorizontal = 'true';
                $interface = 'simple';
                $countertype = 'vertical';
                break;

            case 'counter_horizontal':
                $ishorizontal = 'true';
                $interface = 'simple';
                $countertype = 'horizontal';
                break;

            default:
                $size = '32';
                $interface = 'horizontal';
        }
        if (isset($ishorizontal) && !empty($ishorizontal)) {
            $providers = $this->getCounterProviders('horizontal', $loginRadiusSettings);

            // prepare counter script
            $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $countertype . '\'; $u = LoginRadius.user_settings;  if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $S.show( "loginRadiusHorizontalSharing" ); } );';
        } else {
            $providers = $this->getSharingProviders('horizontal', $loginRadiusSettings);

            // prepare sharing script
            $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
            $sharingScript .= '$i.size = ' . $size . '; $u.sharecounttype="url";  if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $i.show( "loginRadiusHorizontalSharing" ); } );';
        }

        return $sharingScript;
    }


}