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
class SolrBridge_Solrsearch_Model_Solr extends SolrBridge_Solrsearch_Model_Solr_Query
{

    protected $_storeId = null;
    protected $_solrCore = null;
	protected $_solrData = null;

    public function query($queryText, $params = array())
    {
        if ($returnData = Mage::registry('solrbridge_solrsearch_solr_query_data'))
        {
            return $returnData;
        }

        //$solrcore = Mage::helper('solrsearch')->getSetting('solr_index');
        $solrcore = $this->getSolrCore();

        $options = array('solrcore' => $solrcore, 'queryText' => $queryText, 'rows' => 20, 'facetlimit' => 200);

        if (!empty($params)) {
            $options = array_merge($options, $params);
        }

        $resultSet = $this->init($options)->prepareQueryData()->execute();

        $this->_solrData = $resultSet;

        if (isset($this->_solrData['response']['numFound']) && (int) $this->_solrData['response']['numFound'] > 0)
        {
            Mage::register('solrbridge_solrsearch_solr_query_data', $this->_solrData);
        }

        return $resultSet;
    }
    
    protected function getSolrCore() {
    
        if($this->_solrCore === null)
        {
            $this->_solrCore = Mage::helper('solrsearch')->getSolrCore();
        }
        return $this->_solrCore;
    }

    public function advQuery($queryText = '', $params = array())
    {
        $params = Mage::app()->getRequest()->getParams();

        $queryString = '';

        foreach ( $params as $field => $value )
        {
            if ( !empty($value) && !is_array($value))
            {
                $queryString .= '(('.$field.'_text:'.urlencode($value).')+OR+('.$field.'_varchar:'.urlencode($value).'))+AND+';
            }
            else if( is_array($value) )
            {
                $orQuery = '';
                foreach ($value as $subvalue)
                {
                    if ( !empty($subvalue) )
                    {
                        $orQuery .= '('.$field.'_text:'.urlencode($subvalue).')+OR+'.'('.$field.'_varchar:'.urlencode($subvalue).')+OR+';
                    }
                }
                if ( !empty($orQuery) )
                {
                    $queryString .= '('.trim($orQuery, '+OR+').')+AND';
                }

            }
        }

        $queryString = trim($queryString, '+AND');

        //$solrcore = Mage::helper('solrsearch')->getSetting('solr_index');
        $solrcore = $this->getSolrCore();

        $options = array('solrcore' => $solrcore, 'queryText' => $queryString, 'rows' => 20, 'facetlimit' => 200);

        if (!empty($params)) {
            $options = array_merge($options, $params);
        }

        $query = Mage::getModel('solrsearch/solr_advanced');

        $query->init($options)->prepareQueryData();
        $query->queryString = $queryString;
        $resultSet = $query->execute();

        return $resultSet;
    }

    public function getSolrData()
    {
        return $this->_solrData;
    }

    public function getCategoryFacets()
    {
    	$facetfield = 'category_path';
    	$query = '*:*';

    	$queryText = Mage::helper('solrsearch')->getParam('q');
    	if (!empty($queryText)) {
    		$query = 'category_text:('.$queryText.')';
    	}

    	//$solrcore = Mage::helper('solrsearch')->getSetting('solr_index');
    	$solrcore = $this->getSolrCore();

    	$queryUrl = Mage::helper('solrsearch')->getSetting('solr_server_url');

    	$arguments = array(
    			'json.nl' => 'map',
    			'wt'=> 'json',
    	);
    	$queryUrl = trim($queryUrl,'/').'/'.$solrcore;
    	$url = trim($queryUrl,'/').'/select/?q='.$query.'&rows=-1&facet=true&facet.field='.$facetfield.'&facet.mincount=1&facet.limit=5000';

    	//filter query
    	$this->prepareFilterQuery();
    	$filterQueryString = $this->filterQuery;
    	if (!empty($filterQueryString)) {
    	    $url .= '&fq='.$filterQueryString;
    	}

    	$resultSet = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $arguments, 'array');

    	$returnData = array();
    	if(isset($resultSet['facet_counts']['facet_fields'][$facetfield]) && is_array($resultSet['facet_counts']['facet_fields'][$facetfield]))
    	{
    		$returnData = $resultSet['facet_counts']['facet_fields'][$facetfield];
    	}

    	return $returnData;
    }

    public function getBrandsFacets()
    {
    	//display brand suggestion
    	$display_brand_suggestion = Mage::helper('solrsearch')->getSetting('display_brand_suggestion');
    	//display brand suggestion attribute code
    	$brand_attribute_code = Mage::helper('solrsearch')->getSetting('brand_attribute_code');
    	$brand_attribute_code = trim($brand_attribute_code);
    	if ($display_brand_suggestion > 0 && !empty($brand_attribute_code)) {
    		$facetfield = $brand_attribute_code.'_facet';
    	}


    	$query = '*:*';

    	$queryText = Mage::helper('solrsearch')->getParam('q');
    	if (!empty($queryText)) {
    		$query = $brand_attribute_code.'_text:('.$queryText.')';
    	}

    	//$solrcore = Mage::helper('solrsearch')->getSetting('solr_index');
    	$solrcore = $this->getSolrCore();

    	$queryUrl = Mage::helper('solrsearch')->getSetting('solr_server_url');

    	$arguments = array(
    			'json.nl' => 'map',
    			'wt'=> 'json',
    	);
    	$queryUrl = trim($queryUrl,'/').'/'.$solrcore;
    	$url = trim($queryUrl,'/').'/select/?q='.$query.'&rows=-1&facet=true&facet.field='.$facetfield.'&facet.mincount=1&facet.limit=5000';

    	//filter query
    	$this->prepareFilterQuery();
    	$filterQueryString = $this->filterQuery;
    	if (!empty($filterQueryString)) {
    	    $url .= '&fq='.$filterQueryString;
    	}

    	$resultSet = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $arguments, 'array');

    	$returnData = array();
    	if(isset($resultSet['facet_counts']['facet_fields'][$facetfield]) && is_array($resultSet['facet_counts']['facet_fields'][$facetfield]))
    	{
    		$returnData = $resultSet['facet_counts']['facet_fields'][$facetfield];
    	}

    	return $returnData;
    }
}