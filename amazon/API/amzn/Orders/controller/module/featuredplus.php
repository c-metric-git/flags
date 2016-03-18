<?php
class ControllerModuleFeaturedplus extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/featuredplus');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		// featured plus mod start //
		$this->document->addScript('view/javascript/jquery/colorpicker/colorpicker.js');
		$this->document->addStyle('view/stylesheet/colorpicker/css/colorpicker.css');
		$this->document->addStyle('view/stylesheet/prodview.css');	
		// featured plus mod end //
		
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			$this->model_setting_setting->editSetting('featuredplus', $this->request->post);
			
		$featuredplus_settings = array(
		//background color start
		'fstyle1_status'    			    => $this->request->post['fstyle1_status'],
		'fstyle1_prodnama'    			=> $this->request->post['fstyle1_prodnama'],
		'fstyle1_pricecol'    			=> $this->request->post['fstyle1_pricecol'],
		'fstyle1_oldpricecol'    		=> $this->request->post['fstyle1_oldpricecol'],
		'fstyle1_shortdescol'    		=> $this->request->post['fstyle1_shortdescol'],
		'fstyle1_bgroundcol'    			=> $this->request->post['fstyle1_bgroundcol'],
		'fstyle1_bordercol'    			=> $this->request->post['fstyle1_bordercol'],
		'fstyle1_boxprodwidth'    	    => $this->request->post['fstyle1_boxprodwidth'],
		'fstyle1_margin1'    			=> $this->request->post['fstyle1_margin1'],
		'fstyle1_margin2'    			=> $this->request->post['fstyle1_margin2'],
		'fstyle1_margin3'    			=> $this->request->post['fstyle1_margin3'],
		'fstyle2_status'    			    => $this->request->post['fstyle2_status'],
		'fstyle2_prodnama'    			=> $this->request->post['fstyle2_prodnama'],
		'fstyle2_pricecol'    			=> $this->request->post['fstyle2_pricecol'],
		'fstyle2_oldpricecol'    		=> $this->request->post['fstyle2_oldpricecol'],
		'fstyle2_shortdescol'    		=> $this->request->post['fstyle2_shortdescol'],
		'fstyle2_bgroundcol'    			=> $this->request->post['fstyle2_bgroundcol'],
		'fstyle2_bordercol'    			=> $this->request->post['fstyle2_bordercol'],
		'fstyle2_boxprodwidth'    	    => $this->request->post['fstyle2_boxprodwidth'],
		'fstyle2_margin1'    			=> $this->request->post['fstyle2_margin1'],
		'fstyle2_margin2'    			=> $this->request->post['fstyle2_margin2'],
		'fstyle2_margin3'    			=> $this->request->post['fstyle2_margin3'],
		'fstyle3_status'    			    => $this->request->post['fstyle3_status'],
		'fstyle3_prodnama'    			=> $this->request->post['fstyle3_prodnama'],
		'fstyle3_pricecol'    			=> $this->request->post['fstyle3_pricecol'],
		'fstyle3_oldpricecol'    		=> $this->request->post['fstyle3_oldpricecol'],
		'fstyle3_shortdescol'    		=> $this->request->post['fstyle3_shortdescol'],
		'fstyle3_bgroundcol'    			=> $this->request->post['fstyle3_bgroundcol'],
		'fstyle3_bordercol'    			=> $this->request->post['fstyle3_bordercol'],
		'fstyle3_boxprodwidth'    	    => $this->request->post['fstyle3_boxprodwidth'],
		'fstyle3_margin1'    			=> $this->request->post['fstyle3_margin1'],
		'fstyle3_margin2'    			=> $this->request->post['fstyle3_margin2'],
		'fstyle3_margin3'    			=> $this->request->post['fstyle3_margin3'],
		'fstyle4_status'    			    => $this->request->post['fstyle4_status'],
		'fstyle4_prodnama'    			=> $this->request->post['fstyle4_prodnama'],
		'fstyle4_pricecol'    			=> $this->request->post['fstyle4_pricecol'],
		'fstyle4_oldpricecol'    		=> $this->request->post['fstyle4_oldpricecol'],
		'fstyle4_bgroundcol'    			=> $this->request->post['fstyle4_bgroundcol'],
		'fstyle4_bordercol'    			=> $this->request->post['fstyle4_bordercol'],
		'fstyle4_boxprodwidth'    	    => $this->request->post['fstyle4_boxprodwidth'],
		'fstyle4_margin1'    			=> $this->request->post['fstyle4_margin1'],
		'fstyle4_margin2'    			=> $this->request->post['fstyle4_margin2'],
		'fstyle4_margin3'    			=> $this->request->post['fstyle4_margin3'],
		'fstyle5_status'    			    => $this->request->post['fstyle5_status'],
		'fstyle5_prodnama'    			=> $this->request->post['fstyle5_prodnama'],
		'fstyle5_pricecol'    			=> $this->request->post['fstyle5_pricecol'],
		'fstyle5_oldpricecol'    		=> $this->request->post['fstyle5_oldpricecol'],
		'fstyle5_bgroundcol'    			=> $this->request->post['fstyle5_bgroundcol'],
		'fstyle5_bordercol'    			=> $this->request->post['fstyle5_bordercol'],
		'fstyle5_boxprodwidth'    	    => $this->request->post['fstyle5_boxprodwidth'],
		'fstyle5_margin1'    			=> $this->request->post['fstyle5_margin1'],
		'fstyle5_margin2'    			=> $this->request->post['fstyle5_margin2'],
		'fstyle5_margin3'    			=> $this->request->post['fstyle5_margin3']
		);					
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		
		// featured plus mod start //
		$this->data['tab_basicset'] = $this->language->get('tab_basicset');
		$this->data['tab_advanceset'] = $this->language->get('tab_advanceset');
		$this->data['text_pleaseselect'] = $this->language->get('text_pleaseselect');	
		$this->data['text_style1'] = $this->language->get('text_style1');
		$this->data['text_style2'] = $this->language->get('text_style2');
		$this->data['text_style3'] = $this->language->get('text_style3');
		$this->data['text_style4'] = $this->language->get('text_style4');
		$this->data['text_style5'] = $this->language->get('text_style5');
		$this->data['entry_styleview'] = $this->language->get('entry_styleview');	
		
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_prodnama'] = $this->language->get('entry_prodnama');
		$this->data['entry_pricecol'] = $this->language->get('entry_pricecol');
		$this->data['entry_oldpricecol'] = $this->language->get('entry_oldpricecol');
		$this->data['entry_shortdescol'] = $this->language->get('entry_shortdescol');
		$this->data['entry_bgroundcol'] = $this->language->get('entry_bgroundcol');
		$this->data['entry_bordercol'] = $this->language->get('entry_bordercol');
		$this->data['entry_boxprodwidth'] = $this->language->get('entry_boxprodwidth');
		$this->data['entry_margin1'] = $this->language->get('entry_margin1');
		$this->data['entry_margin2'] = $this->language->get('entry_margin2');
		$this->data['entry_margin3'] = $this->language->get('entry_margin3');
		// featured plus mod end //
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
				
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
			'href'      => $this->url->link('module/featuredplus', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/featuredplus', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->post['featuredplus_product'])) {
			$this->data['featuredplus_product'] = $this->request->post['featuredplus_product'];
		} else {
			$this->data['featuredplus_product'] = $this->config->get('featuredplus_product');
		}	
				
		$this->load->model('catalog/product');
				
		if (isset($this->request->post['featuredplus_product'])) {
			$products = explode(',', $this->request->post['featuredplus_product']);
		} else {		
			$products = explode(',', $this->config->get('featuredplus_product'));
		}
		
		if (isset($this->request->post['fstyle1_status'])) {
			$this->data['fstyle1_status'] = $this->request->post['fstyle1_status'];
		} else {
			$this->data['fstyle1_status'] = $this->config->get('fstyle1_status');
		}
		
		if (isset($this->request->post['fstyle1_prodnama'])) {
			$this->data['fstyle1_prodnama'] = $this->request->post['fstyle1_prodnama'];
		} else {
			$this->data['fstyle1_prodnama'] = $this->config->get('fstyle1_prodnama');
		}
		
		if (isset($this->request->post['fstyle1_pricecol'])) {
			$this->data['fstyle1_pricecol'] = $this->request->post['fstyle1_pricecol'];
		} else {
			$this->data['fstyle1_pricecol'] = $this->config->get('fstyle1_pricecol');
		}
		
		if (isset($this->request->post['fstyle1_oldpricecol'])) {
			$this->data['fstyle1_oldpricecol'] = $this->request->post['fstyle1_oldpricecol'];
		} else {
			$this->data['fstyle1_oldpricecol'] = $this->config->get('fstyle1_oldpricecol');
		}
		
		if (isset($this->request->post['fstyle1_shortdescol'])) {
			$this->data['fstyle1_shortdescol'] = $this->request->post['fstyle1_shortdescol'];
		} else {
			$this->data['fstyle1_shortdescol'] = $this->config->get('fstyle1_shortdescol');
		}
		
		if (isset($this->request->post['fstyle1_bgroundcol'])) {
			$this->data['fstyle1_bgroundcol'] = $this->request->post['fstyle1_bgroundcol'];
		} else {
			$this->data['fstyle1_bgroundcol'] = $this->config->get('fstyle1_bgroundcol');
		}
		
		if (isset($this->request->post['fstyle1_bordercol'])) {
			$this->data['fstyle1_bordercol'] = $this->request->post['fstyle1_bordercol'];
		} else {
			$this->data['fstyle1_bordercol'] = $this->config->get('fstyle1_bordercol');
		}
		
		if (isset($this->request->post['fstyle1_boxprodwidth'])) {
			$this->data['fstyle1_boxprodwidth'] = $this->request->post['fstyle1_boxprodwidth'];
		} else {
			$this->data['fstyle1_boxprodwidth'] = $this->config->get('fstyle1_boxprodwidth');
		}
		
		if (isset($this->request->post['fstyle1_margin1'])) {
			$this->data['fstyle1_margin1'] = $this->request->post['fstyle1_margin1'];
		} else {
			$this->data['fstyle1_margin1'] = $this->config->get('fstyle1_margin1');
		}
		
		if (isset($this->request->post['fstyle1_margin2'])) {
			$this->data['fstyle1_margin2'] = $this->request->post['fstyle1_margin2'];
		} else {
			$this->data['fstyle1_margin2'] = $this->config->get('fstyle1_margin2');
		}
		
		if (isset($this->request->post['fstyle1_margin3'])) {
			$this->data['fstyle1_margin3'] = $this->request->post['fstyle1_margin3'];
		} else {
			$this->data['fstyle1_margin3'] = $this->config->get('fstyle1_margin3');
		}
		
		if (isset($this->request->post['fstyle2_status'])) {
			$this->data['fstyle2_status'] = $this->request->post['fstyle2_status'];
		} else {
			$this->data['fstyle2_status'] = $this->config->get('fstyle2_status');
		}
		
		if (isset($this->request->post['fstyle2_prodnama'])) {
			$this->data['fstyle2_prodnama'] = $this->request->post['fstyle2_prodnama'];
		} else {
			$this->data['fstyle2_prodnama'] = $this->config->get('fstyle2_prodnama');
		}
		
		if (isset($this->request->post['fstyle2_pricecol'])) {
			$this->data['fstyle2_pricecol'] = $this->request->post['fstyle2_pricecol'];
		} else {
			$this->data['fstyle2_pricecol'] = $this->config->get('fstyle2_pricecol');
		}
		
		if (isset($this->request->post['fstyle2_oldpricecol'])) {
			$this->data['fstyle2_oldpricecol'] = $this->request->post['fstyle2_oldpricecol'];
		} else {
			$this->data['fstyle2_oldpricecol'] = $this->config->get('fstyle2_oldpricecol');
		}
		
		if (isset($this->request->post['fstyle2_shortdescol'])) {
			$this->data['fstyle2_shortdescol'] = $this->request->post['fstyle2_shortdescol'];
		} else {
			$this->data['fstyle2_shortdescol'] = $this->config->get('fstyle2_shortdescol');
		}
		
		if (isset($this->request->post['fstyle2_bgroundcol'])) {
			$this->data['fstyle2_bgroundcol'] = $this->request->post['fstyle2_bgroundcol'];
		} else {
			$this->data['fstyle2_bgroundcol'] = $this->config->get('fstyle2_bgroundcol');
		}
		
		if (isset($this->request->post['fstyle2_bordercol'])) {
			$this->data['fstyle2_bordercol'] = $this->request->post['fstyle2_bordercol'];
		} else {
			$this->data['fstyle2_bordercol'] = $this->config->get('fstyle2_bordercol');
		}
		
		if (isset($this->request->post['fstyle2_boxprodwidth'])) {
			$this->data['fstyle2_boxprodwidth'] = $this->request->post['fstyle2_boxprodwidth'];
		} else {
			$this->data['fstyle2_boxprodwidth'] = $this->config->get('fstyle2_boxprodwidth');
		}
		
		if (isset($this->request->post['fstyle2_margin1'])) {
			$this->data['fstyle2_margin1'] = $this->request->post['fstyle2_margin1'];
		} else {
			$this->data['fstyle2_margin1'] = $this->config->get('fstyle2_margin1');
		}
		
		if (isset($this->request->post['fstyle2_margin2'])) {
			$this->data['fstyle2_margin2'] = $this->request->post['fstyle2_margin2'];
		} else {
			$this->data['fstyle2_margin2'] = $this->config->get('fstyle2_margin2');
		}
		
		if (isset($this->request->post['fstyle2_margin3'])) {
			$this->data['fstyle2_margin3'] = $this->request->post['fstyle2_margin3'];
		} else {
			$this->data['fstyle2_margin3'] = $this->config->get('fstyle2_margin3');
		}
		
		if (isset($this->request->post['fstyle3_status'])) {
			$this->data['fstyle3_status'] = $this->request->post['fstyle3_status'];
		} else {
			$this->data['fstyle3_status'] = $this->config->get('fstyle3_status');
		}
		
		if (isset($this->request->post['fstyle3_prodnama'])) {
			$this->data['fstyle3_prodnama'] = $this->request->post['fstyle3_prodnama'];
		} else {
			$this->data['fstyle3_prodnama'] = $this->config->get('fstyle3_prodnama');
		}
		
		if (isset($this->request->post['fstyle3_pricecol'])) {
			$this->data['fstyle3_pricecol'] = $this->request->post['fstyle3_pricecol'];
		} else {
			$this->data['fstyle3_pricecol'] = $this->config->get('fstyle3_pricecol');
		}
		
		if (isset($this->request->post['fstyle3_oldpricecol'])) {
			$this->data['fstyle3_oldpricecol'] = $this->request->post['fstyle3_oldpricecol'];
		} else {
			$this->data['fstyle3_oldpricecol'] = $this->config->get('fstyle3_oldpricecol');
		}
		
		if (isset($this->request->post['fstyle3_shortdescol'])) {
			$this->data['fstyle3_shortdescol'] = $this->request->post['fstyle3_shortdescol'];
		} else {
			$this->data['fstyle3_shortdescol'] = $this->config->get('fstyle3_shortdescol');
		}
		
		if (isset($this->request->post['fstyle3_bgroundcol'])) {
			$this->data['fstyle3_bgroundcol'] = $this->request->post['fstyle3_bgroundcol'];
		} else {
			$this->data['fstyle3_bgroundcol'] = $this->config->get('fstyle3_bgroundcol');
		}
		
		if (isset($this->request->post['fstyle3_bordercol'])) {
			$this->data['fstyle3_bordercol'] = $this->request->post['fstyle3_bordercol'];
		} else {
			$this->data['fstyle3_bordercol'] = $this->config->get('fstyle3_bordercol');
		}
		
		if (isset($this->request->post['fstyle3_boxprodwidth'])) {
			$this->data['fstyle3_boxprodwidth'] = $this->request->post['fstyle3_boxprodwidth'];
		} else {
			$this->data['fstyle3_boxprodwidth'] = $this->config->get('fstyle3_boxprodwidth');
		}
		
		if (isset($this->request->post['fstyle3_margin1'])) {
			$this->data['fstyle3_margin1'] = $this->request->post['fstyle3_margin1'];
		} else {
			$this->data['fstyle3_margin1'] = $this->config->get('fstyle3_margin1');
		}
		
		if (isset($this->request->post['fstyle3_margin2'])) {
			$this->data['fstyle3_margin2'] = $this->request->post['fstyle3_margin2'];
		} else {
			$this->data['fstyle3_margin2'] = $this->config->get('fstyle3_margin2');
		}
		
		if (isset($this->request->post['fstyle3_margin3'])) {
			$this->data['fstyle3_margin3'] = $this->request->post['fstyle3_margin3'];
		} else {
			$this->data['fstyle3_margin3'] = $this->config->get('fstyle3_margin3');
		}
		
		if (isset($this->request->post['fstyle4_status'])) {
			$this->data['fstyle4_status'] = $this->request->post['fstyle4_status'];
		} else {
			$this->data['fstyle4_status'] = $this->config->get('fstyle4_status');
		}
		
		if (isset($this->request->post['fstyle4_prodnama'])) {
			$this->data['fstyle4_prodnama'] = $this->request->post['fstyle4_prodnama'];
		} else {
			$this->data['fstyle4_prodnama'] = $this->config->get('fstyle4_prodnama');
		}
		
		if (isset($this->request->post['fstyle4_pricecol'])) {
			$this->data['fstyle4_pricecol'] = $this->request->post['fstyle4_pricecol'];
		} else {
			$this->data['fstyle4_pricecol'] = $this->config->get('fstyle4_pricecol');
		}
		
		if (isset($this->request->post['fstyle4_oldpricecol'])) {
			$this->data['fstyle4_oldpricecol'] = $this->request->post['fstyle4_oldpricecol'];
		} else {
			$this->data['fstyle4_oldpricecol'] = $this->config->get('fstyle4_oldpricecol');
		}
		
		if (isset($this->request->post['fstyle4_bgroundcol'])) {
			$this->data['fstyle4_bgroundcol'] = $this->request->post['fstyle4_bgroundcol'];
		} else {
			$this->data['fstyle4_bgroundcol'] = $this->config->get('fstyle4_bgroundcol');
		}
		
		if (isset($this->request->post['fstyle4_bordercol'])) {
			$this->data['fstyle4_bordercol'] = $this->request->post['fstyle4_bordercol'];
		} else {
			$this->data['fstyle4_bordercol'] = $this->config->get('fstyle4_bordercol');
		}
		
		if (isset($this->request->post['fstyle4_boxprodwidth'])) {
			$this->data['fstyle4_boxprodwidth'] = $this->request->post['fstyle4_boxprodwidth'];
		} else {
			$this->data['fstyle4_boxprodwidth'] = $this->config->get('fstyle4_boxprodwidth');
		}
		
		if (isset($this->request->post['fstyle4_margin1'])) {
			$this->data['fstyle4_margin1'] = $this->request->post['fstyle4_margin1'];
		} else {
			$this->data['fstyle4_margin1'] = $this->config->get('fstyle4_margin1');
		}
		
		if (isset($this->request->post['fstyle4_margin2'])) {
			$this->data['fstyle4_margin2'] = $this->request->post['fstyle4_margin2'];
		} else {
			$this->data['fstyle4_margin2'] = $this->config->get('fstyle4_margin2');
		}
		
		if (isset($this->request->post['fstyle4_margin3'])) {
			$this->data['fstyle4_margin3'] = $this->request->post['fstyle4_margin3'];
		} else {
			$this->data['fstyle4_margin3'] = $this->config->get('fstyle4_margin3');
		}
		
		if (isset($this->request->post['fstyle5_status'])) {
			$this->data['fstyle5_status'] = $this->request->post['fstyle5_status'];
		} else {
			$this->data['fstyle5_status'] = $this->config->get('fstyle5_status');
		}
		
		if (isset($this->request->post['fstyle5_prodnama'])) {
			$this->data['fstyle5_prodnama'] = $this->request->post['fstyle5_prodnama'];
		} else {
			$this->data['fstyle5_prodnama'] = $this->config->get('fstyle5_prodnama');
		}
		
		if (isset($this->request->post['fstyle5_pricecol'])) {
			$this->data['fstyle5_pricecol'] = $this->request->post['fstyle5_pricecol'];
		} else {
			$this->data['fstyle5_pricecol'] = $this->config->get('fstyle5_pricecol');
		}
		
		if (isset($this->request->post['fstyle5_oldpricecol'])) {
			$this->data['fstyle5_oldpricecol'] = $this->request->post['fstyle5_oldpricecol'];
		} else {
			$this->data['fstyle5_oldpricecol'] = $this->config->get('fstyle5_oldpricecol');
		}
		
		if (isset($this->request->post['fstyle5_bgroundcol'])) {
			$this->data['fstyle5_bgroundcol'] = $this->request->post['fstyle5_bgroundcol'];
		} else {
			$this->data['fstyle5_bgroundcol'] = $this->config->get('fstyle5_bgroundcol');
		}
		
		if (isset($this->request->post['fstyle5_bordercol'])) {
			$this->data['fstyle5_bordercol'] = $this->request->post['fstyle5_bordercol'];
		} else {
			$this->data['fstyle5_bordercol'] = $this->config->get('fstyle5_bordercol');
		}
		
		if (isset($this->request->post['fstyle5_boxprodwidth'])) {
			$this->data['fstyle5_boxprodwidth'] = $this->request->post['fstyle5_boxprodwidth'];
		} else {
			$this->data['fstyle5_boxprodwidth'] = $this->config->get('fstyle5_boxprodwidth');
		}
		
		if (isset($this->request->post['fstyle5_margin1'])) {
			$this->data['fstyle5_margin1'] = $this->request->post['fstyle5_margin1'];
		} else {
			$this->data['fstyle5_margin1'] = $this->config->get('fstyle5_margin1');
		}
		
		if (isset($this->request->post['fstyle5_margin2'])) {
			$this->data['fstyle5_margin2'] = $this->request->post['fstyle5_margin2'];
		} else {
			$this->data['fstyle5_margin2'] = $this->config->get('fstyle5_margin2');
		}
		
		if (isset($this->request->post['fstyle5_margin3'])) {
			$this->data['fstyle5_margin3'] = $this->request->post['fstyle5_margin3'];
		} else {
			$this->data['fstyle5_margin3'] = $this->config->get('fstyle5_margin3');
		}
		
		$this->data['products'] = array();
		
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				$this->data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}	
			
		$this->data['modules'] = array();
		
		if (isset($this->request->post['featuredplus_module'])) {
			$this->data['modules'] = $this->request->post['featuredplus_module'];
		} elseif ($this->config->get('featuredplus_module')) { 
			$this->data['modules'] = $this->config->get('featuredplus_module');
		}		
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/featuredplus.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/featuredplus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['featuredplus_module'])) {
			foreach ($this->request->post['featuredplus_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>