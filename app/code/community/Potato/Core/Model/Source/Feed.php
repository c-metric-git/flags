<?php

class Potato_Core_Model_Source_Feed
{
    const RESOURCE_URL = 'http://potatocommerce.com/feed.json';
    const CACHE_ID     = 'POTATOCOMMERCE_EXTENSION_FEED';
    const CACHE_LIFETIME = 172800;
    const POTATOCOMMERCE_URL = 'http://potatocommerce.com';

    public function getFeed()
    {
        if (!$feed = Mage::app()->loadCache(self::CACHE_ID)) {
            $feed = $this->_getFeedFromResource();
            $this->_save($feed);
        }
        return Zend_Json::decode($feed);
    }

    protected function _getFeedFromResource()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, self::RESOURCE_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        $result = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            $result = '{}';
        }
        curl_close($ch);
        return $result;
    }

    protected function _save($feed)
    {
        Mage::app()->saveCache($feed, self::CACHE_ID, array(), self::CACHE_LIFETIME);
        return $this;
    }
}