<?php
####################################################################################
#  Any Feed for Opencart 1.5.x From HostJars http://opencart.hostjars.com  		   #
####################################################################################
class ControllerFeedAnyFeed extends Controller {
	
	private $error = array(); 
	
	public function index() {   
		//LOAD LANGUAGE
		$this->load->language('feed/any_feed');

		//SET TITLE
		$this->document->setTitle($this->language->get('heading_title'));
		
		//LOAD SETTINGS
		$this->load->model('setting/setting');
		
		//SAVE SETTINGS (when form submitted)
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('any_feed', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
			$this->session->data['success'] = $this->language->get('text_success') . ' Click here to view it: <a href="' . HTTP_CATALOG . 'index.php?route=feed/any_feed' . '" target="_blank">' . HTTP_CATALOG . 'index.php?route=feed/any_feed' . '</a>';

			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}

		//LANGUAGE
		$text_strings = array(
				'heading_title',
				'text_feed',
				'text_enabled',
				'text_disabled',
				'text_module',
				'text_success',
				'entry_status',
				'entry_include_fields',
				'entry_exclude_fields',
				'entry_format',
				'entry_delimiter',
				'entry_headings',
				'entry_data_feed',
				'entry_cdata',
				'entry_choose_fields',
				'entry_sub_text',
				'tab1',
				'tab2',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'error_permission'
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		//END LANGUAGE
		
		
		//CONFIG
		$config_data = array(
			'any_feed_status',
			'any_feed_delimiter',
			'any_feed_cdata',
			'any_feed_format',
			'any_feed_order',
			'any_feed_status',
		);
		
		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$this->data[$conf] = $this->request->post[$conf];
			} else {
				$this->data[$conf] = $this->config->get($conf);
			}
		}
	
		//ERROR
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		
		//BREADCRUMB TRAIL
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('feed/any_feed', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('feed/any_feed', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

	
		// FIELDS:
		$this->data['any_feed_all_fields'] = array(
			array(
				'id' => 'any_feed_field_product_id',
				'default_name'=>$this->language->get('Product_id'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_url',
				'default_name'=>$this->language->get('Url'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_name',
				'default_name'=>$this->language->get('Name'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_image',
				'default_name'=>$this->language->get('Image'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_price',
				'default_name'=>$this->language->get('Price'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_desc',
				'default_name'=>$this->language->get('Desc'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_cat',
				'default_name'=>$this->language->get('Category'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_manu',
				'default_name'=>$this->language->get('Manufacturer'),
				'order'=>0
				),
			array(
				'id' => 'any_feed_field_model',
				'default_name'=>$this->language->get('Model'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_sku',
				'default_name'=>$this->language->get('Sku'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_quantity',
				'default_name'=>$this->language->get('Quantity'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_added',
				'default_name'=>$this->language->get('Added'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_views',
				'default_name'=>$this->language->get('Views'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_specialprice',
				'default_name'=>$this->language->get('Special_price'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_stockstatus',
				'default_name'=>$this->language->get('Stock_status'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_length',
				'default_name'=>$this->language->get('Length'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_width',
				'default_name'=>$this->language->get('Width'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_height',
				'default_name'=>$this->language->get('Height'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_upc',
				'default_name'=>$this->language->get('Upc'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_location',
				'default_name'=>$this->language->get('Location'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_points',
				'default_name'=>$this->language->get('Points'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_date_available',
				'default_name'=>$this->language->get('Date_available'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_weight',
				'default_name'=>$this->language->get('Weight'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_shipping',
				'default_name'=>$this->language->get('Shipping'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_status',
				'default_name'=>$this->language->get('Status'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_date_modified',
				'default_name'=>$this->language->get('Date_modified'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_meta_keyword',
				'default_name'=>$this->language->get('Meta_keyword'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_discount',
				'default_name'=>$this->language->get('Discount'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_special',
				'default_name'=>$this->language->get('Special'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_reward',
				'default_name'=>$this->language->get('Reward'),
				'order'=>0
			),
			array(
				'id' => 'any_feed_field_rating',
				'default_name'=>$this->language->get('Rating'),
				'order'=>0
			),
		);
		
		$order = explode(",", $this->data['any_feed_order']);
		$rev_order = array();
		foreach ($order as $key=>$value) {
			$rev_order[$value] = $key;
		}
		
		foreach ($this->data['any_feed_all_fields'] as $key => $value) {
			if (isset($rev_order[$value['id']])) {
				$this->data['any_feed_all_fields'][$key]['order'] = $rev_order[$value['id']] + 1;
			}
			if ($this->config->get($value['id']."name")) {
				$this->data['any_feed_all_fields'][$key]['name'] = $this->config->get($value['id']."name");
			}
		}
		usort($this->data['any_feed_all_fields'], array('ControllerFeedAnyFeed', 'cmp'));

		$this->data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/any_feed';

		//Choose which template file will be used to display this request.
		$this->template = 'feed/any_feed.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		//Send the output.
		$this->response->setOutput($this->render());
	}
	
	
	static function cmp($a, $b)
	{
	    if ($a['order'] == $b['order']) {
	        return 0;
	    }
	    return ($a['order'] < $b['order']) ? -1 : 1;
	}


	private function validate() {
		if (!$this->user->hasPermission('modify', 'feed/any_feed')) {
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