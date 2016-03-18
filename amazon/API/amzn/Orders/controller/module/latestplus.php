<?php
class ControllerModuleLatestplus extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/latestplus');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		// latest plus mod start //
		$this->document->addScript('view/javascript/jquery/colorpicker/colorpicker.js');
		$this->document->addStyle('view/stylesheet/colorpicker/css/colorpicker.css');
		$this->document->addStyle('view/stylesheet/prodview.css');	
		// latest plus mod end //
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('latestplus', $this->request->post);	
		
		// latest plus mod start //	
        $latestplus_settings = array(
		'lstyle1_status'    			=> $this->request->post['lstyle1_status'],
		'lstyle1_prodnama'    			=> $this->request->post['lstyle1_prodnama'],
		'lstyle1_pricecol'    			=> $this->request->post['lstyle1_pricecol'],
		'lstyle1_oldpricecol'    		=> $this->request->post['lstyle1_oldpricecol'],
		'lstyle1_shortdescol'    		=> $this->request->post['lstyle1_shortdescol'],
		'lstyle1_bgroundcol'    		=> $this->request->post['lstyle1_bgroundcol'],
		'lstyle1_bordercol'    			=> $this->request->post['lstyle1_bordercol'],
		'lstyle1_boxprodwidth'    	    => $this->request->post['lstyle1_boxprodwidth'],
		'lstyle1_margin1'    			=> $this->request->post['lstyle1_margin1'],
		'lstyle1_margin2'    			=> $this->request->post['lstyle1_margin2'],
		'lstyle1_margin3'    			=> $this->request->post['lstyle1_margin3'],
		'lstyle2_status'    			=> $this->request->post['lstyle2_status'],
		'lstyle2_prodnama'    			=> $this->request->post['lstyle2_prodnama'],
		'lstyle2_pricecol'    			=> $this->request->post['lstyle2_pricecol'],
		'lstyle2_oldpricecol'    		=> $this->request->post['lstyle2_oldpricecol'],
		'lstyle2_shortdescol'    		=> $this->request->post['lstyle2_shortdescol'],
		'lstyle2_bgroundcol'    		=> $this->request->post['lstyle2_bgroundcol'],
		'lstyle2_bordercol'    			=> $this->request->post['lstyle2_bordercol'],
		'lstyle2_boxprodwidth'    	    => $this->request->post['lstyle2_boxprodwidth'],
		'lstyle2_margin1'    			=> $this->request->post['lstyle2_margin1'],
		'lstyle2_margin2'    			=> $this->request->post['lstyle2_margin2'],
		'lstyle2_margin3'    			=> $this->request->post['lstyle2_margin3'],
		'lstyle3_status'    			=> $this->request->post['lstyle3_status'],
		'lstyle3_prodnama'    			=> $this->request->post['lstyle3_prodnama'],
		'lstyle3_pricecol'    			=> $this->request->post['lstyle3_pricecol'],
		'lstyle3_oldpricecol'    		=> $this->request->post['lstyle3_oldpricecol'],
		'lstyle3_shortdescol'    		=> $this->request->post['lstyle3_shortdescol'],
		'lstyle3_bgroundcol'    		=> $this->request->post['lstyle3_bgroundcol'],
		'lstyle3_bordercol'    			=> $this->request->post['lstyle3_bordercol'],
		'lstyle3_boxprodwidth'    	    => $this->request->post['lstyle3_boxprodwidth'],
		'lstyle3_margin1'    			=> $this->request->post['lstyle3_margin1'],
		'lstyle3_margin2'    			=> $this->request->post['lstyle3_margin2'],
		'lstyle3_margin3'    			=> $this->request->post['lstyle3_margin3'],
		'lstyle4_status'    			=> $this->request->post['lstyle4_status'],
		'lstyle4_prodnama'    			=> $this->request->post['lstyle4_prodnama'],
		'lstyle4_pricecol'    			=> $this->request->post['lstyle4_pricecol'],
		'lstyle4_oldpricecol'    		=> $this->request->post['lstyle4_oldpricecol'],
		'lstyle4_bgroundcol'    		=> $this->request->post['lstyle4_bgroundcol'],
		'lstyle4_bordercol'    			=> $this->request->post['lstyle4_bordercol'],
		'lstyle4_boxprodwidth'    	    => $this->request->post['lstyle4_boxprodwidth'],
		'lstyle4_margin1'    			=> $this->request->post['lstyle4_margin1'],
		'lstyle4_margin2'    			=> $this->request->post['lstyle4_margin2'],
		'lstyle4_margin3'    			=> $this->request->post['lstyle4_margin3'],
		'lstyle5_status'    			=> $this->request->post['lstyle5_status'],
		'lstyle5_prodnama'    			=> $this->request->post['lstyle5_prodnama'],
		'lstyle5_pricecol'    			=> $this->request->post['lstyle5_pricecol'],
		'lstyle5_oldpricecol'    		=> $this->request->post['lstyle5_oldpricecol'],
		'lstyle5_bgroundcol'    		=> $this->request->post['lstyle5_bgroundcol'],
		'lstyle5_bordercol'    			=> $this->request->post['lstyle5_bordercol'],
		'lstyle5_boxprodwidth'    	    => $this->request->post['lstyle5_boxprodwidth'],
		'lstyle5_margin1'    			=> $this->request->post['lstyle5_margin1'],
		'lstyle5_margin2'    			=> $this->request->post['lstyle5_margin2'],
		'lstyle5_margin3'    			=> $this->request->post['lstyle5_margin3']
		);	
		// latest plus mod end //			
			
			$this->cache->delete('product');
			
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
		
		// latest plus mod start //
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
		// latest plus mod end //
		
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
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
			'href'      => $this->url->link('module/latestplus', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/latestplus', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();
		
		if (isset($this->request->post['latestplus_module'])) {
			$this->data['modules'] = $this->request->post['latestplus_module'];
		} elseif ($this->config->get('latestplus_module')) { 
			$this->data['modules'] = $this->config->get('latestplus_module');
		}		
		
		// latest plus mod start //
		if (isset($this->request->post['lstyle1_status'])) {
			$this->data['lstyle1_status'] = $this->request->post['lstyle1_status'];
		} else {
			$this->data['lstyle1_status'] = $this->config->get('lstyle1_status');
		}
		
		if (isset($this->request->post['lstyle1_prodnama'])) {
			$this->data['lstyle1_prodnama'] = $this->request->post['lstyle1_prodnama'];
		} else {
			$this->data['lstyle1_prodnama'] = $this->config->get('lstyle1_prodnama');
		}
		
		if (isset($this->request->post['lstyle1_pricecol'])) {
			$this->data['lstyle1_pricecol'] = $this->request->post['lstyle1_pricecol'];
		} else {
			$this->data['lstyle1_pricecol'] = $this->config->get('lstyle1_pricecol');
		}
		
		if (isset($this->request->post['lstyle1_oldpricecol'])) {
			$this->data['lstyle1_oldpricecol'] = $this->request->post['lstyle1_oldpricecol'];
		} else {
			$this->data['lstyle1_oldpricecol'] = $this->config->get('lstyle1_oldpricecol');
		}
		
		if (isset($this->request->post['lstyle1_shortdescol'])) {
			$this->data['lstyle1_shortdescol'] = $this->request->post['lstyle1_shortdescol'];
		} else {
			$this->data['lstyle1_shortdescol'] = $this->config->get('lstyle1_shortdescol');
		}
		
		if (isset($this->request->post['lstyle1_bgroundcol'])) {
			$this->data['lstyle1_bgroundcol'] = $this->request->post['lstyle1_bgroundcol'];
		} else {
			$this->data['lstyle1_bgroundcol'] = $this->config->get('lstyle1_bgroundcol');
		}
		
		if (isset($this->request->post['lstyle1_bordercol'])) {
			$this->data['lstyle1_bordercol'] = $this->request->post['lstyle1_bordercol'];
		} else {
			$this->data['lstyle1_bordercol'] = $this->config->get('lstyle1_bordercol');
		}
		
		if (isset($this->request->post['lstyle1_boxprodwidth'])) {
			$this->data['lstyle1_boxprodwidth'] = $this->request->post['lstyle1_boxprodwidth'];
		} else {
			$this->data['lstyle1_boxprodwidth'] = $this->config->get('lstyle1_boxprodwidth');
		}
		
		if (isset($this->request->post['lstyle1_margin1'])) {
			$this->data['lstyle1_margin1'] = $this->request->post['lstyle1_margin1'];
		} else {
			$this->data['lstyle1_margin1'] = $this->config->get('lstyle1_margin1');
		}
		
		if (isset($this->request->post['lstyle1_margin2'])) {
			$this->data['lstyle1_margin2'] = $this->request->post['lstyle1_margin2'];
		} else {
			$this->data['lstyle1_margin2'] = $this->config->get('lstyle1_margin2');
		}
		
		if (isset($this->request->post['lstyle1_margin3'])) {
			$this->data['lstyle1_margin3'] = $this->request->post['lstyle1_margin3'];
		} else {
			$this->data['lstyle1_margin3'] = $this->config->get('lstyle1_margin3');
		}
		
		if (isset($this->request->post['lstyle2_status'])) {
			$this->data['lstyle2_status'] = $this->request->post['lstyle2_status'];
		} else {
			$this->data['lstyle2_status'] = $this->config->get('lstyle2_status');
		}
		
		if (isset($this->request->post['lstyle2_prodnama'])) {
			$this->data['lstyle2_prodnama'] = $this->request->post['lstyle2_prodnama'];
		} else {
			$this->data['lstyle2_prodnama'] = $this->config->get('lstyle2_prodnama');
		}
		
		if (isset($this->request->post['lstyle2_pricecol'])) {
			$this->data['lstyle2_pricecol'] = $this->request->post['lstyle2_pricecol'];
		} else {
			$this->data['lstyle2_pricecol'] = $this->config->get('lstyle2_pricecol');
		}
		
		if (isset($this->request->post['lstyle2_oldpricecol'])) {
			$this->data['lstyle2_oldpricecol'] = $this->request->post['lstyle2_oldpricecol'];
		} else {
			$this->data['lstyle2_oldpricecol'] = $this->config->get('lstyle2_oldpricecol');
		}
		
		if (isset($this->request->post['lstyle2_shortdescol'])) {
			$this->data['lstyle2_shortdescol'] = $this->request->post['lstyle2_shortdescol'];
		} else {
			$this->data['lstyle2_shortdescol'] = $this->config->get('lstyle2_shortdescol');
		}
		
		if (isset($this->request->post['lstyle2_bgroundcol'])) {
			$this->data['lstyle2_bgroundcol'] = $this->request->post['lstyle2_bgroundcol'];
		} else {
			$this->data['lstyle2_bgroundcol'] = $this->config->get('lstyle2_bgroundcol');
		}
		
		if (isset($this->request->post['lstyle2_bordercol'])) {
			$this->data['lstyle2_bordercol'] = $this->request->post['lstyle2_bordercol'];
		} else {
			$this->data['lstyle2_bordercol'] = $this->config->get('lstyle2_bordercol');
		}
		
		if (isset($this->request->post['lstyle2_boxprodwidth'])) {
			$this->data['lstyle2_boxprodwidth'] = $this->request->post['lstyle2_boxprodwidth'];
		} else {
			$this->data['lstyle2_boxprodwidth'] = $this->config->get('lstyle2_boxprodwidth');
		}
		
		if (isset($this->request->post['lstyle2_margin1'])) {
			$this->data['lstyle2_margin1'] = $this->request->post['lstyle2_margin1'];
		} else {
			$this->data['lstyle2_margin1'] = $this->config->get('lstyle2_margin1');
		}
		
		if (isset($this->request->post['lstyle2_margin2'])) {
			$this->data['lstyle2_margin2'] = $this->request->post['lstyle2_margin2'];
		} else {
			$this->data['lstyle2_margin2'] = $this->config->get('lstyle2_margin2');
		}
		
		if (isset($this->request->post['lstyle2_margin3'])) {
			$this->data['lstyle2_margin3'] = $this->request->post['lstyle2_margin3'];
		} else {
			$this->data['lstyle2_margin3'] = $this->config->get('lstyle2_margin3');
		}
		
		if (isset($this->request->post['lstyle3_status'])) {
			$this->data['lstyle3_status'] = $this->request->post['lstyle3_status'];
		} else {
			$this->data['lstyle3_status'] = $this->config->get('lstyle3_status');
		}
		
		if (isset($this->request->post['lstyle3_prodnama'])) {
			$this->data['lstyle3_prodnama'] = $this->request->post['lstyle3_prodnama'];
		} else {
			$this->data['lstyle3_prodnama'] = $this->config->get('lstyle3_prodnama');
		}
		
		if (isset($this->request->post['lstyle3_pricecol'])) {
			$this->data['lstyle3_pricecol'] = $this->request->post['lstyle3_pricecol'];
		} else {
			$this->data['lstyle3_pricecol'] = $this->config->get('lstyle3_pricecol');
		}
		
		if (isset($this->request->post['lstyle3_oldpricecol'])) {
			$this->data['lstyle3_oldpricecol'] = $this->request->post['lstyle3_oldpricecol'];
		} else {
			$this->data['lstyle3_oldpricecol'] = $this->config->get('lstyle3_oldpricecol');
		}
		
		if (isset($this->request->post['lstyle3_shortdescol'])) {
			$this->data['lstyle3_shortdescol'] = $this->request->post['lstyle3_shortdescol'];
		} else {
			$this->data['lstyle3_shortdescol'] = $this->config->get('lstyle3_shortdescol');
		}
		
		if (isset($this->request->post['lstyle3_bgroundcol'])) {
			$this->data['lstyle3_bgroundcol'] = $this->request->post['lstyle3_bgroundcol'];
		} else {
			$this->data['lstyle3_bgroundcol'] = $this->config->get('lstyle3_bgroundcol');
		}
		
		if (isset($this->request->post['lstyle3_bordercol'])) {
			$this->data['lstyle3_bordercol'] = $this->request->post['lstyle3_bordercol'];
		} else {
			$this->data['lstyle3_bordercol'] = $this->config->get('lstyle3_bordercol');
		}
		
		if (isset($this->request->post['lstyle3_boxprodwidth'])) {
			$this->data['lstyle3_boxprodwidth'] = $this->request->post['lstyle3_boxprodwidth'];
		} else {
			$this->data['lstyle3_boxprodwidth'] = $this->config->get('lstyle3_boxprodwidth');
		}
		
		if (isset($this->request->post['lstyle3_margin1'])) {
			$this->data['lstyle3_margin1'] = $this->request->post['lstyle3_margin1'];
		} else {
			$this->data['lstyle3_margin1'] = $this->config->get('lstyle3_margin1');
		}
		
		if (isset($this->request->post['lstyle3_margin2'])) {
			$this->data['lstyle3_margin2'] = $this->request->post['lstyle3_margin2'];
		} else {
			$this->data['lstyle3_margin2'] = $this->config->get('lstyle3_margin2');
		}
		
		if (isset($this->request->post['lstyle3_margin3'])) {
			$this->data['lstyle3_margin3'] = $this->request->post['lstyle3_margin3'];
		} else {
			$this->data['lstyle3_margin3'] = $this->config->get('lstyle3_margin3');
		}
		
		if (isset($this->request->post['lstyle4_status'])) {
			$this->data['lstyle4_status'] = $this->request->post['lstyle4_status'];
		} else {
			$this->data['lstyle4_status'] = $this->config->get('lstyle4_status');
		}
		
		if (isset($this->request->post['lstyle4_prodnama'])) {
			$this->data['lstyle4_prodnama'] = $this->request->post['lstyle4_prodnama'];
		} else {
			$this->data['lstyle4_prodnama'] = $this->config->get('lstyle4_prodnama');
		}
		
		if (isset($this->request->post['lstyle4_pricecol'])) {
			$this->data['lstyle4_pricecol'] = $this->request->post['lstyle4_pricecol'];
		} else {
			$this->data['lstyle4_pricecol'] = $this->config->get('lstyle4_pricecol');
		}
		
		if (isset($this->request->post['lstyle4_oldpricecol'])) {
			$this->data['lstyle4_oldpricecol'] = $this->request->post['lstyle4_oldpricecol'];
		} else {
			$this->data['lstyle4_oldpricecol'] = $this->config->get('lstyle4_oldpricecol');
		}
		
		if (isset($this->request->post['lstyle4_bgroundcol'])) {
			$this->data['lstyle4_bgroundcol'] = $this->request->post['lstyle4_bgroundcol'];
		} else {
			$this->data['lstyle4_bgroundcol'] = $this->config->get('lstyle4_bgroundcol');
		}
		
		if (isset($this->request->post['lstyle4_bordercol'])) {
			$this->data['lstyle4_bordercol'] = $this->request->post['lstyle4_bordercol'];
		} else {
			$this->data['lstyle4_bordercol'] = $this->config->get('lstyle4_bordercol');
		}
		
		if (isset($this->request->post['lstyle4_boxprodwidth'])) {
			$this->data['lstyle4_boxprodwidth'] = $this->request->post['lstyle4_boxprodwidth'];
		} else {
			$this->data['lstyle4_boxprodwidth'] = $this->config->get('lstyle4_boxprodwidth');
		}
		
		if (isset($this->request->post['lstyle4_margin1'])) {
			$this->data['lstyle4_margin1'] = $this->request->post['lstyle4_margin1'];
		} else {
			$this->data['lstyle4_margin1'] = $this->config->get('lstyle4_margin1');
		}
		
		if (isset($this->request->post['lstyle4_margin2'])) {
			$this->data['lstyle4_margin2'] = $this->request->post['lstyle4_margin2'];
		} else {
			$this->data['lstyle4_margin2'] = $this->config->get('lstyle4_margin2');
		}
		
		if (isset($this->request->post['lstyle4_margin3'])) {
			$this->data['lstyle4_margin3'] = $this->request->post['lstyle4_margin3'];
		} else {
			$this->data['lstyle4_margin3'] = $this->config->get('lstyle4_margin3');
		}
		
		if (isset($this->request->post['lstyle5_status'])) {
			$this->data['lstyle5_status'] = $this->request->post['lstyle5_status'];
		} else {
			$this->data['lstyle5_status'] = $this->config->get('lstyle5_status');
		}
		
		if (isset($this->request->post['lstyle5_prodnama'])) {
			$this->data['lstyle5_prodnama'] = $this->request->post['lstyle5_prodnama'];
		} else {
			$this->data['lstyle5_prodnama'] = $this->config->get('lstyle5_prodnama');
		}
		
		if (isset($this->request->post['lstyle5_pricecol'])) {
			$this->data['lstyle5_pricecol'] = $this->request->post['lstyle5_pricecol'];
		} else {
			$this->data['lstyle5_pricecol'] = $this->config->get('lstyle5_pricecol');
		}
		
		if (isset($this->request->post['lstyle5_oldpricecol'])) {
			$this->data['lstyle5_oldpricecol'] = $this->request->post['lstyle5_oldpricecol'];
		} else {
			$this->data['lstyle5_oldpricecol'] = $this->config->get('lstyle5_oldpricecol');
		}
		
		if (isset($this->request->post['lstyle5_bgroundcol'])) {
			$this->data['lstyle5_bgroundcol'] = $this->request->post['lstyle5_bgroundcol'];
		} else {
			$this->data['lstyle5_bgroundcol'] = $this->config->get('lstyle5_bgroundcol');
		}
		
		if (isset($this->request->post['lstyle5_bordercol'])) {
			$this->data['lstyle5_bordercol'] = $this->request->post['lstyle5_bordercol'];
		} else {
			$this->data['lstyle5_bordercol'] = $this->config->get('lstyle5_bordercol');
		}
		
		if (isset($this->request->post['lstyle5_boxprodwidth'])) {
			$this->data['lstyle5_boxprodwidth'] = $this->request->post['lstyle5_boxprodwidth'];
		} else {
			$this->data['lstyle5_boxprodwidth'] = $this->config->get('lstyle5_boxprodwidth');
		}
		
		if (isset($this->request->post['lstyle5_margin1'])) {
			$this->data['lstyle5_margin1'] = $this->request->post['lstyle5_margin1'];
		} else {
			$this->data['lstyle5_margin1'] = $this->config->get('lstyle5_margin1');
		}
		
		if (isset($this->request->post['lstyle5_margin2'])) {
			$this->data['lstyle5_margin2'] = $this->request->post['lstyle5_margin2'];
		} else {
			$this->data['lstyle5_margin2'] = $this->config->get('lstyle5_margin2');
		}
		
		if (isset($this->request->post['lstyle5_margin3'])) {
			$this->data['lstyle5_margin3'] = $this->request->post['lstyle5_margin3'];
		} else {
			$this->data['lstyle5_margin3'] = $this->config->get('lstyle5_margin3');
		}
		// latest plus mod end //		
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/latestplus.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/latestplus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['latestplus_module'])) {
			foreach ($this->request->post['latestplus_module'] as $key => $value) {
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