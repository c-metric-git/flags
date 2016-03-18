<?php
class ModelCatalogSEOReport extends Model {
	
	public function gettotalSEOs($data = array()) {
		
			$sql = "select count(*) as total from (select 'Product' as type, pd.name, p.product_id as id, u.keyword, pd.meta_description, pd.meta_keyword, GROUP_CONCAT(pt.tag) AS tags from " . DB_PREFIX . "product p
				  left join " . DB_PREFIX . "product_description pd on p.product_id = pd.product_id
				  left join " . DB_PREFIX . "url_alias u on p.product_id = replace(u.query, 'product_id=','')
				  left join " . DB_PREFIX . "product_tag pt on pt.product_id = p.product_id
				  where (pd.language_id = '" . (int)$this->session->data['language_id'] . "' or pd.language_id is null) and (pt.language_id = '" . (int)$this->session->data['language_id'] . "' or pt.language_id is null)
				  group by p.product_id
				  
				union

				select 'Category', cd.name, c.category_id, u.keyword, cd.meta_description, cd.meta_keyword, -1 from " . DB_PREFIX . "category c
				  inner join " . DB_PREFIX . "category_description cd on c.category_id = cd.category_id
				  left join " . DB_PREFIX . "url_alias u on c.category_id = replace(u.query, 'category_id=','')
				  where cd.language_id = '" . (int)$this->session->data['language_id'] . "'
				  
				union

				select 'Information', id.title, i.information_id, u.keyword, -1, -1, -1 from " . DB_PREFIX . "information i
				  inner join " . DB_PREFIX . "information_description id on id.information_id = i.information_id
				  left join " . DB_PREFIX . "url_alias u on i.information_id = replace(u.query, 'information_id=','')
				  where id.language_id = '" . (int)$this->session->data['language_id'] . "'
				  
				union

				select 'Manufacturer', m.name, m.manufacturer_id, u.keyword, -1, -1, -1 from " . DB_PREFIX . "manufacturer m
				  left join " . DB_PREFIX . "url_alias u on m.manufacturer_id = replace(u.query, 'manufacturer_id=','')
				  
				)x ";
								
			//$sql .= " WHERE pd.language_id = '" . (int)$this->session->data['language_id'] . "'"; 
			$sql .= ' where 1=1 ';
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			if (!empty($data['filter_keyword'])) {
				$sql .= " AND LCASE(keyword) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_keyword'])) . "%'";
			}
			
			if (!empty($data['filter_meta_description'])) {
				$sql .= " AND LCASE(meta_description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_meta_description'])) . "%'";
			}
			
			if (!empty($data['filter_meta_keyword'])) {
				$sql .= " AND LCASE(meta_keyword) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_meta_keyword'])) . "%'";
			}
			
			if (!empty($data['filter_tags'])) {
				$sql .= " AND LCASE(tags) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tags'])) . "%'";
			}
			
			if (isset($data['filter_type']) && !is_null($data['filter_type'])) {
			$sql .= " AND LCASE(type) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_type'])) . "%'";
			}

			$query = $this->db->query($sql);
		
			return $query->row['total'];
		 
	}
	
	public function getSEOs($data = array()) {
		
			$sql = "select * from (select 'Product' as type, pd.name as name, p.product_id as id, u.keyword, pd.meta_description, pd.meta_keyword, GROUP_CONCAT(pt.tag SEPARATOR ', ') AS tags from " . DB_PREFIX . "product p
				  left join " . DB_PREFIX . "product_description pd on p.product_id = pd.product_id
				  left join " . DB_PREFIX . "url_alias u on p.product_id = replace(u.query, 'product_id=','')
				  left join " . DB_PREFIX . "product_tag pt on pt.product_id = p.product_id
				  where pd.language_id = '" . (int)$this->session->data['language_id'] . "' and (pt.language_id = '" . (int)$this->session->data['language_id'] . "' or pt.language_id is null)
				  group by p.product_id
				 				  
				union

				select 'Category', cd.name, c.category_id, u.keyword, cd.meta_description, cd.meta_keyword, -1 from " . DB_PREFIX . "category c
				  inner join " . DB_PREFIX . "category_description cd on c.category_id = cd.category_id
				  left join " . DB_PREFIX . "url_alias u on c.category_id = replace(u.query, 'category_id=','')
				  where cd.language_id = '" . (int)$this->session->data['language_id'] . "'
				  
				union

				select 'Information', id.title, i.information_id, u.keyword, -1, -1, -1 from " . DB_PREFIX . "information i
				  inner join " . DB_PREFIX . "information_description id on id.information_id = i.information_id
				  left join " . DB_PREFIX . "url_alias u on i.information_id = replace(u.query, 'information_id=','')
				  where id.language_id = '" . (int)$this->session->data['language_id'] . "'
				  
				union

				select 'Manufacturer', m.name, m.manufacturer_id, u.keyword, -1, -1, -1 from " . DB_PREFIX . "manufacturer m
				  left join " . DB_PREFIX . "url_alias u on m.manufacturer_id = replace(u.query, 'manufacturer_id=','')
				  
				)x ";
			$sql .= ' where 1=1 ';
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}
			
			if (!empty($data['filter_keyword'])) {
				$sql .= " AND LCASE(keyword) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_keyword'])) . "%'";
			}
			
			if (!empty($data['filter_meta_description'])) {
				$sql .= " AND LCASE(meta_description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_meta_description'])) . "%'";
			}
			
			if (!empty($data['filter_meta_keyword'])) {
				$sql .= " AND LCASE(meta_keyword) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_meta_keyword'])) . "%'";
			}
			
			if (!empty($data['filter_tags'])) {
				$sql .= " AND LCASE(tags) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tags'])) . "%'";
			}
			
			if (isset($data['filter_type']) && !is_null($data['filter_type'])) {
			$sql .= " AND LCASE(type) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_type'])) . "%'";
			}

			
						
			$sort_data = array(
				'type',
				'name',
				'keyword',
				'meta_description',
				'meta_keyword',
				'tags'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY type desc, name";	
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
		 
	}
	
			
}
?>