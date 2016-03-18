<?php
static $config = NULL;
static $log = NULL;

// Error Handler
function error_handler_for_export($errno, $errstr, $errfile, $errline) {
	global $config;
	global $log;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}
		
	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}


function fatal_error_shutdown_handler_for_export()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_export(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}


class ModelCatalogProductImportExport extends Model {
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");
		
		$product_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
					
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {				
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}
	
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
				
					$product_option_id = $this->db->getLastId();
				
					if (isset($product_option['product_option_value'])) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . $this->db->escape($product_option_value['option_value_id']) . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						} 
					}
				} else { 
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}
		
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$value['customer_group_id'] . "', quantity = '" . (int)$value['quantity'] . "', priority = '" . (int)$value['priority'] . "', price = '" . (float)$value['price'] . "', date_start = '" . $this->db->escape($value['date_start']) . "', date_end = '" . $this->db->escape($value['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$value['customer_group_id'] . "', priority = '" . (int)$value['priority'] . "', price = '" . (float)$value['price'] . "', date_start = '" . $this->db->escape($value['date_start']) . "', date_end = '" . $this->db->escape($value['date_end']) . "'");
			}
		}
		
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image) . "'");
			}
		}
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
		
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
		
		foreach ($data['product_tag'] as $language_id => $value) {
			if ($value) {
				$tags = explode(',', $value);
				
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}
						
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
						
		$this->cache->delete('product');
	}
	
	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
					
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {				
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
				
					$product_option_id = $this->db->getLastId();
				
					if (isset($product_option['product_option_value'])) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . $this->db->escape($product_option_value['option_value_id']) . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else { 
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
				}					
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
 
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$value['customer_group_id'] . "', quantity = '" . (int)$value['quantity'] . "', priority = '" . (int)$value['priority'] . "', price = '" . (float)$value['price'] . "', date_start = '" . $this->db->escape($value['date_start']) . "', date_end = '" . $this->db->escape($value['date_end']) . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$value['customer_group_id'] . "', priority = '" . (int)$value['priority'] . "', price = '" . (float)$value['price'] . "', date_start = '" . $this->db->escape($value['date_start']) . "', date_end = '" . $this->db->escape($value['date_end']) . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image) . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}		
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				if ($layout['layout_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id. "'");
		
		foreach ($data['product_tag'] as $language_id => $value) {
			if ($value) {
				$tags = explode(',', $value);
			
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}
						
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
						
		$this->cache->delete('product');
	}
	
	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if ($query->num_rows) {
			$data = array();
			
			$data = $query->row;
			
			$data['keyword'] = '';

			$data['status'] = '0';
						
			$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
			$data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));			
			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_image' => $this->getProductImages($product_id)));
			
			$data['product_image'] = array();
			
			$results = $this->getProductImages($product_id);
			
			foreach ($results as $result) {
				$data['product_image'][] = $result['image'];
			}
						
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_tag' => $this->getProductTags($product_id)));
			$data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
			$data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
			$data = array_merge($data, array('product_store' => $this->getProductStores($product_id)));
			
			$this->addProduct($data);
		}
	}
	
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id. "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		$this->cache->delete('product');
	}
	
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
		return $query->row;
	}
	
	public function getProducts($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'"; 
		
			if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
				$sql .= " AND LCASE(pd.name) LIKE '" . $this->db->escape(mb_strtolower($data['filter_name'], 'UTF-8')) . "%'";
			}

			if (isset($data['filter_model']) && !is_null($data['filter_model'])) {
				$sql .= " AND LCASE(p.model) LIKE '" . $this->db->escape(mb_strtolower($data['filter_model'], 'UTF-8')) . "%'";
			}
			
			if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}
			
			if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
			}
			
			if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}

			$sort_data = array(
				'pd.name',
				'p.model',
				'p.price',
				'p.quantity',
				'p.status',
				'p.sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY pd.name";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
			
			$query = $this->db->query($sql);
		
			return $query->rows;
		} else {
			$product_data = $this->cache->get('product.' . $this->config->get('config_language_id'));
		
			if (!$product_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name ASC");
	
				$product_data = $query->rows;
			
				$this->cache->set('product.' . $this->config->get('config_language_id'), $product_data);
			}	
	
			return $product_data;
		}
	}
	
	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");
								  
		return $query->rows;
	} 
	
	public function getProductDescriptions($product_id) {
		$product_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}
		
		return $product_description_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();
		
		$product_attribute_query = $this->db->query("SELECT pa.attribute_id, ad.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY pa.attribute_id");
		
		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();
			
			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
			
			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}
			
			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'name'                          => $product_attribute['name'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}
		
		return $product_attribute_data;
	}
	
	public function getProductOptions($product_id) {
		$product_option_data = array();
		
		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		foreach ($product_option_query->rows as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox') {
				$product_option_value_data = array();	
				
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],						
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']					
					);
				}
				
				$product_option_data[] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'product_option_value' => $product_option_value_data,
					'required'             => $product_option['required']
				);				
			} else {
				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['option_value'],
					'required'          => $product_option['required']
				);				
			}
		}	
		
		return $product_option_data;
	}
	
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}
	
	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductRewards($product_id) {
		$product_reward_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}
		
		return $product_reward_data;
	}
		
	public function getProductDownloads($product_id) {
		$product_download_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}
		
		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}
		
		return $product_store_data;
	}
	
	public function getProductKeyword($product_id) {
		$product_keyword_data="";
		
		$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_keyword_data = $result['keyword'];
		}
		
		return $product_keyword_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}
		
		return $product_layout_data;
	}
		
	public function getProductCategories($product_id) {
		$product_category_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}
		
		return $product_related_data;
	}
	
	public function getProductTags($product_id) {
		$product_tag_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "'");
		
		$tag_data = array();
		
		foreach ($query->rows as $result) {
			$tag_data[$result['language_id']][] = $result['tag'];
		}
		
		foreach ($tag_data as $language => $tags) {
			$product_tag_data[$language] = implode(',', $tags);
		}
		
		return $product_tag_data;
	}
		
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_name'], 'UTF-8')) . "%'";
		}

		if (isset($data['filter_model']) && !is_null($data['filter_model'])) {
			$sql .= " AND LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_model'], 'UTF-8')) . "%'";
		}
		
		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}
		
		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}	
	
	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}	
	
	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
	
	public function clean( &$str, $allowBlanks=FALSE ) {
		$result = "";
		$n = strlen( $str );
		for ($m=0; $m<$n; $m++) {
			$ch = substr( $str, $m, 1 );
			if (($ch==" ") && (!$allowBlanks) || ($ch=="\n") || ($ch=="\r") || ($ch=="\t") || ($ch=="\0") || ($ch=="\x0B")) {
				continue;
			}
			$result .= $ch;
		}
		return $result;
	}
	
	protected function detect_encoding( $str ) {
		return mb_detect_encoding( $str, 'UTF-8,ISO-8859-15,ISO-8859-1,cp1251,KOI8-R' );
	}
	
	public function getTaxClassIds( &$database , $defaulttype = 0) {
		
		$taxClassIds = array();
		$sql = "SELECT `tax_class_id`, `title` FROM `".DB_PREFIX."tax_class`;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$taxClassId = $row['title'];
					$title = $row['tax_class_id'];
				}else{
					$taxClassId = $row['tax_class_id'];
					$title = $row['title'];
				}
				if (!isset($taxClassIds[strtoupper($title)])) {
					$taxClassIds[strtoupper($title)] = $taxClassId;
				}
			}
		}

		return $taxClassIds;
	}
	
	public function getWeightClassIds( &$database , $defaulttype = 0) {
		$languageId = (int)$this->config->get('config_language_id');
		
		$weightClassIds = array();
		$sql = "SELECT `weight_class_id`, `title` FROM `".DB_PREFIX."weight_class_description` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$weightClassId = $row['title'];
					$unit = $row['weight_class_id'];
				}else{
					$weightClassId = $row['weight_class_id'];
					$unit = $row['title'];
				}				
				if (!isset($weightClassIds[strtoupper($unit)])) {
					$weightClassIds[strtoupper($unit)] = $weightClassId;
				}
			}
		}

		return $weightClassIds;
	}


	public function getLengthClassIds( &$database , $defaulttype = 0) {
		$languageId = (int)$this->config->get('config_language_id');
		
		$lengthClassIds = array();
		$sql = "SELECT `length_class_id`, `title` FROM `".DB_PREFIX."length_class_description` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$lengthClassId = $row['title'];
					$unit = $row['length_class_id'];
				}else{
					$lengthClassId = $row['length_class_id'];
					$unit = $row['title'];
				}				
				if (!isset($lengthClassIds[strtoupper($unit)])) {
					$lengthClassIds[strtoupper($unit)] = $lengthClassId;
				}
			}
		}

		return $lengthClassIds;
	}
	
	public function getManufacturersIds( &$database , $defaulttype = 0) {
		$manufacturerIds = array();
		$sql = "SELECT `manufacturer_id`, `name` FROM `".DB_PREFIX."manufacturer`;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$manufacturerId = $row['name'];
					$name = $row['manufacturer_id'];
				}else{
					$manufacturerId = $row['manufacturer_id'];
					$name = $row['name'];
				}				
				if (!isset($manufacturerIds[strtoupper($name)])) {
					$manufacturerIds[strtoupper($name)] = $manufacturerId;
				}
			}
		}
		
		return $manufacturerIds;
	}
	
	
	public function getStockStatusIds( &$database , $defaulttype = 0) {
		$languageId = (int)$this->config->get('config_language_id');
		
		$stockStatusIds = array();
		$sql = "SELECT `stock_status_id`, `name` FROM `".DB_PREFIX."stock_status` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$stockStatusId = $row['name'];
					$name = $row['stock_status_id'];
				}else{
					$stockStatusId = $row['stock_status_id'];
					$name = $row['name'];
				}				
				if (!isset($stockStatusIds[strtoupper($name)])) {
					$stockStatusIds[strtoupper($name)] = $stockStatusId;
				}
			}
		}

		return $stockStatusIds;
	}
	
	public function getCategoriesIds( &$database , $defaulttype = 0) {
		$languageId = (int)$this->config->get('config_language_id');
		
		$categoriesIds = array();
		$sql = "SELECT `category_id`, `name` FROM `".DB_PREFIX."category_description` WHERE `language_id`=$languageId;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$categoryId = $row['name'];
					$name = $row['category_id'];
				}else{
					$categoryId = $row['category_id'];
					$name = $row['name'];
				}				
				if (!isset($categoriesIds[strtoupper($name)])) {
					$categoriesIds[strtoupper($name)] = $categoryId;
				}
			}
		}

		return $categoriesIds;
	}
	
	public function getStoresIds( &$database , $defaulttype = 0) {
		$storesIds = array();
		$storesIds[strtoupper('Default')] = 0;
		$storesIds[0] = 'Default';
		$sql = "SELECT `store_id`, `name` FROM `".DB_PREFIX."store`;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$storeId = $row['name'];
					$unit = $row['store_id'];
				}else{
					$storeId = $row['store_id'];
					$unit = $row['name'];
				}				
				if (!isset($storesIds[strtoupper($unit)])) {
					$storesIds[strtoupper($unit)] = $storeId;
				}
			}
		}

		return $storesIds;
	}
	
	public function getRelatedIds( &$database , $defaulttype = 0) {
		$relatedIds = array();
		$sql = "SELECT `product_id`, `model` FROM `".DB_PREFIX."product`;";
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$relatedId = $row['model'];
					$unit = $row['product_id'];
				}else{
					$relatedId = $row['product_id'];
					$unit = $row['model'];
				}				
				if (!isset($relatedIds[strtoupper($unit)])) {
					$relatedIds[strtoupper($unit)] = $relatedId;
				}
			}
		}

		return $relatedIds;
	}
	
	public function getCustomIds( &$database , $defaulttype = 0) {		
		$version_int = (int)str_replace(".", "", substr(VERSION, 0, 5));
		$customIds = array();
		if($version_int < 153){
			$sql = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group`;";
		}else{
			$languageId = (int)$this->config->get('config_language_id');
			$sql = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group_description` WHERE `language_id`=$languageId;";
		}
		$result = $database->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				if ($defaulttype){
					$customId = $row['name'];
					$unit = $row['customer_group_id'];
				}else{
					$customId = $row['customer_group_id'];
					$unit = $row['name'];
				}				
				if (!isset($customIds[strtoupper($unit)])) {
					$customIds[strtoupper($unit)] = $customId;
				}
			}
		}

		return $customIds;
	}
	
	public function getLayoutIds( &$database ) {
		$result = $database->query( "SELECT * FROM `".DB_PREFIX."layout`" );
		$layoutIds = array();
		foreach ($result->rows as $row) {
			$layoutIds[$row['name']] = $row['layout_id'];
		}
		return $layoutIds;
	}
	
	public function getAvailableStoreIds( &$database ) {
		$sql = "SELECT store_id FROM `".DB_PREFIX."store`;";
		$result = $database->query( $sql );
		$storeIds = array(0);
		foreach ($result->rows as $row) {
			if (!in_array((int)$row['store_id'],$storeIds)) {
				$storeIds[] = (int)$row['store_id'];
			}
		}
		return $storeIds;
	}
	
	public function IsExistModel( $model ) {
		
		$sql = "SELECT `product_id` FROM `".DB_PREFIX."product` WHERE UCASE(`model`)='".trim(strtoupper($this->db->escape($model)))."';";
		$result = $this->db->query( $sql );
		if ($result->rows) {
			return true;
		}else{
			return false;
		}
	}
	
	function populateProductsWorksheet( &$worksheet, &$database, $productsid )
	{
		$languageId = (int)$this->config->get('config_language_id');
		$manufacturerIds = $this->getManufacturersIds( $database , 1);
		$taxClassIds = $this->getTaxClassIds( $database , 1);
		$weightClassIds = $this->getWeightClassIds( $database , 1);		
		$lengthClassIds = $this->getLengthClassIds( $database , 1);
		$stockStatusIds = $this->getStockStatusIds( $database , 1);
		$categoriesIds_a  = $this->getCategoriesIds( $database , 1);
		$storesIds_a  = $this->getStoresIds( $database , 1);
		$relatedIds_a  = $this->getRelatedIds( $database , 1);
		$customIds = $this->getCustomIds( $database , 1);
		
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'Product Name' );
		$worksheet->writeString( $i, $j++, 'Meta Tag Description' );
		$worksheet->writeString( $i, $j++, 'Meta Tag Keywords' );
		$worksheet->writeString( $i, $j++, 'Description' );
		$worksheet->writeString( $i, $j++, 'Product Tags' );
		$worksheet->writeString( $i, $j++, 'Model' );
		$worksheet->writeString( $i, $j++, 'Price' );
		$worksheet->writeString( $i, $j++, 'Tax Class' );
		$worksheet->writeString( $i, $j++, 'Quantity' );
		$worksheet->writeString( $i, $j++, 'Minimum Quantity' );
		$worksheet->writeString( $i, $j++, 'Subtract Stock' );
		$worksheet->writeString( $i, $j++, 'Out Of Stock Status' );
		$worksheet->writeString( $i, $j++, 'Requires Shipping' );
		$worksheet->writeString( $i, $j++, 'SKU' );
		$worksheet->writeString( $i, $j++, 'UPC' );
		$worksheet->writeString( $i, $j++, 'Location' );
		$worksheet->writeString( $i, $j++, 'SEO Keyword' );
		$worksheet->writeString( $i, $j++, 'Image' );
		$worksheet->writeString( $i, $j++, 'Dimensions (L x W x H)' );
		$worksheet->writeString( $i, $j++, 'Length Class' );
		$worksheet->writeString( $i, $j++, 'Weight' );
		$worksheet->writeString( $i, $j++, 'Weight Class' );
		$worksheet->writeString( $i, $j++, 'Status' );
		$worksheet->writeString( $i, $j++, 'Sort Order' );
		$worksheet->writeString( $i, $j++, 'Manufacturer' );
		$worksheet->writeString( $i, $j++, 'Categories' );
		$worksheet->writeString( $i, $j++, 'Stores' );
		$worksheet->writeString( $i, $j++, 'Related Products' );
		$worksheet->writeString( $i, $j++, 'AdditionalImage' );
		$worksheet->writeString( $i, $j++, 'Points' );
		$worksheet->writeString( $i, $j++, 'Product Reward' );
		// The actual options data
		$i += 1;
		$j = 0;
		$query  = "SELECT * FROM `".DB_PREFIX."product` WHERE `product_id` IN ( ".$productsid." )";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {
			$productdata = array();
			$productdata = $row;
			$product_id  = $productdata["product_id"];
			$productdata = array_merge($productdata, array('product_description' => $this->getProductDescriptions($product_id)));
			$productdata = array_merge($productdata, array('product_image' => $this->getProductImages($product_id)));
			$productdata['product_image'] = array();			
			$results = $this->getProductImages($product_id);			
			foreach ($results as $result) {
				$productdata['product_image'][] = $result['image'];
			}				
			$productdata = array_merge($productdata, array('product_related' => $this->getProductRelated($product_id)));
			$productdata = array_merge($productdata, array('product_reward' => $this->getProductRewards($product_id)));
			$productdata = array_merge($productdata, array('product_tag' => $this->getProductTags($product_id)));
			$productdata = array_merge($productdata, array('product_category' => $this->getProductCategories($product_id)));
			$productdata = array_merge($productdata, array('product_store' => $this->getProductStores($product_id)));
			$productdata = array_merge($productdata, array('keyword' => $this->getProductKeyword($product_id)));
			
			$worksheet->writeString( $i, $j++, $productdata['product_description'][$languageId]['name']);
			$worksheet->writeString( $i, $j++, (isset($productdata['product_description'][$languageId]['meta_description']))? $productdata['product_description'][$languageId]['meta_description'] : "");
			$worksheet->writeString( $i, $j++, (isset($productdata['product_description'][$languageId]['meta_keyword']))? $productdata['product_description'][$languageId]['meta_keyword'] : "");
			$worksheet->writeString( $i, $j++, (isset($productdata['product_description'][$languageId]['description']))? $productdata['product_description'][$languageId]['description'] : "");
			$worksheet->writeString( $i, $j++, (isset($productdata['product_tag'][$languageId]))? $productdata['product_tag'][$languageId] : "");
			$worksheet->writeString( $i, $j++, $productdata['model']);
			$worksheet->writeString( $i, $j++, $productdata['price']);
			$worksheet->writeString( $i, $j++, (isset($taxClassIds[$productdata['tax_class_id']])) ? $taxClassIds[$productdata['tax_class_id']] : "None");
			$worksheet->writeString( $i, $j++, $productdata['quantity']);
			$worksheet->writeString( $i, $j++, $productdata['minimum']);
			$worksheet->writeString( $i, $j++, ($productdata['subtract']) ? "Yes" : "No");
			$worksheet->writeString( $i, $j++, (isset($stockStatusIds[$productdata['stock_status_id']])) ? $stockStatusIds[$productdata['stock_status_id']] : "");
			$worksheet->writeString( $i, $j++, ($productdata['shipping']) ? "Yes" : "No");
			$worksheet->writeString( $i, $j++, $productdata['sku']);
			$worksheet->writeString( $i, $j++, $productdata['upc']);
			$worksheet->writeString( $i, $j++, $productdata['location']);
			$worksheet->writeString( $i, $j++, $productdata['keyword']);
			$worksheet->writeString( $i, $j++, $productdata['image']);
			$worksheet->writeString( $i, $j++, $productdata['length'].",".$productdata['width'].",".$productdata['height']);
			$worksheet->writeString( $i, $j++, (isset($lengthClassIds[$productdata['length_class_id']])) ? $lengthClassIds[$productdata['length_class_id']] : "");
			$worksheet->writeString( $i, $j++, $productdata['weight']);
			$worksheet->writeString( $i, $j++, (isset($weightClassIds[$productdata['weight_class_id']])) ? $weightClassIds[$productdata['weight_class_id']] : "");
			$worksheet->writeString( $i, $j++, ($productdata['status']) ? "Enabled" : "Disabled");
			$worksheet->writeString( $i, $j++, $productdata['sort_order']);
			$worksheet->writeString( $i, $j++, (isset($manufacturerIds[$productdata['manufacturer_id']])) ? $manufacturerIds[$productdata['manufacturer_id']] : "None");
			$tmparr=array();
			foreach ($productdata['product_category'] as $categoryId) {
				if(isset($categoriesIds_a[$categoryId])){ 
					$tmparr[] = $categoriesIds_a[$categoryId];
				}
			}			
			$worksheet->writeString( $i, $j++, implode(",", $tmparr));
			$tmparr=array();
			foreach ($productdata['product_store'] as $storeId) {
				if(isset($storesIds_a[$storeId])){ 
					$tmparr[] = $storesIds_a[$storeId];
				}
			}	
			$worksheet->writeString( $i, $j++, implode(",", $tmparr));
			$tmparr=array();
			foreach ($productdata['product_related'] as $relatedId) {
				if(isset($relatedIds_a[$relatedId])){
					$tmparr[] = $relatedIds_a[$relatedId];
				}
			}
			$worksheet->writeString( $i, $j++, implode(",", $tmparr));			
			$worksheet->writeString( $i, $j++, implode(",", $productdata['product_image']));
			$worksheet->writeString( $i, $j++, $productdata['points']);
			$tmparr=array();
			foreach ($productdata['product_reward'] as $customer_group_id => $value) {
				if(isset($customIds[$customer_group_id])){
					$tmparr[] = $customIds[$customer_group_id]."=".$value['points'];
				}
			}
			$worksheet->writeString( $i, $j++, implode(",", $tmparr));
			$i += 1;
			$j = 0;
		}
	}

	public function exportProducts($selectid) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;

		// We use the package from http://pear.php.net/package/Spreadsheet_Excel_Writer/
		chdir( DIR_SYSTEM . 'pear' );
		require_once "Spreadsheet/Excel/Writer.php";
		chdir( DIR_APPLICATION );
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(DIR_CACHE);
		$workbook->setVersion(8); // Use Excel97/2000 BIFF8 Format
		
		// sending HTTP headers
		$workbook->send('download_products.xls');
		
		// Creating the categories worksheet
		$worksheet =& $workbook->addWorksheet('sheet');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateProductsWorksheet( $worksheet, $database, $selectid); 
		//$worksheet->freezePanes(array(1, 1, 1, 1));

		$workbook->close();
		
		// Clear the spreadsheet caches
		$this->clearSpreadsheetCache();
		exit;
	}
	
	public function upload( $filename, $bulktype ) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		
		ini_set("memory_limit","512M");
		ini_set("max_execution_time",180);
		//set_time_limit( 60 );
		chdir( DIR_SYSTEM . 'PHPExcel' );
		require_once( 'Classes/PHPExcel.php' );
		chdir( DIR_APPLICATION );
		$inputFileType = PHPExcel_IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$reader = $objReader->load($filename);
		$ok = $this->validateUpload( $reader );
		if (!$ok) {
			return FALSE;
		}
		$this->clearCache();
		$this->uploadProducts( $reader, $bulktype );
		return $ok;
	}
	
	public function uploadProducts( &$reader, $bulktype ){
		$data = $reader->getSheet(0);
		$products = array();
		$isFirstRow = TRUE;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$Dimensions = explode( ",", $this->getCell($data,$i,19));
			$product = array();
			$product['name'] =  $this->getCell($data,$i,1);
			$product['description'] = $this->getCell($data,$i,4);
			$product['meta_description'] = $this->getCell($data,$i,2);
			$product['meta_keywords'] = $this->getCell($data,$i,3);
			$product['model'] = trim($this->getCell($data,$i,6,''));
			if ($product['model']=="") continue;
			$product['quantity'] = $this->getCell($data,$i,9);
			$product['manufacturer'] =  $this->getCell($data,$i,25);
			$product['image'] = $this->getCell($data,$i,18);
			$product['shipping'] = $this->getCell($data,$i,13);
			$product['price'] = $this->getCell($data,$i,7);
			$product['points'] = $this->getCell($data,$i,30);
			$product['weight'] = $this->getCell($data,$i,21);
			$product['unit'] = $this->getCell($data,$i,22);
			$product['status'] = $this->getCell($data,$i,23);
			$product['tax_class_id'] = $this->getCell($data,$i,8);
			$product['stock_status_id'] = $this->getCell($data,$i,12);
			$product['length'] = (count($Dimensions)==3) ? $Dimensions[0] : "";
			$product['width'] = (count($Dimensions)==3) ? $Dimensions[1] : "";
			$product['height'] = (count($Dimensions)==3) ? $Dimensions[2] : "";
			$product['seo_keyword'] = $this->getCell($data,$i,17);
			$product['measurement_unit'] = $this->getCell($data,$i,20);
			$product['sku'] = $this->getCell($data,$i,14);
			$product['upc'] = $this->getCell($data,$i,15);
			$product['location'] = $this->getCell($data,$i,16);
			$product['tags'] = $this->getCell($data,$i,5);
			$product['subtract'] = $this->getCell($data,$i,11);
			$product['minimum'] = $this->getCell($data,$i,10);
			$product['sort_order'] = $this->getCell($data,$i,24);
			$product['date_available'] = date('Y-m-d',mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			$categories = trim( $this->clean($this->getCell($data,$i,26), TRUE) );
			$product['categories'] = ($categories=="") ? array() : explode( ",", $categories );
			if ($product['categories']===FALSE) {
				$product['categories'] = array();
			}
			$storeIds = trim( $this->clean($this->getCell($data,$i,27), true) );
			$product['store_ids'] = ($storeIds=="") ? array() : explode( ",", $storeIds );
			if ($product['store_ids']===FALSE) {
				$product['store_ids'] = array();
			}
			$related = trim( $this->clean($this->getCell($data,$i,28), true) );
			$product['related_ids'] = ($related=="") ? array() : explode( ",", $related );
			if ($product['related_ids']===FALSE) {
				$product['related_ids'] = array();
			}
			$additionalImageNames = trim( $this->clean($this->getCell($data,$i,29), true) );
			$product['additionalImage'] = ($additionalImageNames=="") ? array() : explode( ",",$additionalImageNames  );
			if ($product['additionalImage']===FALSE) {
				$product['additionalImage'] = array();
			}
			$reward = trim( $this->clean($this->getCell($data,$i,31), true) );
			$product['reward'] = ($reward=="") ? array() : explode( ",", $reward );
			if ($product['reward']===FALSE) {
				$product['reward'] = array();
			}
			$products[] = $product;
		}
		if($bulktype)
		  $this->addProducts($products);
		else
		  $this->updateProducts($products);
	}
	
	public function addProducts(&$products ){
		$database =& $this->db;
		$languageId = (int)$this->config->get('config_language_id');
		$defaultStockStatusId = $this->config->get('config_stock_status_id');
		
		$availableStoreIds = $this->getAvailableStoreIds( $database );
		$manufacturerIds = $this->getManufacturersIds( $database );
		$taxClassIds = $this->getTaxClassIds( $database );
		$weightClassIds = $this->getWeightClassIds( $database );		
		$lengthClassIds = $this->getLengthClassIds( $database );
		$stockStatusIds = $this->getStockStatusIds( $database );
		$categoriesIds_a  = $this->getCategoriesIds( $database );
		$storesIds_a  = $this->getStoresIds( $database );
		$relatedIds_a  = $this->getRelatedIds( $database );
		$customIds = $this->getCustomIds( $database );
		
		foreach ($products as $product) {
			if ($this->IsExistModel($product['model'])) continue;
			$productdata = array();
			$productdata['product_description'][$languageId]['name'] = $product['name'];
			$productdata['product_description'][$languageId]['description'] = $product['description'];
			$productdata['product_description'][$languageId]['meta_description'] = $product['meta_description'];
			$productdata['product_description'][$languageId]['meta_keyword'] = $product['meta_keywords'];
			$productdata['product_tag'][$languageId] =$product['tags'];
			$productdata['model'] = $product['model'];
			$productdata['price'] = trim($product['price']);
			$productdata['tax_class_id'] = (isset($taxClassIds[strtoupper($product['tax_class_id'])])) ? $taxClassIds[strtoupper($product['tax_class_id'])] : 0;
			$productdata['quantity'] = $product['quantity'];
			$productdata['minimum'] = $product['minimum'];
			$productdata['subtract'] = ((strtoupper($product['subtract'])=="TRUE") || (strtoupper($product['subtract'])=="YES") || (strtoupper($product['subtract'])=="ENABLED")) ? 1 : 0;
			$productdata['stock_status_id'] = (isset($stockStatusIds[strtoupper($product['stock_status_id'])])) ? $stockStatusIds[strtoupper($product['stock_status_id'])] : $defaultStockStatusId;
			$productdata['shipping'] = ((strtoupper($product['shipping'])=="YES") || (strtoupper($product['shipping'])=="Y") || (strtoupper($product['shipping'])=="TRUE")) ? 1 : 0;
			$productdata['sku'] = $database->escape($product['sku']);
			$productdata['upc'] = $database->escape($product['upc']);
			$productdata['location'] = $database->escape($product['location']);
			$productdata['keyword'] = $database->escape($product['seo_keyword']);
			$productdata['image'] = $product['image'];
			$productdata['date_available'] = $product['date_available'];
			$productdata['length'] = $product['length'];
			$productdata['width'] = $product['width'];
			$productdata['height'] = $product['height'];
			$productdata['length_class_id'] = (isset($lengthClassIds[strtoupper($product['measurement_unit'])])) ? $lengthClassIds[strtoupper($product['measurement_unit'])] : 0;
			$productdata['weight'] = ($product['weight']=="") ? 0 : $product['weight'];
			$productdata['weight_class_id'] = (isset($weightClassIds[strtoupper($product['unit'])])) ? $weightClassIds[strtoupper($product['unit'])] : 0;
			$productdata['status'] = ((strtoupper($product['status'])=="TRUE") || (strtoupper($product['status'])=="YES") || (strtoupper($product['status'])=="ENABLED")) ? 1 : 0;
			$productdata['sort_order'] = $product['sort_order'];
			$productdata['manufacturer_id'] = (($product['manufacturer']=="") || (strtoupper($product['manufacturer'])=="NONE") || (!isset($manufacturerIds[strtoupper($product['manufacturer'])]))) ? 0 : $manufacturerIds[strtoupper($product['manufacturer'])];
			$productdata['product_category'] = array();
			foreach ($product['categories'] as $categoryId) {
				if(isset($categoriesIds_a[strtoupper($categoryId)])){ 
					$productdata['product_category'][] = $categoriesIds_a[strtoupper($categoryId)];
				}
			}
			$productdata['product_store'] = array();
			foreach ($product['store_ids'] as $storeId) {
				if (isset($storesIds_a[strtoupper($storeId)])) {
					$productdata['product_store'][] = $storesIds_a[strtoupper($storeId)];
				}
			}
			$productdata['product_related'] = array();
			foreach ($product['related_ids'] as $relatedId) {
				if(isset($relatedIds_a[strtoupper($relatedId)])){
					$productdata['product_related'][] = $relatedIds_a[strtoupper($relatedId)];
				}
			}
			$productdata['points'] = $product['points'];
			$productdata['product_image'] = $product['additionalImage'];
			$productdata['product_reward'] = array();
			foreach ($product['reward'] as $rewardPart) {
				$nextReward = explode('=',$rewardPart);
				if ($nextReward===FALSE) {
					$nextReward = array( $rewardPart, 0 );
				} else if (count($nextReward)==1) {
					$nextReward = array( $rewardPart, 0 );
				}
				if (isset($customIds[strtoupper($nextReward[0])]) ) {
					$productdata['product_reward'][$customIds[strtoupper($nextReward[0])]]['points'] = $nextReward[1];
				}
			}
			$this->addProduct($productdata);
		}
	}
	
	public function updateProducts(&$products ){
		$database =& $this->db;
		$languageId = (int)$this->config->get('config_language_id');
		$defaultStockStatusId = $this->config->get('config_stock_status_id');
		
		$availableStoreIds = $this->getAvailableStoreIds( $database );
		$manufacturerIds = $this->getManufacturersIds( $database );
		$taxClassIds = $this->getTaxClassIds( $database );
		$weightClassIds = $this->getWeightClassIds( $database );		
		$lengthClassIds = $this->getLengthClassIds( $database );
		$stockStatusIds = $this->getStockStatusIds( $database );
		$categoriesIds_a  = $this->getCategoriesIds( $database );
		$storesIds_a  = $this->getStoresIds( $database );
		$relatedIds_a  = $this->getRelatedIds( $database );
		$customIds = $this->getCustomIds( $database );
		
		foreach ($products as $product) {
			if (!$this->IsExistModel($product['model'])) continue;
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE UCASE(p.model) = '" . trim(strtoupper($this->db->escape($product['model']))) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
			if (!$query->num_rows) continue;
			$productdata = array();
			$productdata = $query->row;
			$product_id  = $productdata["product_id"];
			$productdata = array_merge($productdata, array('product_attribute' => $this->getProductAttributes($product_id)));
			$productdata = array_merge($productdata, array('product_description' => $this->getProductDescriptions($product_id)));			
			$productdata = array_merge($productdata, array('product_discount' => $this->getProductDiscounts($product_id)));
			$productdata = array_merge($productdata, array('product_image' => $this->getProductImages($product_id)));			
			$productdata['product_image'] = array();			
			$results = $this->getProductImages($product_id);			
			foreach ($results as $result) {
				$productdata['product_image'][] = $result['image'];
			}						
			$productdata = array_merge($productdata, array('product_option' => $this->getProductOptions($product_id)));
			$productdata = array_merge($productdata, array('product_related' => $this->getProductRelated($product_id)));
			$productdata = array_merge($productdata, array('product_reward' => $this->getProductRewards($product_id)));
			$productdata = array_merge($productdata, array('product_special' => $this->getProductSpecials($product_id)));
			$productdata = array_merge($productdata, array('product_tag' => $this->getProductTags($product_id)));
			$productdata = array_merge($productdata, array('product_category' => $this->getProductCategories($product_id)));
			$productdata = array_merge($productdata, array('product_download' => $this->getProductDownloads($product_id)));
			$productdata = array_merge($productdata, array('product_layout' => $this->getProductLayouts($product_id)));
			$productdata = array_merge($productdata, array('product_store' => $this->getProductStores($product_id)));
			$productdata = array_merge($productdata, array('keyword' => $this->getProductKeyword($product_id)));
			
			if(trim($product['name'])!="") $productdata['product_description'][$languageId]['name'] = $product['name'];
			if(trim($product['description'])!="") $productdata['product_description'][$languageId]['description'] = $product['description'];
			if(trim($product['meta_description'])!="") $productdata['product_description'][$languageId]['meta_description'] = $product['meta_description'];
			if(trim($product['meta_keywords'])!="") $productdata['product_description'][$languageId]['meta_keyword'] = $product['meta_keywords'];
			if(trim($product['tags'])!="") $productdata['product_tag'][$languageId] =$product['tags'];
			if(trim($product['model'])!="") $productdata['model'] = $product['model'];
			if(trim($product['price'])!="") $productdata['price'] = trim($product['price']);
			if(trim($product['tax_class_id'])!="") $productdata['tax_class_id'] = (isset($taxClassIds[strtoupper($product['tax_class_id'])])) ? $taxClassIds[strtoupper($product['tax_class_id'])] : 0;
			if(trim($product['quantity'])!="") $productdata['quantity'] = $product['quantity'];
			if(trim($product['minimum'])!="") $productdata['minimum'] = $product['minimum'];
			if(trim($product['subtract'])!="") $productdata['subtract'] = ((strtoupper($product['subtract'])=="TRUE") || (strtoupper($product['subtract'])=="YES") || (strtoupper($product['subtract'])=="ENABLED")) ? 1 : 0;
			if(trim($product['stock_status_id'])!="") $productdata['stock_status_id'] = (isset($stockStatusIds[strtoupper($product['stock_status_id'])])) ? $stockStatusIds[strtoupper($product['stock_status_id'])] : $defaultStockStatusId;
			if(trim($product['shipping'])!="") $productdata['shipping'] = ((strtoupper($product['shipping'])=="YES") || (strtoupper($product['shipping'])=="Y") || (strtoupper($product['shipping'])=="TRUE")) ? 1 : 0;
			if(trim($product['sku'])!="") $productdata['sku'] = $database->escape($product['sku']);
			if(trim($product['upc'])!="") $productdata['upc'] = $database->escape($product['upc']);
			if(trim($product['location'])!="") $productdata['location'] = $database->escape($product['location']);
			if(trim($product['seo_keyword'])!="") $productdata['keyword'] = $database->escape($product['seo_keyword']);
			if(trim($product['image'])!="") $productdata['image'] = $product['image'];
			//if(trim($product['date_available'])!="") $productdata['date_available'] = $product['date_available'];
			if(trim($product['length'])!="") $productdata['length'] = $product['length'];
			if(trim($product['width'])!="") $productdata['width'] = $product['width'];
			if(trim($product['height'])!="") $productdata['height'] = $product['height'];
			if(trim($product['measurement_unit'])!="") $productdata['length_class_id'] = (isset($lengthClassIds[strtoupper($product['measurement_unit'])])) ? $lengthClassIds[strtoupper($product['measurement_unit'])] : 0;
			if(trim($product['weight'])!="") $productdata['weight'] = ($product['weight']=="") ? 0 : $product['weight'];
			if(trim($product['unit'])!="") $productdata['weight_class_id'] = (isset($weightClassIds[strtoupper($product['unit'])])) ? $weightClassIds[strtoupper($product['unit'])] : 0;
			if(trim($product['status'])!="") $productdata['status'] = ((strtoupper($product['status'])=="TRUE") || (strtoupper($product['status'])=="YES") || (strtoupper($product['status'])=="ENABLED")) ? 1 : 0;
			if(trim($product['sort_order'])!="") $productdata['sort_order'] = $product['sort_order'];
			if(trim($product['manufacturer'])!="") $productdata['manufacturer_id'] = (($product['manufacturer']=="") || (strtoupper($product['manufacturer'])=="NONE") || (!isset($manufacturerIds[strtoupper($product['manufacturer'])]))) ? 0 : $manufacturerIds[strtoupper($product['manufacturer'])];
			if(count($product['categories']) > 0) {
			$productdata['product_category'] = array();
			foreach ($product['categories'] as $categoryId) {
				if(isset($categoriesIds_a[strtoupper($categoryId)])){ 
					$productdata['product_category'][] = $categoriesIds_a[strtoupper($categoryId)];
				}
			}}
			if(count($product['store_ids'])>0) {
			$productdata['product_store'] = array();
			foreach ($product['store_ids'] as $storeId) {
				if (isset($storesIds_a[strtoupper($storeId)])) {
					$productdata['product_store'][] = $storesIds_a[strtoupper($storeId)];
				}
			}}
			if(count($product['related_ids'])>0) {
			$productdata['product_related'] = array();
			foreach ($product['related_ids'] as $relatedId) {
				if(isset($relatedIds_a[strtoupper($relatedId)])){
					$productdata['product_related'][] = $relatedIds_a[strtoupper($relatedId)];
				}
			}}
			if(trim($product['points'])!="") $productdata['points'] = $product['points'];
			if(count($product['additionalImage'])>0) $productdata['product_image'] = $product['additionalImage'];
			if(count($product['reward'])>0) {
			$productdata['product_reward'] = array();
			foreach ($product['reward'] as $rewardPart) {
				$nextReward = explode('=',$rewardPart);
				if ($nextReward===FALSE) {
					$nextReward = array( $rewardPart, 0 );
				} else if (count($nextReward)==1) {
					$nextReward = array( $rewardPart, 0 );
				}
				if (isset($customIds[strtoupper($nextReward[0])]) ) {
					$productdata['product_reward'][$customIds[strtoupper($nextReward[0])]]['points'] = $nextReward[1];
				}
			}}
			$this->editProduct($product_id, $productdata);
		}
	}
	
	public function validateUpload( &$reader )
	{
		if (!$this->validateProducts( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time()).$this->language->get('error_products_header')."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		return TRUE;
	}
	
	public function getCell(&$worksheet,$row,$col,$default_val='') {
		$col -= 1; // we use 1-based, PHPExcel uses 0-based column index
		$row += 1; // we use 0-based, PHPExcel used 1-based row index
		return ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
	}
	
	public function validateHeading( &$data, &$expected ) {
		$heading = array();
		$k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
		if ($k != count($expected)) {
			return FALSE;
		}
		$i = 0;
		for ($j=1; $j <= $k; $j+=1) {
			$heading[] = $this->getCell($data,$i,$j);
		}
		$valid = TRUE;
		for ($i=0; $i < count($expected); $i+=1) {
			if (!isset($heading[$i])) {
				$valid = FALSE;
				break;
			}
			if (strtolower($heading[$i]) != strtolower($expected[$i])) {
				$valid = FALSE;
				break;
			}
		}
		return $valid;
	}
	
	
    public function validateProducts( &$reader )
	{
		$expectedProductHeading = array
		( "Product Name", "Meta Tag Description", "Meta Tag Keywords", "Description", "Product Tags", "Model", "Price", "Tax Class", "Quantity", "Minimum Quantity", "Subtract Stock", "Out Of Stock Status", "Requires Shipping", "SKU", "UPC", "Location", "SEO Keyword", "Image", "Dimensions (L x W x H)", "Length Class", "Weight", "Weight Class", "Status", "Sort Order", "Manufacturer", "Categories", "Stores", "Related Products", "AdditionalImage", "Points", "Product Reward" );
		$data =& $reader->getSheet(0);
		return $this->validateHeading( $data, $expectedProductHeading );
	}
	
	protected function clearSpreadsheetCache() {
		$files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');
		
		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					@unlink($file);
					clearstatcache();
				}
			}
		}
	}
	
	public function clearCache() {
		$this->cache->delete('category');
		$this->cache->delete('category_description');
		$this->cache->delete('manufacturer');
		$this->cache->delete('product');
		$this->cache->delete('product_image');
		$this->cache->delete('product_option');
		$this->cache->delete('product_option_description');
		$this->cache->delete('product_option_value');
		$this->cache->delete('product_option_value_description');
		$this->cache->delete('product_to_category');
		$this->cache->delete('url_alias');
		$this->cache->delete('product_special');
		$this->cache->delete('product_discount');
	}
}
?>