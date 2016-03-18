<?php
################################################################################################
#  developed by tomsky http://themeforest.net/user/tomsky 		                               #
#  All rights reserved                                                                         #
################################################################################################
class ControllerModuleGentle extends Controller {
	
	private $error = array(); 
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/gentle');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));
		
		

		
		
		//Load the settings model. You can also add any other models you want to load here.
		$this->load->model('setting/setting');
		
					$this->load->model('tool/image');
	
	if (isset($this->request->post['own_image'])) {
			$this->data['own_image'] = $this->request->post['own_image'];
			$own_image = $this->request->post['own_image'];
		} else {
			$this->data['own_image'] = '';
		}
		
		if (isset($this->request->post['own_full_image'])) {
			$this->data['own_full_image'] = $this->request->post['own_full_image'];
			$own_image = $this->request->post['own_full_image'];
		} else {
			$this->data['own_full_image'] = '';
		}
		
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('gentle', $this->request->post);	

				
					
			$this->session->data['success'] = $this->language->get('text_success');
		
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
			$this->data['text_image_manager'] = 'Image manager';
					$this->data['token'] = $this->session->data['token'];		
		
		$text_strings = array(
				'heading_title',
				'text_enabled',
				'text_disabled',
				'text_content_top',
				'text_content_bottom',
				'text_column_left',
				'text_column_right',
				'entry_layout',
				'entry_position',
				'entry_status',
				'entry_sort_order',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'entry_example' 
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		

		// store config data
		
		$config_data = array(
		'gentle_status',
		'gentle_primary_c',
		'gentle_secondary_c',
		'gentle_links_c',
		'gentle_links_c_hover',
		'gentle_body_bg',
		'gentle_button_bg',
		'gentle_button_bg_hover',
		'gentle_button_c',
		'gentle_menu_bg',
		'gentle_menu_hover',
		'gentle_menu_c',
		'gentle_price_c',
		'gentle_old_price_c',
		'gentle_body_bg',
		'gentle_body_bg_pattern',
		'gentle_body_font',
		'gentle_header_font',
		'gentle_footer_b',
		'own_image',
		'gentle_preview',
		'own_bg_image',
		'own_full_image',
		'gentle_full_preview',
		'own_full_bg_image',
		'gentle_transparent_content'
		);
		
		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$this->data[$conf] = $this->request->post[$conf];
			} else {
				$this->data[$conf] = $this->config->get($conf);
			}
		}
		
		
		
	
		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
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
			'href'      => $this->url->link('module/gentle', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/gentle', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

	
		//This code handles the situation where you have multiple instances of this module, for different layouts.
		if (isset($this->request->post['gentle_module'])) {
			$modules = explode(',', $this->request->post['gentle_module']);
		} elseif ($this->config->get('gentle_module') != '') {
			$modules = explode(',', $this->config->get('gentle_module'));
		} else {
			$modules = array();
		}			
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		foreach ($modules as $module) {
			if (isset($this->request->post['gentle_' . $module . '_layout_id'])) {
				$this->data['gentle_' . $module . '_layout_id'] = $this->request->post['gentle_' . $module . '_layout_id'];
			} else {
				$this->data['gentle_' . $module . '_layout_id'] = $this->config->get('gentle_' . $module . '_layout_id');
			}	
			
			if (isset($this->request->post['gentle_' . $module . '_position'])) {
				$this->data['gentle_' . $module . '_position'] = $this->request->post['gentle_' . $module . '_position'];
			} else {
				$this->data['gentle_' . $module . '_position'] = $this->config->get('gentle_' . $module . '_position');
			}	
			
			if (isset($this->request->post['gentle_' . $module . '_status'])) {
				$this->data['gentle_' . $module . '_status'] = $this->request->post['gentle_' . $module . '_status'];
			} else {
				$this->data['gentle_' . $module . '_status'] = $this->config->get('gentle_' . $module . '_status');
			}	
						
			if (isset($this->request->post['gentle_' . $module . '_sort_order'])) {
				$this->data['gentle_' . $module . '_sort_order'] = $this->request->post['gentle_' . $module . '_sort_order'];
			} else {
				$this->data['gentle_' . $module . '_sort_order'] = $this->config->get('gentle_' . $module . '_sort_order');
			}				
		}
		

		
		$this->data['modules'] = $modules;
		
		if (isset($this->request->post['gentle_module'])) {
			$this->data['gentle_module'] = $this->request->post['gentle_module'];
		} else {
			$this->data['gentle_module'] = $this->config->get('gentle_module');
		}

		//Choose which template file will be used to display this request.
		$this->template = 'module/gentle.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
		

		
		if (isset($this->data['own_image']) && file_exists(DIR_IMAGE . $this->data['own_image'])) {
			$this->data['gentle_preview'] = $this->model_tool_image->resize($this->data['own_image'], 70, 70);
		} else {
			$this->data['gentle_preview'] = $this->model_tool_image->resize('no_image.jpg', 50, 70);
		}
		
		
		if (isset($this->data['own_full_image']) && file_exists(DIR_IMAGE . $this->data['own_full_image'])) {
			$this->data['gentle_full_preview'] = $this->model_tool_image->resize($this->data['own_full_image'], 70, 70);
		} else {
			$this->data['gentle_full_preview'] = $this->model_tool_image->resize('no_image.jpg', 50, 70);
		}

		//Send the output.
		$this->response->setOutput($this->render());
	}
	
	/*
	 * 
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 * 
	 */
	
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/gentle')) {
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