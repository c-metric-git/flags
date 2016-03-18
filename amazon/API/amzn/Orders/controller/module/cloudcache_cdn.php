<?php
####################################################################################################
#  Cloudcache CDN Integration for Opencart 1.5.1.x from HostJars http://opencart.hostjars.com      #
####################################################################################################
class ControllerModuleCloudcacheCdn extends Controller {
	
	private $error = array(); 
	public function index() {
		$this->load->language('module/cloudcache_cdn');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cloudcache_cdn', $this->request->post);		
					 
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$text_strings = array(
			'heading_title',
			'button_save',
			'button_cancel',
			'text_enabled',
			'text_disabled',
			'entry_cdn_status',
			'entry_cdn_domain',
			'entry_cdn_images',
			'entry_cdn_js',
			'entry_cdn_css'
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		$config_data = array(
			'cdn_status',
			'cdn_domain',
			'cdn_images',
			'cdn_js',
			'cdn_css'
		);
		
		foreach ($config_data as $conf) {
			$this->data[$conf] = (isset($this->request->post[$conf])) ? $this->request->post[$conf] : $this->config->get($conf);
		}		
	
 		$this->data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/cloudcache_cdn', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/cloudcache_cdn', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

	
		$this->template = 'module/cloudcache_cdn.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/cloudcache_cdn')) {
			$this->error['warning'] = $this->language->get('error_permission');
		} elseif (isset($this->request->post['cdn_domain'])) {
			if (!preg_match('/netdna-cdn.com$/is', $this->request->post['cdn_domain'])) {
				$result = dns_get_record($this->request->post['cdn_domain']);
				$last = array_pop($result);
				if (!preg_match('/netdna-cdn.com$/is', $last['host']) && !preg_match('/netdna-ssl.com$/is', $last['host'])) {
					$this->error['warning'] = 'You have entered an invalid domain. Please check your CloudCache account!';
				}
			}
		}
		
		return (!$this->error);
	}


}
?>