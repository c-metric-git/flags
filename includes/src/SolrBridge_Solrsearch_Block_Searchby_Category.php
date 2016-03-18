<?php
class SolrBridge_Solrsearch_Block_Searchby_Category extends Mage_Core_Block_Template
{
	public function getCategories()
	{
		return Mage::getModel('solrsearch/solr')->getCategoryFacets();
	}

	public function formatCategoryPath($path, $facetcount)
	{
		$pathArray = $this->pathToArray($path);
		$outPut = '';
		if (is_array($pathArray) && count($pathArray) > 0) {
			$index = 1;
			$count = count($pathArray);
		    foreach ($pathArray as $item){
		    	$name = isset($item['name'])?$item['name']:"";
		    	$id = isset($item['id'])?$item['id']:"";
		    	if (is_numeric($id) && !empty($name)) {
					$url = Mage::getModel('catalog/category')->load($id)->getUrl(array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()));
					if (!empty($url)){
						$outPut .= '<a class="category-item" href="'.$url.'">'.$this->facetFormat($name).'<a/>';
						if ($index < $count) {
						    $outPut .= '&nbsp;&nbsp;>&nbsp;&nbsp;';
						}
						$index++;
					}
		    	}

		    }
		}
		return $outPut.'&nbsp;('.$facetcount.')';
	}

	public function facetFormat($text) {
		$returnText = $text;
		if (strrpos($text, '_._._') > -1) {
			$returnText = str_replace('_._._', '/', $text);
		}
		return $returnText;
	}
	
	/**
	 * Convert string path to array
	 * @param string $path
	 * @return array
	 */
	public function pathToArray($path) {
		$chunks = explode('/', $path);
		$result = array();
		for ($i = 0; $i < sizeof($chunks) - 1; $i+=2)
		{
			//$result[] = array('id' => $chunks[($i+1)], 'name' => $chunks[$i]);
			//CatId format x:y, x is category id, y is position
			$catIdArr = explode(':', $chunks[($i+1)]);
			$result[] = array('id' => $catIdArr[0], 'position' => $catIdArr[1], 'name' => $chunks[$i]);
		}
	
		return $result;
	}

	/**
	 * Convert string path to array
	 * @param string $path
	 * @return array
	 */
	public function pathToArrayOld($path) {
		$chunks = explode('/', $path);
		$result = array();
		for ($i = 0; $i < sizeof($chunks) - 1; $i+=2)
		{
			$result[] = array('id' => $chunks[($i+1)], 'name' => $chunks[$i]);
		}

		return $result;
	}
}