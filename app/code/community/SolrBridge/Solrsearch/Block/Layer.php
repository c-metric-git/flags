<?php
/**
 * @category SolrBridge
 * @package Solrbridge_Search
 * @author	Hau Danh
 * @copyright	Copyright (c) 2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Layer extends SolrBridge_Solrsearch_Block_Faces
{
	protected function _construct()
	{
		$this->solrModel = Mage::getModel('solrsearch/solr');
	}
	
	public function canShowBlock()
	{
		return $this->isLayerNavigationActive();
	}
	
	public function canShowOptions()
	{
		return $this->isShoppingOptionsActive();
	}
	public function getFilters()
	{
		$facetFields = $this->getFacetFields();
		$filterQuery = $this->getFilterQuery();
		$filterKeys = array_keys($filterQuery);
		
		$facetGroup = array(
				'oem_model'=>array('label' => $this->__('oem & model'), 'items' => array(), 'selected'=> '' ),
				'family_product'=>array('label' => $this->__('Family & Product Line'), 'items' => array(), 'selected'=> '' ),
		);
		foreach ($facetFields as $key=>$facetData)
		{
			if ( count($facetData) > 0 )
			{
				if ( strpos($key, 'attr_oem_') !== false )
				{
					//$attrKey = substr($key, 0, strrpos($key, '_'));
					if ( in_array($key, $filterKeys) && !$facetGroup['oem_model']['selected'] )
					{
						$facetGroup['oem_model']['selected'] = 'select';
					}
					
					$facetGroup['oem_model']['items'][$key] = $facetData;
				}
				else if ( strpos($key, 'fam_line_') !== false )
				{
					//$attrKey = substr($key, 0, strrpos($key, '_'));
					if ( in_array($key, $filterKeys) && !$facetGroup['oem_model']['selected'] )
					{
						$facetGroup['family_product']['selected'] = 'select';
					}
					
					$facetGroup['family_product']['items'][$key] = $facetData;
				}
			}
			
		}
		
		return $facetGroup;
	}
	
	public function getAttrIcon( $key )
	{
		$iconImg = $this->getSkinUrl('images/oem_sections/arrow_category_left.png');
		if ( strpos($key, 'attr_oem_') !== false )
		{
			if ( $this->getIsActive($key) )
			{
				$iconImg = $this->getSkinUrl('images/oem_sections/arrow_category_currently.png');
			}
		}
		else if ( strpos($key, 'fam_line_') !== false )
		{
			$attrKey = substr($key, 0, strrpos($key, '_'));
			
			$iconImg = '/media/filters/' . $attrKey . '.png' ;
			
			if ( $this->getIsActive($key) )
			{
				$iconImg = '/media/filters/' . $attrKey . '_select.png' ;
			}
		}
		return $iconImg;
	}
	
	public function getIsActive($attrKey)
	{
		$filterQuery = $this->getFilterQuery();
		$filterKeys = array_keys($filterQuery);
		if ( in_array($attrKey, $filterKeys) )
		{
			return true;
		}
		return false;
	}
	
	public function getSubMenuCssDisplay($attrKey)
	{
		if ( !$this->getIsActive($attrKey) ) 
		{
			return 'display: none;';
		}
		return 'display: block;';
	}
	
	public function getIsElementActive($attrKey, $attrValue)
	{
		$filterQuery = $this->getFilterQuery();
		if ( isset($filterQuery[$attrKey]) && is_array($filterQuery[$attrKey]) && in_array($attrValue, $filterQuery[$attrKey]) )
		{
			return true;
		}
		return false;
	}
	
	public function getSelectedItemClass($attrKey, $attrValue)
	{
		if ( $this->getIsElementActive($attrKey, $attrValue) )
		{
			return 'm-selected-ln-item';
		}
		return '';
	}
	
	public function getItemRemoveUrl($attrKey, $attrValue)
	{
		$face_key = substr($attrKey, 0, strrpos($attrKey, '_'));
		return $this->getRemoveFacesUrl($face_key, $attrValue);
	}
}
?>