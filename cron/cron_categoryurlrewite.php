<?php 
require_once '../app/Mage.php';
Mage::app();
set_time_limit(0);
ini_set("memory_limit", "256M");

//CreateUrlRewrites(1);
echo  "<br /><br />Category Url Rewrites For Flagsrus Site <br /><br />";
CreateUrlRewrites(2);       
echo  "<br /><br />Category Url Rewrites For Facepaint Site <br /><br />";
CreateUrlRewrites(3);

function CreateUrlRewrites($storeId) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');
        $counter=0;
        $query = 'SELECT * FROM ' . $resource->getTableName('core/url_rewrite') . ' WHERE id_path like "category%" and store_id = '.(int)$storeId;
        $results = $readConnection->fetchAll($query);
        foreach($results as $url_arr) {
	        $cat_url_arr[] = $url_arr['request_path'];
        }
			        
			        $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
                    $categories = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->setStoreId($storeId)
                        ->addFieldToFilter('is_active', 1)
                        ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
                        ->addAttributeToSelect('*');
			        $cats= array();
			        $requestPath_arr = array();
			        foreach($categories as $categorie)
                    {
					        $cat_id = $categorie['entity_id'];
					        if(!in_array($categorie['umm_cat_target'],$cat_url_arr)) {
						        $fromUrl =$categorie['umm_cat_target'];					
						        $toUrl="catalog/category/view/id/".$cat_id;
						        $IdPath="category/".$cat_id;
						        $rewrite = Mage::getModel('core/url_rewrite');	
						        $rewrite->setStoreId($storeId);
						        $rewrite->setIdPath($IdPath);
						        $rewrite->setRequestPath($fromUrl);
						        $rewrite->setTargetPath($toUrl);
						        $rewrite->setIsSystem(0);
						        $rewrite->setEntityType(Mage_Core_Model_Url_Rewrite::TYPE_CUSTOM);
                                $rewrite->setCategoryId($cat_id);  
						        $rewrite->save();						
						        echo "Successfully updated Custom Url: ".$fromUrl."<br>";				
                                $counter++;	                                                
					        }
			        }
                    echo "<br /><br />Category Urls Added : ".$counter;					
}					
                
?>