<?php 
class ControllerCatalogSeoPack extends Controller { 
	private $error = array();
 
	public function index() {
	
		$this->load->language('catalog/seopack');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('seopack', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
		}
	
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
	
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['action'] = $this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['clearmetas'] = $this->url->link('catalog/seopack/clearmetas', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['clearkeywords'] = $this->url->link('catalog/seopack/clearkeywords', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cleartags'] = $this->url->link('catalog/seopack/cleartags', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['clearproducts'] = $this->url->link('catalog/seopack/clearproducts', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['clearurls'] = $this->url->link('catalog/seopack/clearurls', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['friendlyurls'] = '';
		$this->data['seourls'] = '';
		
		
		$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product_description WHERE field = 'custom_title'");

		$exists = 0;
		foreach ($query->rows as $index) {$exists++;}

		if (!$exists) {$this->db->query("ALTER TABLE " . DB_PREFIX . "product_description ADD COLUMN `custom_title` varchar(255) NULL DEFAULT '';");}
		
		$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "category_description WHERE field = 'custom_title'");

		$exists = 0;
		foreach ($query->rows as $index) {$exists++;}

		if (!$exists) {$this->db->query("ALTER TABLE " . DB_PREFIX . "category_description ADD COLUMN `custom_title` varchar(255) NULL DEFAULT '';");}
		
		$this->data['parameters'] = array();
		
		if (isset($this->request->post['parameters'])) {
			$this->data['parameters'] = $this->request->post['parameters'];
		} elseif ($this->config->get('parameters')) { 
			$this->data['parameters'] = $this->config->get('parameters');
		}
		$initial_parameters = array('parameters'=>array('keywords'=>'%p%c','metas'=>'%p - %f','tags'=>'%p%c','related'=>'5', 'ext'=>'.html'));
		if (!$this->data['parameters']) 
			{
			$this->model_setting_setting->editSetting('seopack', $initial_parameters);		
			$this->data['parameters']  = $initial_parameters['parameters'];			
			}
		
				
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
	
	$this->template = 'catalog/seopack.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
	$this->response->setOutput($this->render());
	
		 
	}
	
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'catalog/seopack')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function clearmetas() {
	
		$query = $this->db->query("update " . DB_PREFIX . "product_description set meta_description = '';");
		
		$this->session->data['success'] = "Meta descriptions were deleted.";
		
		$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
	
	}
	public function clearkeywords() {
	
		$query = $this->db->query("update " . DB_PREFIX . "product_description set meta_keyword = '';");
		
		$this->session->data['success'] = "Meta keywords were deleted.";
		
		$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
	
	}
	public function cleartags() {
	
		$query = $this->db->query("delete from " . DB_PREFIX . "product_tag;");
		
		$this->session->data['success'] = "Product tags were deleted.";
		
		$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
	
	}
	public function clearproducts() {
	
		$query = $this->db->query("delete from " . DB_PREFIX . "product_related;");
		
		$this->session->data['success'] = "Related products were deleted.";
		
		$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
	
	}
	public function clearurls() {
	
		$query = $this->db->query("delete from " . DB_PREFIX . "url_alias;");
		
		$this->session->data['success'] = "SEO URLs were deleted.";
		
		$this->redirect($this->url->link('catalog/seopack', 'token=' . $this->session->data['token'], 'SSL'));
	
	}
	

	
}
?>