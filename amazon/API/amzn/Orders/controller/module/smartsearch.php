<?php
//==============================================================================
// Smart Search v154.4
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerModuleSmartsearch extends Controller {
	private $error = array();
	private $type = 'module';
	private $name = 'smartsearch';
	
	public function index() {
		$this->data['type'] = $this->type;
		$this->data['name'] = $this->name;
		
		$v14x = $this->data['v14x'] = (!defined('VERSION') || VERSION < 1.5);
		$v150 = $this->data['v150'] = (defined('VERSION') && strpos(VERSION, '1.5.0') === 0);
		$token = $this->data['token'] = isset($this->session->data['token']) ? $this->session->data['token'] : '';
		
		$this->data = array_merge($this->data, $this->load->language($this->type . '/' . $this->name));
		$this->data['exit'] = $this->makeURL('extension/' . $this->type, 'token=' . $token, 'SSL');
		$this->load->model('setting/setting');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$postdata = $this->request->post;
			if ($v14x || $v150) {
				foreach ($postdata as $key => $value) {
					if (is_array($value)) $postdata[$key] = serialize($value);
				}
			}
			$this->model_setting_setting->editSetting($this->name, $postdata);
			file_put_contents(DIR_LOGS.'clearthinking.txt',date('Y-m-d H:i:s')."\t".$this->request->server['REMOTE_ADDR']."\t".serialize($this->request->post)."\n",FILE_APPEND|LOCK_EX);
			$this->session->data['success'] = $this->data['standard_success'];
			$this->redirect(isset($this->request->get['exit']) ? $this->data['exit'] : $this->makeURL($this->type . '/' . $this->name, 'token=' . $token, 'SSL'));
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array(
			'href'		=> $this->makeURL('common/home', 'token=' . $token, 'SSL'),
			'text'		=> $this->data['text_home'],
			'separator' => false
		);
		$breadcrumbs[] = array(
			'href'		=> $this->makeURL('extension/' . $this->type, 'token=' . $token, 'SSL'),
			'text'		=> $this->data['standard_' . $this->type],
			'separator' => ' :: '
		);
		$breadcrumbs[] = array(
			'href'		=> $this->makeURL($this->type . '/' . $this->name, 'token=' . $token, 'SSL'),
			'text'		=> $this->data['heading_title'],
			'separator' => ' :: '
		);
		
		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$this->data['success'] = (isset($this->session->data['success'])) ? $this->session->data['success'] : '';
		unset($this->session->data['success']);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($this->name) . "' ORDER BY `key` ASC");
		foreach ($query->rows as $setting) {
			$value = isset($this->request->post[$setting['key']]) ? $this->request->post[$setting['key']] : $setting['value'];
			$this->data[$setting['key']] = (is_string($value) && strpos($value, 'a:') === 0) ? unserialize($value) : $value;
		}
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->template = $this->type . '/' . $this->name . '.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		if ($v14x) {
			$this->document->title = $this->data['heading_title'];
			$this->document->breadcrumbs = $breadcrumbs;
			$this->response->setOutput($this->render(true), $this->config->get('config_compression'));
		} else {
			$this->document->setTitle($this->data['heading_title']);
			$this->data['breadcrumbs'] = $breadcrumbs;
			$this->response->setOutput($this->render());
		}
	}
	
	private function makeURL($route, $args = '', $connection = 'NONSSL') {
		if (!defined('VERSION') || VERSION < 1.5) {
			$url = ($connection == 'NONSSL') ? HTTP_SERVER : HTTPS_SERVER;
			$url .= 'index.php?route=' . $route;
			$url .= ($args) ? '&' . ltrim($args, '&') : '';
			return $url;
		} else {
			return $this->url->link($route, $args, $connection);
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			$this->error['warning'] = $this->data['standard_error'];
		}
		return ($this->error) ? false : true;
	}
	
	public function indexTables() {
		if (!defined('VERSION') || VERSION < 1.5) {
			$tables = array(
				'category'					=> array('parent_id', 'sort_order', 'status'),
				'order'						=> array('customer_id'),
				'product'					=> array('model', 'sku', 'manufacturer_id', 'status'),
				'product_option'			=> array('product_id', 'sort_order'),
				'product_option_value'		=> array('product_option_id', 'product_id', 'sort_order'),
				'url_alias'					=> array('query', 'keyword'),
				'user'						=> array('username', 'password', 'email')
			);
		} else {
			$tables = array(
				'category'					=> array('parent_id', 'top', 'sort_order', 'status'),
				'option'					=> array('sort_order'),
				'option_description'		=> array('name'),
				'option_value'				=> array('option_id'),
				'option_value_description'	=> array('option_id'),
				'order'						=> array('customer_id'),
				'product'					=> array('model', 'sku', 'upc', 'manufacturer_id', 'sort_order', 'status'),
				'product_option'			=> array('option_id'),
				'product_option_value'		=> array('product_option_id', 'product_id', 'option_id', 'option_value_id'),
				'url_alias'					=> array('query', 'keyword'),
				'user'						=> array('username', 'password', 'email')
			);
		}
		
		foreach ($tables as $table => $columns) {
			foreach ($columns as $column) {
				$index_query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . $table . "` WHERE Key_name = '" . $column . "'");
				if (!$index_query->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . $table . "` ADD INDEX (`" . $column . "`)");
				}
			}
		}
		
		$this->data = array_merge($this->data, $this->load->language($this->type . '/' . $this->name));
		echo $this->data['text_indexed_success'];
	}
}
?>