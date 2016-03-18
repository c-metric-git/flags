<?php
 /**
 * Magespot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespot License.
 * It is available through the world-wide-web at this URL:
 * http://magespot.com/license.html
 * If you need receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contacts@magespot.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magespot.com/ for more information.
 *
 * @category   Magespot
 * @package    Magespot_Addresslinelength
 * @copyright  Copyright (c) 2013 Magespot Crew (http://www.magespot.com/)
 * @author     Magespot Crew <contacts@magespot.com>
 * @license    http://magespot.com/license.html  Magespot License
 */
class Magespot_Addresslinelength_Model_Observer
{
    public function onCoreBlockAbstractToHtmlAfter($observer) 
    {
        $storeId = Mage::app()->getStore()->getId();
        if ($maxLength = Mage::getStoreConfig('customer/address/line_length', $storeId)) {
            $lines = Mage::getStoreConfig('customer/address/street_lines', $storeId);
            $message = Mage::getStoreConfig('customer/address/val_message', $storeId);
            $block = $observer->getBlock();
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            
            $frontAddressEditClass = Mage::getConfig()->getBlockClassName('customer/address_edit');
            if ($frontAddressEditClass == get_class($block)) {
                if (Mage::getStoreConfig('customer/address/set_maxlength', $storeId)) {
                    $html = $this->_injection($html, $lines, 'street[]', 'class', 'maxlength="' . $maxLength . '" ');
                } elseif (Mage::getStoreConfig('customer/address/lines_amount', $storeId)) {
                    $html = $this->_amountInjection($html, $lines, 'street[]', 'input-text', 'validate-addresslinelength ', 'street_', 'validate_addresslinelength', 'validate-addresslinelength', $maxLength, $message);
                } else {
                    $html = $this->_injection($html, $lines, 'street[]', 'input-text', 'validate-addresslinelength ', $maxLength, $message);
                }
            }
            
            $frontCheckoutOnepageBilling = Mage::getConfig()->getBlockClassName('checkout/onepage_billing');
            if ($frontCheckoutOnepageBilling == get_class($block)) {
                if (Mage::getStoreConfig('customer/address/set_maxlength', $storeId)) {
                    $html = $this->_injection($html, $lines, 'billing[street]', 'class', 'maxlength="' . $maxLength . '" ');
                } elseif (Mage::getStoreConfig('customer/address/lines_amount', $storeId)) {
                    $html = $this->_amountInjection($html, $lines, 'billing[street]', 'input-text', 'validate-addresslinelengthbil ', 'billing:street', 'validate_addresslinelengthbil', 'validate-addresslinelengthbil', $maxLength, $message);
                } else {
                    $html = $this->_injection($html, $lines, 'billing[street]', 'input-text', 'validate-addresslinelength ', $maxLength, $message);
                }
            }
            
            $frontCheckoutOnepageShipping = Mage::getConfig()->getBlockClassName('checkout/onepage_shipping');
            if ($frontCheckoutOnepageShipping == get_class($block)) {
                if (Mage::getStoreConfig('customer/address/set_maxlength', $storeId)) {
                    $html = $this->_injection($html, $lines, 'shipping[street]', 'class', 'maxlength="' . $maxLength . '" ');
                } elseif (Mage::getStoreConfig('customer/address/lines_amount', $storeId)) {
                    $html = $this->_amountInjection($html, $lines, 'shipping[street]', 'input-text', 'validate-addresslinelengthship ', 'shipping:street', 'validate_addresslinelengthship', 'validate-addresslinelengthship', $maxLength, $message);
                } else {
                    $html = $this->_injection($html, $lines, 'shipping[street]', 'input-text', 'validate-addresslinelength ', $maxLength, $message);
                }
            }
            
            $transport->setHtml($html);
        }
    }
    
    private function _injection($html, $lines, $first, $second, $insert, $maxLength = 0, $message = '')
    {
        $pos = 0;
        for ($i = 1; $i <= $lines; $i++) {
            $pos = strpos($html, $first, $pos);
            $pos = strpos($html, $second, $pos);
            $html = substr_replace($html, $insert, $pos, 0);
        }
        if ($maxLength) {
            $js = '<script type=\'text/javascript\'>
                   Validation.addAllThese([
                       [\'validate-addresslinelength\', \'' . $message . '\', function(v) {
                           return v.length <= ' . $maxLength . ';
                       }],
                   ]);
                   </script>
                  ';
            $html .= $js;
        }
        return $html;
    }
    
    private function _amountInjection($html, $lines, $first, $second, $insert, $element, $method, $class, $maxLength, $message)
    {
        $pos = 0;
        $pos = strpos($html, $first, $pos);
        $pos = strpos($html, $second, $pos);
        $html = substr_replace($html, $insert, $pos, 0);
        $js = '
               <script type=\'text/javascript\'>
               function ' . $method . '(d) {
                   var amount_length = 0;
                   for (var i=1; i < ' . $lines . ' + 1; i++) {
                       var element = $(\'' . $element . '\' + i);
                       if (element) {
                           amount_length += element.value.length;
                       }
                   }
                   if (' . $maxLength . ' < amount_length) {
                       return false;
                   }
                   return true;
               }
               Validation.addAllThese([
                   [\'' . $class . '\', \''.$message.'\', function(v) {
                       return ' . $method . '(v);
                   }]
               ]);
               </script>
              ';
        $html .= $js;
        
        return $html;
    }
}