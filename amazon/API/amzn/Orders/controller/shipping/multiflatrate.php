<?php
//==============================================================================
// Multi Flat Rate Shipping v154.1
//
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerShippingMultiflatrate extends Controller {
	private $error = array();
 	private $type = 'shipping';
	private $name = 'multiflatrate';
	
	public function index() {	 
		$this->data['type'] = $this->type;
		$this->data['name'] = $this->name;
		
		$v14x = $this->data['v14x'] = (!defined('VERSION') || VERSION < 1.5);
		$v150 = $this->data['v150'] = (defined('VERSION') && strpos(VERSION, '1.5.0') === 0);
		$token = $this->data['token'] = (isset($this->session->data['token'])) ? $this->session->data['token'] : '';
		
		$this->data = array_merge($this->data, $this->load->language($this->type . '/' . $this->name));
		$this->data['exit'] = $this->makeURL('extension/' . $this->type, 'token=' . $token, 'SSL');
		$this->load->model('setting/setting');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			if (isset($this->request->post) && strlen(serialize($this->request->post)) > 65535) {
				$setting_table = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "setting WHERE Field = 'value'");
				if (strtolower($setting_table->row['Type']) == 'text') {
					$this->db->query("ALTER TABLE " . DB_PREFIX . "setting MODIFY `value` MEDIUMTEXT NOT NULL");
				}
			}
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
		
		// non-standard
		$this->data['selectall_links'] = '<div class="selectall-links"><a onclick="$(this).parent().prev().find(\':checkbox\').attr(\'checked\', true)">' . $this->data['text_select_all'] . '</a> / <a onclick="$(this).parent().prev().find(\':checkbox\').attr(\'checked\', false)">' . $this->data['text_unselect_all'] . '</a></div>';
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		array_unshift($this->data['tax_classes'], array('tax_class_id' => 0, 'title' => $this->data['text_none']));
		
		$this->data['order_criteria'] = array(
			'store',
			'currency',
			'customer_group',
			'geo_zone'
		);
		
		$stores = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY name");
		$this->data['stores'] = $stores->rows;
		array_unshift($this->data['stores'], array('store_id' => 0, 'name' => $this->config->get('config_name')));
		
		$currencies = $this->db->query("(SELECT * FROM " . DB_PREFIX . "currency WHERE status = '1' AND `code` = '" . $this->config->get('config_currency') . "') UNION (SELECT * FROM " . DB_PREFIX . "currency WHERE status = '1' AND `code` != '" . $this->config->get('config_currency') . "')");
		$this->data['currencys'] = array(array('currency_id' => 'autoconvert', 'name' => $this->data['text_convert_unselected']));
		foreach ($currencies->rows as $currency) {
			$this->data['currencys'][] = array('currency_id' => $currency['code'], 'name' => $currency['title']);
		}
		
		$this->load->model('sale/customer_group');
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		array_unshift($this->data['customer_groups'], array('customer_group_id' => 0, 'name' => $this->data['text_not_logged_in']));
		
		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		array_unshift($this->data['geo_zones'], array('geo_zone_id' => 0, 'name' => $this->data['text_all_other_zones']));
		// end
		
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
}
?>