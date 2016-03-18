<?php
class ControllerModuleBestSellerplus extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/bestsellerplus');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		// bestseller plus mod start //
		$this->document->addScript('view/javascript/jquery/colorpicker/colorpicker.js');
		$this->document->addStyle('view/stylesheet/colorpicker/css/colorpicker.css');
		$this->document->addStyle('view/stylesheet/prodview.css');	
		// bestseller plus mod end //		
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bestsellerplus', $this->request->post);	
			
		// bestseller plus mod start //	
        $bestsellerplus_settings = array(
		'bstyle1_status'    			=> $this->request->post['bstyle1_status'],
		'bstyle1_prodnama'    			=> $this->request->post['bstyle1_prodnama'],
		'bstyle1_pricecol'    			=> $this->request->post['bstyle1_pricecol'],
		'bstyle1_oldpricecol'    		=> $this->request->post['bstyle1_oldpricecol'],
		'bstyle1_shortdescol'    		=> $this->request->post['bstyle1_shortdescol'],
		'bstyle1_bgroundcol'    		=> $this->request->post['bstyle1_bgroundcol'],
		'bstyle1_bordercol'    			=> $this->request->post['bstyle1_bordercol'],
		'bstyle1_boxprodwidth'    	    => $this->request->post['bstyle1_boxprodwidth'],
		'bstyle1_margin1'    			=> $this->request->post['bstyle1_margin1'],
		'bstyle1_margin2'    			=> $this->request->post['bstyle1_margin2'],
		'bstyle1_margin3'    			=> $this->request->post['bstyle1_margin3'],
		'bstyle2_status'    			=> $this->request->post['bstyle2_status'],
		'bstyle2_prodnama'    			=> $this->request->post['bstyle2_prodnama'],
		'bstyle2_pricecol'    			=> $this->request->post['bstyle2_pricecol'],
		'bstyle2_oldpricecol'    		=> $this->request->post['bstyle2_oldpricecol'],
		'bstyle2_shortdescol'    		=> $this->request->post['bstyle2_shortdescol'],
		'bstyle2_bgroundcol'    		=> $this->request->post['bstyle2_bgroundcol'],
		'bstyle2_bordercol'    			=> $this->request->post['bstyle2_bordercol'],
		'bstyle2_boxprodwidth'    	    => $this->request->post['bstyle2_boxprodwidth'],
		'bstyle2_margin1'    			=> $this->request->post['bstyle2_margin1'],
		'bstyle2_margin2'    			=> $this->request->post['bstyle2_margin2'],
		'bstyle2_margin3'    			=> $this->request->post['bstyle2_margin3'],
		'bstyle3_status'    			=> $this->request->post['bstyle3_status'],
		'bstyle3_prodnama'    			=> $this->request->post['bstyle3_prodnama'],
		'bstyle3_pricecol'    			=> $this->request->post['bstyle3_pricecol'],
		'bstyle3_oldpricecol'    		=> $this->request->post['bstyle3_oldpricecol'],
		'bstyle3_shortdescol'    		=> $this->request->post['bstyle3_shortdescol'],
		'bstyle3_bgroundcol'    		=> $this->request->post['bstyle3_bgroundcol'],
		'bstyle3_bordercol'    			=> $this->request->post['bstyle3_bordercol'],
		'bstyle3_boxprodwidth'    	    => $this->request->post['bstyle3_boxprodwidth'],
		'bstyle3_margin1'    			=> $this->request->post['bstyle3_margin1'],
		'bstyle3_margin2'    			=> $this->request->post['bstyle3_margin2'],
		'bstyle3_margin3'    			=> $this->request->post['bstyle3_margin3'],
		'bstyle4_status'    			=> $this->request->post['bstyle4_status'],
		'bstyle4_prodnama'    			=> $this->request->post['bstyle4_prodnama'],
		'bstyle4_pricecol'    			=> $this->request->post['bstyle4_pricecol'],
		'bstyle4_oldpricecol'    		=> $this->request->post['bstyle4_oldpricecol'],
		'bstyle4_bgroundcol'    		=> $this->request->post['bstyle4_bgroundcol'],
		'bstyle4_bordercol'    			=> $this->request->post['bstyle4_bordercol'],
		'bstyle4_boxprodwidth'    	    => $this->request->post['bstyle4_boxprodwidth'],
		'bstyle4_margin1'    			=> $this->request->post['bstyle4_margin1'],
		'bstyle4_margin2'    			=> $this->request->post['bstyle4_margin2'],
		'bstyle4_margin3'    			=> $this->request->post['bstyle4_margin3'],
		'bstyle5_status'    			=> $this->request->post['bstyle5_status'],
		'bstyle5_prodnama'    			=> $this->request->post['bstyle5_prodnama'],
		'bstyle5_pricecol'    			=> $this->request->post['bstyle5_pricecol'],
		'bstyle5_oldpricecol'    		=> $this->request->post['bstyle5_oldpricecol'],
		'bstyle5_bgroundcol'    		=> $this->request->post['bstyle5_bgroundcol'],
		'bstyle5_bordercol'    			=> $this->request->post['bstyle5_bordercol'],
		'bstyle5_boxprodwidth'    	    => $this->request->post['bstyle5_boxprodwidth'],
		'bstyle5_margin1'    			=> $this->request->post['bstyle5_margin1'],
		'bstyle5_margin2'    			=> $this->request->post['bstyle5_margin2'],
		'bstyle5_margin3'    			=> $this->request->post['bstyle5_margin3']
		);	
		// bestseller plus mod end //					
			
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

		// bestseller plus mod start //
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
		// bestseller plus mod end //		
		
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
			'href'      => $this->url->link('module/bestsellerplus', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/bestsellerplus', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['modules'] = array();
		
		if (isset($this->request->post['bestsellerplus_module'])) {
			$this->data['modules'] = $this->request->post['bestsellerplus_module'];
		} elseif ($this->config->get('bestsellerplus_module')) { 
			$this->data['modules'] = $this->config->get('bestsellerplus_module');
		}	
		
		// bestseller plus mod start //
		if (isset($this->request->post['bstyle1_status'])) {
			$this->data['bstyle1_status'] = $this->request->post['bstyle1_status'];
		} else {
			$this->data['bstyle1_status'] = $this->config->get('bstyle1_status');
		}
		
		if (isset($this->request->post['bstyle1_prodnama'])) {
			$this->data['bstyle1_prodnama'] = $this->request->post['bstyle1_prodnama'];
		} else {
			$this->data['bstyle1_prodnama'] = $this->config->get('bstyle1_prodnama');
		}
		
		if (isset($this->request->post['bstyle1_pricecol'])) {
			$this->data['bstyle1_pricecol'] = $this->request->post['bstyle1_pricecol'];
		} else {
			$this->data['bstyle1_pricecol'] = $this->config->get('bstyle1_pricecol');
		}
		
		if (isset($this->request->post['bstyle1_oldpricecol'])) {
			$this->data['bstyle1_oldpricecol'] = $this->request->post['bstyle1_oldpricecol'];
		} else {
			$this->data['bstyle1_oldpricecol'] = $this->config->get('bstyle1_oldpricecol');
		}
		
		if (isset($this->request->post['bstyle1_shortdescol'])) {
			$this->data['bstyle1_shortdescol'] = $this->request->post['bstyle1_shortdescol'];
		} else {
			$this->data['bstyle1_shortdescol'] = $this->config->get('bstyle1_shortdescol');
		}
		
		if (isset($this->request->post['bstyle1_bgroundcol'])) {
			$this->data['bstyle1_bgroundcol'] = $this->request->post['bstyle1_bgroundcol'];
		} else {
			$this->data['bstyle1_bgroundcol'] = $this->config->get('bstyle1_bgroundcol');
		}
		
		if (isset($this->request->post['bstyle1_bordercol'])) {
			$this->data['bstyle1_bordercol'] = $this->request->post['bstyle1_bordercol'];
		} else {
			$this->data['bstyle1_bordercol'] = $this->config->get('bstyle1_bordercol');
		}
		
		if (isset($this->request->post['bstyle1_boxprodwidth'])) {
			$this->data['bstyle1_boxprodwidth'] = $this->request->post['bstyle1_boxprodwidth'];
		} else {
			$this->data['bstyle1_boxprodwidth'] = $this->config->get('bstyle1_boxprodwidth');
		}
		
		if (isset($this->request->post['bstyle1_margin1'])) {
			$this->data['bstyle1_margin1'] = $this->request->post['bstyle1_margin1'];
		} else {
			$this->data['bstyle1_margin1'] = $this->config->get('bstyle1_margin1');
		}
		
		if (isset($this->request->post['bstyle1_margin2'])) {
			$this->data['bstyle1_margin2'] = $this->request->post['bstyle1_margin2'];
		} else {
			$this->data['bstyle1_margin2'] = $this->config->get('bstyle1_margin2');
		}
		
		if (isset($this->request->post['bstyle1_margin3'])) {
			$this->data['bstyle1_margin3'] = $this->request->post['bstyle1_margin3'];
		} else {
			$this->data['bstyle1_margin3'] = $this->config->get('bstyle1_margin3');
		}
		
		if (isset($this->request->post['bstyle2_status'])) {
			$this->data['bstyle2_status'] = $this->request->post['bstyle2_status'];
		} else {
			$this->data['bstyle2_status'] = $this->config->get('bstyle2_status');
		}
		
		if (isset($this->request->post['bstyle2_prodnama'])) {
			$this->data['bstyle2_prodnama'] = $this->request->post['bstyle2_prodnama'];
		} else {
			$this->data['bstyle2_prodnama'] = $this->config->get('bstyle2_prodnama');
		}
		
		if (isset($this->request->post['bstyle2_pricecol'])) {
			$this->data['bstyle2_pricecol'] = $this->request->post['bstyle2_pricecol'];
		} else {
			$this->data['bstyle2_pricecol'] = $this->config->get('bstyle2_pricecol');
		}
		
		if (isset($this->request->post['bstyle2_oldpricecol'])) {
			$this->data['bstyle2_oldpricecol'] = $this->request->post['bstyle2_oldpricecol'];
		} else {
			$this->data['bstyle2_oldpricecol'] = $this->config->get('bstyle2_oldpricecol');
		}
		
		if (isset($this->request->post['bstyle2_shortdescol'])) {
			$this->data['bstyle2_shortdescol'] = $this->request->post['bstyle2_shortdescol'];
		} else {
			$this->data['bstyle2_shortdescol'] = $this->config->get('bstyle2_shortdescol');
		}
		
		if (isset($this->request->post['bstyle2_bgroundcol'])) {
			$this->data['bstyle2_bgroundcol'] = $this->request->post['bstyle2_bgroundcol'];
		} else {
			$this->data['bstyle2_bgroundcol'] = $this->config->get('bstyle2_bgroundcol');
		}
		
		if (isset($this->request->post['bstyle2_bordercol'])) {
			$this->data['bstyle2_bordercol'] = $this->request->post['bstyle2_bordercol'];
		} else {
			$this->data['bstyle2_bordercol'] = $this->config->get('bstyle2_bordercol');
		}
		
		if (isset($this->request->post['bstyle2_boxprodwidth'])) {
			$this->data['bstyle2_boxprodwidth'] = $this->request->post['bstyle2_boxprodwidth'];
		} else {
			$this->data['bstyle2_boxprodwidth'] = $this->config->get('bstyle2_boxprodwidth');
		}
		
		if (isset($this->request->post['bstyle2_margin1'])) {
			$this->data['bstyle2_margin1'] = $this->request->post['bstyle2_margin1'];
		} else {
			$this->data['bstyle2_margin1'] = $this->config->get('bstyle2_margin1');
		}
		
		if (isset($this->request->post['bstyle2_margin2'])) {
			$this->data['bstyle2_margin2'] = $this->request->post['bstyle2_margin2'];
		} else {
			$this->data['bstyle2_margin2'] = $this->config->get('bstyle2_margin2');
		}
		
		if (isset($this->request->post['bstyle2_margin3'])) {
			$this->data['bstyle2_margin3'] = $this->request->post['bstyle2_margin3'];
		} else {
			$this->data['bstyle2_margin3'] = $this->config->get('bstyle2_margin3');
		}
		
		if (isset($this->request->post['bstyle3_status'])) {
			$this->data['bstyle3_status'] = $this->request->post['bstyle3_status'];
		} else {
			$this->data['bstyle3_status'] = $this->config->get('bstyle3_status');
		}
		
		if (isset($this->request->post['bstyle3_prodnama'])) {
			$this->data['bstyle3_prodnama'] = $this->request->post['bstyle3_prodnama'];
		} else {
			$this->data['bstyle3_prodnama'] = $this->config->get('bstyle3_prodnama');
		}
		
		if (isset($this->request->post['bstyle3_pricecol'])) {
			$this->data['bstyle3_pricecol'] = $this->request->post['bstyle3_pricecol'];
		} else {
			$this->data['bstyle3_pricecol'] = $this->config->get('bstyle3_pricecol');
		}
		
		if (isset($this->request->post['bstyle3_oldpricecol'])) {
			$this->data['bstyle3_oldpricecol'] = $this->request->post['bstyle3_oldpricecol'];
		} else {
			$this->data['bstyle3_oldpricecol'] = $this->config->get('bstyle3_oldpricecol');
		}
		
		if (isset($this->request->post['bstyle3_shortdescol'])) {
			$this->data['bstyle3_shortdescol'] = $this->request->post['bstyle3_shortdescol'];
		} else {
			$this->data['bstyle3_shortdescol'] = $this->config->get('bstyle3_shortdescol');
		}
		
		if (isset($this->request->post['bstyle3_bgroundcol'])) {
			$this->data['bstyle3_bgroundcol'] = $this->request->post['bstyle3_bgroundcol'];
		} else {
			$this->data['bstyle3_bgroundcol'] = $this->config->get('bstyle3_bgroundcol');
		}
		
		if (isset($this->request->post['bstyle3_bordercol'])) {
			$this->data['bstyle3_bordercol'] = $this->request->post['bstyle3_bordercol'];
		} else {
			$this->data['bstyle3_bordercol'] = $this->config->get('bstyle3_bordercol');
		}
		
		if (isset($this->request->post['bstyle3_boxprodwidth'])) {
			$this->data['bstyle3_boxprodwidth'] = $this->request->post['bstyle3_boxprodwidth'];
		} else {
			$this->data['bstyle3_boxprodwidth'] = $this->config->get('bstyle3_boxprodwidth');
		}
		
		if (isset($this->request->post['bstyle3_margin1'])) {
			$this->data['bstyle3_margin1'] = $this->request->post['bstyle3_margin1'];
		} else {
			$this->data['bstyle3_margin1'] = $this->config->get('bstyle3_margin1');
		}
		
		if (isset($this->request->post['bstyle3_margin2'])) {
			$this->data['bstyle3_margin2'] = $this->request->post['bstyle3_margin2'];
		} else {
			$this->data['bstyle3_margin2'] = $this->config->get('bstyle3_margin2');
		}
		
		if (isset($this->request->post['bstyle3_margin3'])) {
			$this->data['bstyle3_margin3'] = $this->request->post['bstyle3_margin3'];
		} else {
			$this->data['bstyle3_margin3'] = $this->config->get('bstyle3_margin3');
		}
		
		if (isset($this->request->post['bstyle4_status'])) {
			$this->data['bstyle4_status'] = $this->request->post['bstyle4_status'];
		} else {
			$this->data['bstyle4_status'] = $this->config->get('bstyle4_status');
		}
		
		if (isset($this->request->post['bstyle4_prodnama'])) {
			$this->data['bstyle4_prodnama'] = $this->request->post['bstyle4_prodnama'];
		} else {
			$this->data['bstyle4_prodnama'] = $this->config->get('bstyle4_prodnama');
		}
		
		if (isset($this->request->post['bstyle4_pricecol'])) {
			$this->data['bstyle4_pricecol'] = $this->request->post['bstyle4_pricecol'];
		} else {
			$this->data['bstyle4_pricecol'] = $this->config->get('bstyle4_pricecol');
		}
		
		if (isset($this->request->post['bstyle4_oldpricecol'])) {
			$this->data['bstyle4_oldpricecol'] = $this->request->post['bstyle4_oldpricecol'];
		} else {
			$this->data['bstyle4_oldpricecol'] = $this->config->get('bstyle4_oldpricecol');
		}
		
		if (isset($this->request->post['bstyle4_bgroundcol'])) {
			$this->data['bstyle4_bgroundcol'] = $this->request->post['bstyle4_bgroundcol'];
		} else {
			$this->data['bstyle4_bgroundcol'] = $this->config->get('bstyle4_bgroundcol');
		}
		
		if (isset($this->request->post['bstyle4_bordercol'])) {
			$this->data['bstyle4_bordercol'] = $this->request->post['bstyle4_bordercol'];
		} else {
			$this->data['bstyle4_bordercol'] = $this->config->get('bstyle4_bordercol');
		}
		
		if (isset($this->request->post['bstyle4_boxprodwidth'])) {
			$this->data['bstyle4_boxprodwidth'] = $this->request->post['bstyle4_boxprodwidth'];
		} else {
			$this->data['bstyle4_boxprodwidth'] = $this->config->get('bstyle4_boxprodwidth');
		}
		
		if (isset($this->request->post['bstyle4_margin1'])) {
			$this->data['bstyle4_margin1'] = $this->request->post['bstyle4_margin1'];
		} else {
			$this->data['bstyle4_margin1'] = $this->config->get('bstyle4_margin1');
		}
		
		if (isset($this->request->post['bstyle4_margin2'])) {
			$this->data['bstyle4_margin2'] = $this->request->post['bstyle4_margin2'];
		} else {
			$this->data['bstyle4_margin2'] = $this->config->get('bstyle4_margin2');
		}
		
		if (isset($this->request->post['bstyle4_margin3'])) {
			$this->data['bstyle4_margin3'] = $this->request->post['bstyle4_margin3'];
		} else {
			$this->data['bstyle4_margin3'] = $this->config->get('bstyle4_margin3');
		}
		
		if (isset($this->request->post['bstyle5_status'])) {
			$this->data['bstyle5_status'] = $this->request->post['bstyle5_status'];
		} else {
			$this->data['bstyle5_status'] = $this->config->get('bstyle5_status');
		}
		
		if (isset($this->request->post['bstyle5_prodnama'])) {
			$this->data['bstyle5_prodnama'] = $this->request->post['bstyle5_prodnama'];
		} else {
			$this->data['bstyle5_prodnama'] = $this->config->get('bstyle5_prodnama');
		}
		
		if (isset($this->request->post['bstyle5_pricecol'])) {
			$this->data['bstyle5_pricecol'] = $this->request->post['bstyle5_pricecol'];
		} else {
			$this->data['bstyle5_pricecol'] = $this->config->get('bstyle5_pricecol');
		}
		
		if (isset($this->request->post['bstyle5_oldpricecol'])) {
			$this->data['bstyle5_oldpricecol'] = $this->request->post['bstyle5_oldpricecol'];
		} else {
			$this->data['bstyle5_oldpricecol'] = $this->config->get('bstyle5_oldpricecol');
		}
		
		if (isset($this->request->post['bstyle5_bgroundcol'])) {
			$this->data['bstyle5_bgroundcol'] = $this->request->post['bstyle5_bgroundcol'];
		} else {
			$this->data['bstyle5_bgroundcol'] = $this->config->get('bstyle5_bgroundcol');
		}
		
		if (isset($this->request->post['bstyle5_bordercol'])) {
			$this->data['bstyle5_bordercol'] = $this->request->post['bstyle5_bordercol'];
		} else {
			$this->data['bstyle5_bordercol'] = $this->config->get('bstyle5_bordercol');
		}
		
		if (isset($this->request->post['bstyle5_boxprodwidth'])) {
			$this->data['bstyle5_boxprodwidth'] = $this->request->post['bstyle5_boxprodwidth'];
		} else {
			$this->data['bstyle5_boxprodwidth'] = $this->config->get('bstyle5_boxprodwidth');
		}
		
		if (isset($this->request->post['bstyle5_margin1'])) {
			$this->data['bstyle5_margin1'] = $this->request->post['bstyle5_margin1'];
		} else {
			$this->data['bstyle5_margin1'] = $this->config->get('bstyle5_margin1');
		}
		
		if (isset($this->request->post['bstyle5_margin2'])) {
			$this->data['bstyle5_margin2'] = $this->request->post['bstyle5_margin2'];
		} else {
			$this->data['bstyle5_margin2'] = $this->config->get('bstyle5_margin2');
		}
		
		if (isset($this->request->post['bstyle5_margin3'])) {
			$this->data['bstyle5_margin3'] = $this->request->post['bstyle5_margin3'];
		} else {
			$this->data['bstyle5_margin3'] = $this->config->get('bstyle5_margin3');

		}
		// bestseller plus mod end //			

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/bestsellerplus.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/bestsellerplus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['bestsellerplus_module'])) {
			foreach ($this->request->post['bestsellerplus_module'] as $key => $value) {
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