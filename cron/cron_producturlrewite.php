<?php 
require_once '../app/Mage.php';
Mage::app();
umask(0);
ini_set('memory_limit', '712M');
set_time_limit(0);
//ini_set("memory_limit", "1024M");
echo  "<br /><br />Product Url Rewrites For Flagsrus Site <br /><br />";   
CreateProductUrlRewrites(2);       
echo  "<br /><br />Product Url Rewrites For Facepaint Site <br /><br />";
CreateProductUrlRewrites(3);

function CreateProductUrlRewrites($storeId) {
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $writeConnection = $resource->getConnection('core_write');
    $query = 'SELECT * FROM ' . $resource->getTableName('core/url_rewrite') . ' WHERE id_path like "product%" and store_id = '.(int)$storeId;
    $results = $readConnection->fetchAll($query);
    $counter=0;
    foreach($results as $url_arr) {
	    $product_url_arr[] = $url_arr['request_path'];
    }
		            $_productCollection = Mage::getResourceModel('reports/product_collection')
				    ->setStoreId($storeId)
				    ->addStoreFilter($storeId)
				    ->addAttributeToFilter('status', 1)
				    ->addAttributeToSelect('url_key','entity_id');							
				foreach($_productCollection  as $product)
			    {                       
				        if(!in_array($product['url_key'],$product_url_arr)) {
						    $fromUrl =$product['url_key'];					
						    $toUrl="catalog/product/view/id/".$prod_id;
						    $IdPath="product/".$prod_id;
						    $rewrite = Mage::getModel('core/url_rewrite');	
						    $rewrite->setStoreId($storeId);
						    $rewrite->setIdPath($IdPath);
						    $rewrite->setRequestPath($fromUrl);
						    $rewrite->setTargetPath($toUrl);
						    $rewrite->setIsSystem(0);
                            $rewrite->setProductId($prod_id); 
						    $rewrite->setEntityType(Mage_Core_Model_Url_Rewrite::TYPE_CUSTOM);
						    //$rewrite->save();						
						    echo "Successfully updated Custom Url: ".$fromUrl."<br>";					
					    }
					    else {
						    echo "<br>product id = ".$prod_id = $product['entity_id']."   product url =".$product['url_key'];				
					    }
					    //if($counter==10) {
    //						exit;
    //					}
					    $counter++;
			    }						
}                
?>