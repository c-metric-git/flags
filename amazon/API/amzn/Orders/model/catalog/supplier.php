<?php
class ModelCatalogSupplier extends Model {
	public function addSupplier($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "supplier SET name = '" . $this->db->escape($data['name']) . "', username = '" . $this->db->escape($data['username']) . "', password = '" . $this->db->escape(md5($data['password'])) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', notes = '" . $this->db->escape($data['notes']) . "', sort_order = '" . (int)$data['sort_order'] . "', email_frequency = '" . $this->db->escape($data['email_frequency']) . "', date_added = NOW()");
		
		$supplier_id = $this->db->getLastId();

		
		if (isset($data['supplier_store'])) {
			foreach ($data['supplier_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "supplier_to_store SET supplier_id = '" . (int)$supplier_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
				
		
		$this->cache->delete('supplier');
	}
	
	public function editSupplier($supplier_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "supplier SET name = '" . $this->db->escape($data['name']) . "', username = '" . $this->db->escape($data['username']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', notes = '" . $this->db->escape($data['notes']) . "', email_frequency = '" . $this->db->escape($data['email_frequency']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE supplier_id = '" . (int)$supplier_id . "'");
		
		
		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "supplier` SET password = '" . $this->db->escape(md5($data['password'])) . "' WHERE supplier_id = '" . (int)$supplier_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "supplier_to_store WHERE supplier_id = '" . (int)$supplier_id . "'");

		if (isset($data['supplier_store'])) {
			foreach ($data['supplier_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "supplier_to_store SET supplier_id = '" . (int)$supplier_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
			
		
		$this->cache->delete('supplier');
	}
	
	public function deleteSupplier($supplier_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "supplier WHERE supplier_id = '" . (int)$supplier_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "supplier_to_store WHERE supplier_id = '" . (int)$supplier_id . "'");
			
		$this->cache->delete('supplier');
	}	
	
	public function getSupplier($supplier_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "supplier WHERE supplier_id = '" . (int)$supplier_id . "'");
		
		return $query->row;
	}
	
	public function getSuppliers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "supplier";
			
			$sort_data = array(
				'name',
				'sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY name";	
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
			$supplier_data = $this->cache->get('supplier');
		
			if (!$supplier_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "supplier ORDER BY name");
	
				$supplier_data = $query->rows;
			
				$this->cache->set('supplier', $supplier_data);
			}
		 
			return $supplier_data;
		}
	}
	
	public function getSupplierStores($supplier_id) {
		$supplier_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "supplier_to_store WHERE supplier_id = '" . (int)$supplier_id . "'");

		foreach ($query->rows as $result) {
			$supplier_store_data[] = $result['store_id'];
		}
		
		return $supplier_store_data;
	}
	
	public function getTotalSuppliersByImageId($image_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "supplier WHERE image_id = '" . (int)$image_id . "'");

		return $query->row['total'];
	}

	public function getTotalSuppliers() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "supplier");
		
		return $query->row['total'];
	}	
}
?>