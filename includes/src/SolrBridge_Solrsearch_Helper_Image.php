<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Helper_Image extends Mage_Catalog_Helper_Image
{
	public function getImagePath()
	{
		try {
            $model = $this->_getModel();

            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            } else {
                $model->setBaseFile($this->getProduct()->getData($model->getDestinationSubdir()));
            }

            if ($model->isCached()) {
                return $model->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $model->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $model->resize();
                }

                if ($this->getWatermark()) {
                    $model->setWatermark($this->getWatermark());
                }

                $url = $model->getNewFile();
            }
        } catch (Exception $e) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
	}
    /**
     *
     * @param string $type image/small_image/thumbnail
     * @return string
     */
	public function getImagePlaceHolder($type = 'image')
	{
	    $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
	    $file = '';
	    // check if placeholder defined in config
	    $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$type}_placeholder");
	    $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
	    if ($isConfigPlaceholder && file_exists($baseDir . $configPlaceholder)) {
	        $file = $configPlaceholder;
	    }
	    else {
	        // replace file with skin or default skin placeholder
	        $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
	        $skinPlaceholder = "/images/catalog/product/placeholder/{$type}.jpg";
	        $file = $skinPlaceholder;
	        if (file_exists($skinBaseDir . $file)) {
	            $baseDir = $skinBaseDir;
	        }
	        else {
	            $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
	            if (!file_exists($baseDir . $file)) {
	                $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base', '_area' => Mage_Core_Model_Design_Package::DEFAULT_AREA));
	            }
	        }
	    }
	    $baseFile = $baseDir . $file;
	    return $baseFile;
	}
}