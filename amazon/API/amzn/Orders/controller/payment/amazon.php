<?php 
class ControllerPaymentamazon extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/amazon');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('amazon', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_aws_key_id'] = $this->language->get('entry_aws_key_id');
		$this->data['entry_secret_key'] = $this->language->get('entry_secret_key');
		$this->data['entry_service_url'] = $this->language->get('entry_service_url');
		$this->data['entry_merchant_id'] = $this->language->get('entry_merchant_id');		
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('payment/amazon', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('payment/amazon', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['amazon_order_status_id'])) {
			$this->data['amazon_order_status_id'] = $this->request->post['amazon_order_status_id'];
		} else {
			$this->data['amazon_order_status_id'] = $this->config->get('amazon_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['amazon_geo_zone_id'])) {
			$this->data['amazon_geo_zone_id'] = $this->request->post['amazon_geo_zone_id'];
		} else {
			$this->data['amazon_geo_zone_id'] = $this->config->get('amazon_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');						
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['amazon_status'])) {
			$this->data['amazon_status'] = $this->request->post['amazon_status'];
		} else {
			$this->data['amazon_status'] = $this->config->get('amazon_status');
		}
		
		if (isset($this->request->post['amazon_sort_order'])) {
			$this->data['amazon_sort_order'] = $this->request->post['amazon_sort_order'];
		} else {
			$this->data['amazon_sort_order'] = $this->config->get('amazon_sort_order');
		}
		
		if (isset($this->request->post['amazon_aws_key_id'])) {
			$this->data['amazon_aws_key_id'] = $this->request->post['amazon_aws_key_id'];
		} else {
			$this->data['amazon_aws_key_id'] = $this->config->get('amazon_aws_key_id');
		}
		
		if (isset($this->request->post['amazon_secret_key'])) {
			$this->data['amazon_secret_key'] = $this->request->post['amazon_secret_key'];
		} else {
			$this->data['amazon_secret_key'] = $this->config->get('amazon_secret_key');
		}
		
		if (isset($this->request->post['amazon_service_url'])) {
			$this->data['amazon_service_url'] = $this->request->post['amazon_service_url'];
		} else {
			$this->data['amazon_service_url'] = $this->config->get('amazon_service_url');
		}
		
	
		
		if (isset($this->request->post['amazon_merchant_id'])) {
			$this->data['amazon_merchant_id'] = $this->request->post['amazon_merchant_id'];
		} else {
			$this->data['amazon_merchant_id'] = $this->config->get('amazon_merchant_id');
		}	
		
		$this->template = 'payment/amazon.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/amazon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>