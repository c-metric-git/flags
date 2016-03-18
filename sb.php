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
define('SBDS', DIRECTORY_SEPARATOR);
define('SOLRBRIDGE_ROOT', getcwd());

#ini_set('display_errors', 1);

$baseDir = '/'.trim(SOLRBRIDGE_ROOT, '/');
require_once $baseDir.'/lib/SolrBridge/Base.php';
require_once $baseDir.'/lib/SolrBridge/Solr.php';  
SolrBridge_Base::loadZend();
  
$config = SolrBridge_Base::prepareConfigData($baseDir);
     
$solr = new SolrBridge_Solr($config);
$result = $solr->execute();

$result['keywordssuggestions'] = array();
$result['keywordsraw'] = array();

$display_keyword_suggestion = (int)$solr->getConfigValue('display_keyword_suggestion');  
if (!empty($display_keyword_suggestion) && $display_keyword_suggestion > 0) {
	require_once $baseDir.'/lib/SolrBridge/Autocomplete.php';

	$autocomplete = new SolrBridge_Autocomplete($config);  
	$resultAutocomplete = $autocomplete->execute();

	if (isset($resultAutocomplete['facet_counts']['facet_fields']['textSearchStandard']) && is_array($resultAutocomplete['facet_counts']['facet_fields']['textSearchStandard'])) {

	    $allow_ignore_term = (int)$solr->getConfigValue('allow_ignore_term');

	    if ($allow_ignore_term > 0)
	    {
	        $ignoreSearchTerms = trim($solr->getConfigValue('ignoresearchterms'));
	        if (!empty($ignoreSearchTerms)) {
	            $ignoreSearchTermsArray = explode(',', trim($ignoreSearchTerms));

	            foreach ($resultAutocomplete['facet_counts']['facet_fields']['textSearchStandard'] as $term => $val)
	            {
	                if (!in_array(strtolower($term), $ignoreSearchTermsArray))
	                {
	                    $result['keywordssuggestions'][] = $solr->hightlight($result['responseHeader']['params']['q'], trim($term, ','));
	                    $result['keywordsraw'][] = trim($term, ',');
	                }
	            }

	        }else{
	            foreach ($resultAutocomplete['facet_counts']['facet_fields']['textSearchStandard'] as $term => $val)
	            {
	                $result['keywordssuggestions'][] = $solr->hightlight($result['responseHeader']['params']['q'], trim($term, ','));
	                $result['keywordsraw'][] = trim($term, ',');
	            }
	        }
	    }
	    else
	    {
	        foreach ($resultAutocomplete['facet_counts']['facet_fields']['textSearchStandard'] as $term => $val)
	        {
	            $result['keywordssuggestions'][] = $solr->hightlight($result['responseHeader']['params']['q'], trim($term, ','));
	            $result['keywordsraw'][] = trim($term, ',');
	        }
	    }
	}
}

$priceFields = $solr->getPriceFields();

$priceFieldName = $priceFields[0];
$specialPriceFieldName = $priceFields[1];
$specialPriceFromDateFieldName = $priceFields[2];
$specialPriceToDateFieldName = $priceFields[3];


if (isset($result['response']['numFound']) && intval($result['response']['numFound']) > 0){
	foreach ($result['response']['docs'] as $k=>$document) {
		$price = '&nbsp;';
		$specialPrice = 0;
		if (isset($document[$priceFieldName])) {
			$price = $document[$priceFieldName];
		}

		$result['response']['docs'][$k]['price_decimal'] = (is_numeric($price))?number_format($price,2):$price;

		if ( isset($document[$specialPriceFieldName]) && isset($document[$specialPriceToDateFieldName]) && intval($document[$specialPriceToDateFieldName]) > 0 && intval($document[$specialPriceFieldName]) > 0 )
		{
			$storeTimeStamp = $solr->getParam('storetimestamp');

			if (is_numeric($storeTimeStamp) && $storeTimeStamp > 0)
			{
			    if(intval($document[$specialPriceToDateFieldName]) >= $storeTimeStamp){
			    	$specialPrice = $document[$specialPriceFieldName];
			    }
			}
		}else
		{
			if (isset($document[$specialPriceFieldName]) && intval($document[$specialPriceFieldName]) > 0)
			{
				$specialPrice = $document[$specialPriceFieldName];
			}
		}



		$result['response']['docs'][$k]['special_price_decimal'] = (is_numeric($specialPrice) && $specialPrice > 0)?number_format($specialPrice,2):0;
		$result['response']['docs'][$k]['name_varchar'] = $solr->hightlight($result['responseHeader']['params']['q'], $result['response']['docs'][$k]['name_varchar']);
		$result['response']['docs'][$k]['time'] = time();
	}
}

if (isset($result['facet_counts']['facet_fields']['category_facet'])) {
	if (!isset($result['facet_counts']['facet_fields']['category_path'])) {
		$result['facet_counts']['facet_fields']['category_path'] = $result['facet_counts']['facet_fields']['category_facet'];
	}
}


if (isset($result['responseHeader']['params']['q'])) {
    $categoryFacets = $solr->getCategoryFacets('category_path', $result['responseHeader']['params']['q']);
    if (is_array($categoryFacets) && isset($result['facet_counts']['facet_fields']['category_path']) && is_array($result['facet_counts']['facet_fields']['category_path'])) {
        $categoryFacets = array_merge($categoryFacets, $result['facet_counts']['facet_fields']['category_path']);

        $categoryFacets = array_slice($categoryFacets,0,$solr->getFacetLimit());

        $result['facet_counts']['facet_fields']['category_path'] = $categoryFacets;
    }
}
$solr->cleanUpCategoryFacet($result);

header('Content-Type: application/javascript');

$js_callback = $solr->getParam('json_wrf');

$timestamp = $solr->getParam('timestamp');

if (isset($timestamp)) {
	$result['responseHeader']['params']['timestamp'] = $timestamp;
}

if( isset($js_callback) && !empty($js_callback) )
{
    echo $js_callback.'('.json_encode($result).')';
}else{
    echo json_encode($result);
}
exit;