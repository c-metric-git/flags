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
 * @package    Magespot_Base
 * @copyright  Copyright (c) 2013 Magespot Crew (http://www.magespot.com/)
 * @author     Magespot Crew <contacts@magespot.com>
 * @license    http://magespot.com/license.html  Magespot License
 */
class Magespot_Base_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isVersionLessThan($major=1, $minor=4)
    {
        $curr = explode('.', Mage::getVersion()); // 1.3. compatibility
        $need = func_get_args();
        foreach ($need as $k => $v){
            if ($curr[$k] != $v)
                return ($curr[$k] < $v);
        }
        return false;
    } 
    
    public function isModuleActive($code)
    {
        return ('true' == (string)Mage::getConfig()->getNode('modules/'.$code.'/active'));
    } 
    
}