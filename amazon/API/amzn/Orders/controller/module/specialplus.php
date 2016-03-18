<?php
class ControllerModulespecialplus extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/specialplus');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		// special plus mod start //
		$this->document->addScript('view/javascript/jquery/colorpicker/colorpicker.js');
		$this->document->addStyle('view/stylesheet/colorpicker/css/colorpicker.css');
		$this->document->addStyle('view/stylesheet/prodview.css');	
		// special plus mod end /
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('specialplus', $this->request->post);	
		
		// special plus mod start //	
        $latestplus_settings = array(
		'sstyle1_status'    			=> $this->request->post['sstyle1_status'],
		'sstyle1_prodnama'    			=> $this->request->post['sstyle1_prodnama'],
		'sstyle1_pricecol'    			=> $this->request->post['sstyle1_pricecol'],
		'sstyle1_oldpricecol'    		=> $this->request->post['sstyle1_oldpricecol'],
		'sstyle1_shortdescol'    		=> $this->request->post['sstyle1_shortdescol'],
		'sstyle1_bgroundcol'    		=> $this->request->post['sstyle1_bgroundcol'],
		'sstyle1_bordercol'    			=> $this->request->post['sstyle1_bordercol'],
		'sstyle1_boxprodwidth'    	    => $this->request->post['sstyle1_boxprodwidth'],
		'sstyle1_margin1'    			=> $this->request->post['sstyle1_margin1'],
		'sstyle1_margin2'    			=> $this->request->post['sstyle1_margin2'],
		'sstyle1_margin3'    			=> $this->request->post['sstyle1_margin3'],
		'sstyle2_status'    			=> $this->request->post['sstyle2_status'],
		'sstyle2_prodnama'    			=> $this->request->post['sstyle2_prodnama'],
		'sstyle2_pricecol'    			=> $this->request->post['sstyle2_pricecol'],
		'sstyle2_oldpricecol'    		=> $this->request->post['sstyle2_oldpricecol'],
		'sstyle2_shortdescol'    		=> $this->request->post['sstyle2_shortdescol'],
		'sstyle2_bgroundcol'    		=> $this->request->post['sstyle2_bgroundcol'],
		'sstyle2_bordercol'    			=> $this->request->post['sstyle2_bordercol'],
		'sstyle2_boxprodwidth'    	    => $this->request->post['sstyle2_boxprodwidth'],
		'sstyle2_margin1'    			=> $this->request->post['sstyle2_margin1'],
		'sstyle2_margin2'    			=> $this->request->post['sstyle2_margin2'],
		'sstyle2_margin3'    			=> $this->request->post['sstyle2_margin3'],
		'sstyle3_status'    			=> $this->request->post['sstyle3_status'],
		'sstyle3_prodnama'    			=> $this->request->post['sstyle3_prodnama'],
		'sstyle3_pricecol'    			=> $this->request->post['sstyle3_pricecol'],
		'sstyle3_oldpricecol'    		=> $this->request->post['sstyle3_oldpricecol'],
		'sstyle3_shortdescol'    		=> $this->request->post['sstyle3_shortdescol'],
		'sstyle3_bgroundcol'    		=> $this->request->post['sstyle3_bgroundcol'],
		'sstyle3_bordercol'    			=> $this->request->post['sstyle3_bordercol'],
		'sstyle3_boxprodwidth'    	    => $this->request->post['sstyle3_boxprodwidth'],
		'sstyle3_margin1'    			=> $this->request->post['sstyle3_margin1'],
		'sstyle3_margin2'    			=> $this->request->post['sstyle3_margin2'],
		'sstyle3_margin3'    			=> $this->request->post['sstyle3_margin3'],
		'sstyle4_status'    			=> $this->request->post['sstyle4_status'],
		'sstyle4_prodnama'    			=> $this->request->post['sstyle4_prodnama'],
		'sstyle4_pricecol'    			=> $this->request->post['sstyle4_pricecol'],
		'sstyle4_oldpricecol'    		=> $this->request->post['sstyle4_oldpricecol'],
		'sstyle4_bgroundcol'    		=> $this->request->post['sstyle4_bgroundcol'],
		'sstyle4_bordercol'    			=> $this->request->post['sstyle4_bordercol'],
		'sstyle4_boxprodwidth'    	    => $this->request->post['sstyle4_boxprodwidth'],
		'sstyle4_margin1'    			=> $this->request->post['sstyle4_margin1'],
		'sstyle4_margin2'    			=> $this->request->post['sstyle4_margin2'],
		'sstyle4_margin3'    			=> $this->request->post['sstyle4_margin3'],
		'sstyle5_status'    			=> $this->request->post['sstyle5_status'],
		'sstyle5_prodnama'    			=> $this->request->post['sstyle5_prodnama'],
		'sstyle5_pricecol'    			=> $this->request->post['sstyle5_pricecol'],
		'sstyle5_oldpricecol'    		=> $this->request->post['sstyle5_oldpricecol'],
		'sstyle5_bgroundcol'    		=> $this->request->post['sstyle5_bgroundcol'],
		'sstyle5_bordercol'    			=> $this->request->post['sstyle5_bordercol'],
		'sstyle5_boxprodwidth'    	    => $this->request->post['sstyle5_boxprodwidth'],
		'sstyle5_margin1'    			=> $this->request->post['sstyle5_margin1'],
		'sstyle5_margin2'    			=> $this->request->post['sstyle5_margin2'],
		'sstyle5_margin3'    			=> $this->request->post['sstyle5_margin3']
		);					
		// special plus mod end //	
			
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
		
		// special plus mod start //
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
		// special plus mod end //		
		
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
			'href'      => $this->url->link('module/specialplus', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/specialplus', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();
		
		if (isset($this->request->post['specialplus_module'])) {
			$this->data['modules'] = $this->request->post['specialplus_module'];
		} elseif ($this->config->get('specialplus_module')) { 
			$this->data['modules'] = $this->config->get('specialplus_module');
		}
		
		// special plus mod start //
		if (isset($this->request->post['sstyle1_status'])) {
			$this->data['sstyle1_status'] = $this->request->post['sstyle1_status'];
		} else {
			$this->data['sstyle1_status'] = $this->config->get('sstyle1_status');
		}
		
		if (isset($this->request->post['sstyle1_prodnama'])) {
			$this->data['sstyle1_prodnama'] = $this->request->post['sstyle1_prodnama'];
		} else {
			$this->data['sstyle1_prodnama'] = $this->config->get('sstyle1_prodnama');
		}
		
		if (isset($this->request->post['sstyle1_pricecol'])) {
			$this->data['sstyle1_pricecol'] = $this->request->post['sstyle1_pricecol'];
		} else {
			$this->data['sstyle1_pricecol'] = $this->config->get('sstyle1_pricecol');
		}
		
		if (isset($this->request->post['sstyle1_oldpricecol'])) {
			$this->data['sstyle1_oldpricecol'] = $this->request->post['sstyle1_oldpricecol'];
		} else {
			$this->data['sstyle1_oldpricecol'] = $this->config->get('sstyle1_oldpricecol');
		}
		
		if (isset($this->request->post['sstyle1_shortdescol'])) {
			$this->data['sstyle1_shortdescol'] = $this->request->post['sstyle1_shortdescol'];
		} else {
			$this->data['sstyle1_shortdescol'] = $this->config->get('sstyle1_shortdescol');
		}
		
		if (isset($this->request->post['sstyle1_bgroundcol'])) {
			$this->data['sstyle1_bgroundcol'] = $this->request->post['sstyle1_bgroundcol'];
		} else {
			$this->data['sstyle1_bgroundcol'] = $this->config->get('sstyle1_bgroundcol');
		}
		
		if (isset($this->request->post['sstyle1_bordercol'])) {
			$this->data['sstyle1_bordercol'] = $this->request->post['sstyle1_bordercol'];
		} else {
			$this->data['sstyle1_bordercol'] = $this->config->get('sstyle1_bordercol');
		}
		
		if (isset($this->request->post['sstyle1_boxprodwidth'])) {
			$this->data['sstyle1_boxprodwidth'] = $this->request->post['sstyle1_boxprodwidth'];
		} else {
			$this->data['sstyle1_boxprodwidth'] = $this->config->get('sstyle1_boxprodwidth');
		}
		
		if (isset($this->request->post['sstyle1_margin1'])) {
			$this->data['sstyle1_margin1'] = $this->request->post['sstyle1_margin1'];
		} else {
			$this->data['sstyle1_margin1'] = $this->config->get('sstyle1_margin1');
		}
		
		if (isset($this->request->post['sstyle1_margin2'])) {
			$this->data['sstyle1_margin2'] = $this->request->post['sstyle1_margin2'];
		} else {
			$this->data['sstyle1_margin2'] = $this->config->get('sstyle1_margin2');
		}
		
		if (isset($this->request->post['sstyle1_margin3'])) {
			$this->data['sstyle1_margin3'] = $this->request->post['sstyle1_margin3'];
		} else {
			$this->data['sstyle1_margin3'] = $this->config->get('sstyle1_margin3');
		}
		
		if (isset($this->request->post['sstyle2_status'])) {
			$this->data['sstyle2_status'] = $this->request->post['sstyle2_status'];
		} else {
			$this->data['sstyle2_status'] = $this->config->get('sstyle2_status');
		}
		
		if (isset($this->request->post['sstyle2_prodnama'])) {
			$this->data['sstyle2_prodnama'] = $this->request->post['sstyle2_prodnama'];
		} else {
			$this->data['sstyle2_prodnama'] = $this->config->get('sstyle2_prodnama');
		}
		
		if (isset($this->request->post['sstyle2_pricecol'])) {
			$this->data['sstyle2_pricecol'] = $this->request->post['sstyle2_pricecol'];
		} else {
			$this->data['sstyle2_pricecol'] = $this->config->get('sstyle2_pricecol');
		}
		
		if (isset($this->request->post['sstyle2_oldpricecol'])) {
			$this->data['sstyle2_oldpricecol'] = $this->request->post['sstyle2_oldpricecol'];
		} else {
			$this->data['sstyle2_oldpricecol'] = $this->config->get('sstyle2_oldpricecol');
		}
		
		if (isset($this->request->post['sstyle2_shortdescol'])) {
			$this->data['sstyle2_shortdescol'] = $this->request->post['sstyle2_shortdescol'];
		} else {
			$this->data['sstyle2_shortdescol'] = $this->config->get('sstyle2_shortdescol');
		}
		
		if (isset($this->request->post['sstyle2_bgroundcol'])) {
			$this->data['sstyle2_bgroundcol'] = $this->request->post['sstyle2_bgroundcol'];
		} else {
			$this->data['sstyle2_bgroundcol'] = $this->config->get('sstyle2_bgroundcol');
		}
		

		if (isset($this->request->post['sstyle2_bordercol'])) {
			$this->data['sstyle2_bordercol'] = $this->request->post['sstyle2_bordercol'];
		} else {
			$this->data['sstyle2_bordercol'] = $this->config->get('sstyle2_bordercol');
		}
		
		if (isset($this->request->post['sstyle2_boxprodwidth'])) {
			$this->data['sstyle2_boxprodwidth'] = $this->request->post['sstyle2_boxprodwidth'];
		} else {
			$this->data['sstyle2_boxprodwidth'] = $this->config->get('sstyle2_boxprodwidth');
		}
		
		if (isset($this->request->post['sstyle2_margin1'])) {
			$this->data['sstyle2_margin1'] = $this->request->post['sstyle2_margin1'];
		} else {
			$this->data['sstyle2_margin1'] = $this->config->get('sstyle2_margin1');
		}
		
		if (isset($this->request->post['sstyle2_margin2'])) {
			$this->data['sstyle2_margin2'] = $this->request->post['sstyle2_margin2'];
		} else {
			$this->data['sstyle2_margin2'] = $this->config->get('sstyle2_margin2');
		}
		
		if (isset($this->request->post['sstyle2_margin3'])) {
			$this->data['sstyle2_margin3'] = $this->request->post['sstyle2_margin3'];
		} else {
			$this->data['sstyle2_margin3'] = $this->config->get('sstyle2_margin3');
		}
		
		if (isset($this->request->post['sstyle3_status'])) {
			$this->data['sstyle3_status'] = $this->request->post['sstyle3_status'];
		} else {
			$this->data['sstyle3_status'] = $this->config->get('sstyle3_status');
		}
		
		if (isset($this->request->post['sstyle3_prodnama'])) {
			$this->data['sstyle3_prodnama'] = $this->request->post['sstyle3_prodnama'];
		} else {
			$this->data['sstyle3_prodnama'] = $this->config->get('sstyle3_prodnama');
		}
		
		if (isset($this->request->post['sstyle3_pricecol'])) {
			$this->data['sstyle3_pricecol'] = $this->request->post['sstyle3_pricecol'];
		} else {
			$this->data['sstyle3_pricecol'] = $this->config->get('sstyle3_pricecol');
		}
		
		if (isset($this->request->post['sstyle3_oldpricecol'])) {
			$this->data['sstyle3_oldpricecol'] = $this->request->post['sstyle3_oldpricecol'];
		} else {
			$this->data['sstyle3_oldpricecol'] = $this->config->get('sstyle3_oldpricecol');
		}
		
		if (isset($this->request->post['sstyle3_shortdescol'])) {
			$this->data['sstyle3_shortdescol'] = $this->request->post['sstyle3_shortdescol'];
		} else {
			$this->data['sstyle3_shortdescol'] = $this->config->get('sstyle3_shortdescol');
		}
		
		if (isset($this->request->post['sstyle3_bgroundcol'])) {
			$this->data['sstyle3_bgroundcol'] = $this->request->post['sstyle3_bgroundcol'];
		} else {
			$this->data['sstyle3_bgroundcol'] = $this->config->get('sstyle3_bgroundcol');
		}
		
		if (isset($this->request->post['sstyle3_bordercol'])) {
			$this->data['sstyle3_bordercol'] = $this->request->post['sstyle3_bordercol'];
		} else {
			$this->data['sstyle3_bordercol'] = $this->config->get('sstyle3_bordercol');
		}
		
		if (isset($this->request->post['sstyle3_boxprodwidth'])) {
			$this->data['sstyle3_boxprodwidth'] = $this->request->post['sstyle3_boxprodwidth'];
		} else {
			$this->data['sstyle3_boxprodwidth'] = $this->config->get('sstyle3_boxprodwidth');
		}
		
		if (isset($this->request->post['sstyle3_margin1'])) {
			$this->data['sstyle3_margin1'] = $this->request->post['sstyle3_margin1'];
		} else {
			$this->data['sstyle3_margin1'] = $this->config->get('sstyle3_margin1');
		}
		
		if (isset($this->request->post['sstyle3_margin2'])) {
			$this->data['sstyle3_margin2'] = $this->request->post['sstyle3_margin2'];
		} else {
			$this->data['sstyle3_margin2'] = $this->config->get('sstyle3_margin2');
		}
		
		if (isset($this->request->post['sstyle3_margin3'])) {
			$this->data['sstyle3_margin3'] = $this->request->post['sstyle3_margin3'];
		} else {
			$this->data['sstyle3_margin3'] = $this->config->get('sstyle3_margin3');
		}
		
		if (isset($this->request->post['sstyle4_status'])) {
			$this->data['sstyle4_status'] = $this->request->post['sstyle4_status'];
		} else {
			$this->data['sstyle4_status'] = $this->config->get('sstyle4_status');
		}
		
		if (isset($this->request->post['sstyle4_prodnama'])) {
			$this->data['sstyle4_prodnama'] = $this->request->post['sstyle4_prodnama'];
		} else {
			$this->data['sstyle4_prodnama'] = $this->config->get('sstyle4_prodnama');
		}
		
		if (isset($this->request->post['sstyle4_pricecol'])) {
			$this->data['sstyle4_pricecol'] = $this->request->post['sstyle4_pricecol'];
		} else {
			$this->data['sstyle4_pricecol'] = $this->config->get('sstyle4_pricecol');
		}
		
		if (isset($this->request->post['sstyle4_oldpricecol'])) {
			$this->data['sstyle4_oldpricecol'] = $this->request->post['sstyle4_oldpricecol'];
		} else {
			$this->data['sstyle4_oldpricecol'] = $this->config->get('sstyle4_oldpricecol');
		}
		
		if (isset($this->request->post['sstyle4_bgroundcol'])) {
			$this->data['sstyle4_bgroundcol'] = $this->request->post['sstyle4_bgroundcol'];
		} else {
			$this->data['sstyle4_bgroundcol'] = $this->config->get('sstyle4_bgroundcol');
		}
		
		if (isset($this->request->post['sstyle4_bordercol'])) {
			$this->data['sstyle4_bordercol'] = $this->request->post['sstyle4_bordercol'];
		} else {
			$this->data['sstyle4_bordercol'] = $this->config->get('sstyle4_bordercol');
		}
		
		if (isset($this->request->post['sstyle4_boxprodwidth'])) {
			$this->data['sstyle4_boxprodwidth'] = $this->request->post['sstyle4_boxprodwidth'];
		} else {
			$this->data['sstyle4_boxprodwidth'] = $this->config->get('sstyle4_boxprodwidth');
		}
		
		if (isset($this->request->post['sstyle4_margin1'])) {
			$this->data['sstyle4_margin1'] = $this->request->post['sstyle4_margin1'];
		} else {
			$this->data['sstyle4_margin1'] = $this->config->get('sstyle4_margin1');
		}
		
		if (isset($this->request->post['sstyle4_margin2'])) {
			$this->data['sstyle4_margin2'] = $this->request->post['sstyle4_margin2'];
		} else {
			$this->data['sstyle4_margin2'] = $this->config->get('sstyle4_margin2');
		}
		
		if (isset($this->request->post['sstyle4_margin3'])) {
			$this->data['sstyle4_margin3'] = $this->request->post['sstyle4_margin3'];
		} else {
			$this->data['sstyle4_margin3'] = $this->config->get('sstyle4_margin3');
		}
		
		if (isset($this->request->post['sstyle5_status'])) {
			$this->data['sstyle5_status'] = $this->request->post['sstyle5_status'];
		} else {
			$this->data['sstyle5_status'] = $this->config->get('sstyle5_status');
		}
		
		if (isset($this->request->post['sstyle5_prodnama'])) {
			$this->data['sstyle5_prodnama'] = $this->request->post['sstyle5_prodnama'];
		} else {
			$this->data['sstyle5_prodnama'] = $this->config->get('sstyle5_prodnama');
		}
		
		if (isset($this->request->post['sstyle5_pricecol'])) {
			$this->data['sstyle5_pricecol'] = $this->request->post['sstyle5_pricecol'];
		} else {
			$this->data['sstyle5_pricecol'] = $this->config->get('sstyle5_pricecol');
		}
		
		if (isset($this->request->post['sstyle5_oldpricecol'])) {
			$this->data['sstyle5_oldpricecol'] = $this->request->post['sstyle5_oldpricecol'];
		} else {
			$this->data['sstyle5_oldpricecol'] = $this->config->get('sstyle5_oldpricecol');
		}
		
		if (isset($this->request->post['sstyle5_bgroundcol'])) {
			$this->data['sstyle5_bgroundcol'] = $this->request->post['sstyle5_bgroundcol'];
		} else {
			$this->data['sstyle5_bgroundcol'] = $this->config->get('sstyle5_bgroundcol');
		}
		
		if (isset($this->request->post['sstyle5_bordercol'])) {
			$this->data['sstyle5_bordercol'] = $this->request->post['sstyle5_bordercol'];
		} else {
			$this->data['sstyle5_bordercol'] = $this->config->get('sstyle5_bordercol');
		}
		
		if (isset($this->request->post['sstyle5_boxprodwidth'])) {
			$this->data['sstyle5_boxprodwidth'] = $this->request->post['sstyle5_boxprodwidth'];
		} else {
			$this->data['sstyle5_boxprodwidth'] = $this->config->get('sstyle5_boxprodwidth');
		}
		
		if (isset($this->request->post['sstyle5_margin1'])) {
			$this->data['sstyle5_margin1'] = $this->request->post['sstyle5_margin1'];
		} else {
			$this->data['sstyle5_margin1'] = $this->config->get('sstyle5_margin1');
		}
		
		if (isset($this->request->post['sstyle5_margin2'])) {
			$this->data['sstyle5_margin2'] = $this->request->post['sstyle5_margin2'];
		} else {
			$this->data['sstyle5_margin2'] = $this->config->get('sstyle5_margin2');
		}
		
		if (isset($this->request->post['sstyle5_margin3'])) {
			$this->data['sstyle5_margin3'] = $this->request->post['sstyle5_margin3'];
		} else {
			$this->data['sstyle5_margin3'] = $this->config->get('sstyle5_margin3');
		}
		// latest plus mod end //
						
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/specialplus.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/specialplus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (isset($this->request->post['specialplus_module'])) {
			foreach ($this->request->post['specialplus_module'] as $key => $value) {
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