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
class Magespot_Base_Model_Source_Updates_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const TYPE_PROMO            = 'PROMO';
	const TYPE_NEW_RELEASE      = 'NEW_RELEASE';
	const TYPE_UPDATE_RELEASE   = 'UPDATE_RELEASE';
	const TYPE_INFO             = 'INFO';
	const TYPE_INSTALLED_UPDATE = 'INSTALLED_UPDATE';
	
	
	public function toOptionArray()
	{
	    $hlp = Mage::helper('msbase');
		return array(
			array('value' => self::TYPE_INSTALLED_UPDATE, 'label' => $hlp->__('New version of my extensions')),
			array('value' => self::TYPE_UPDATE_RELEASE,   'label' => $hlp->__('Updates of all extensions')),
			array('value' => self::TYPE_NEW_RELEASE,      'label' => $hlp->__('Releases of new extensions')),
			array('value' => self::TYPE_PROMO,            'label' => $hlp->__('Discounts and special offers')),
			array('value' => self::TYPE_INFO,             'label' => $hlp->__('Other customer information'))
		);
	}
	
	/**
     * Retrive all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
    	return $this->toOptionArray();
	}
	
	
	/**
	 * Returns label for value
	 * @param string $value
	 * @return string
	 */
	public function getLabel($value)
	{
		$options = $this->toOptionArray();
		foreach($options as $v){
			if($v['value'] == $value){
				return $v['label'];
			}
		}
		return '';
	}
	
	/**
	 * Returns array ready for use by grid
	 * @return array 
	 */
	public function getGridOptions()
	{
		$items = $this->getAllOptions();
		$out = array();
		foreach($items as $item){
			$out[$item['value']] = $item['label'];
		}
		return $out;
	}
}
