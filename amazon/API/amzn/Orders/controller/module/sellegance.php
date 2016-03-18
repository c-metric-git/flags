<?php

class ControllerModuleSellegance extends Controller {
    
    private $error = array(); 
    
    public function index() {   
        //Load the language file for this module
        $language = $this->load->language('module/sellegance');
        $this->data = array_merge($this->data, $language);

        //Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));
        
        //Load the settings model. You can also add any other models you want to load here.
        $this->load->model('setting/setting');
        
        $this->load->model('tool/image');    
        
        //Save the settings if the user has submitted the admin form (ie if someone has pressed save).
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('sellegance', $this->request->post);    
               
                    
            $this->session->data['success'] = $this->language->get('text_success');
        
                        
            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
            $this->data['text_image_manager'] = 'Image manager';
                    $this->data['token'] = $this->session->data['token'];       

            $this->data['sellegance_footer_info_text'] = $this->language->get('sellegance_footer_info_text');
        
        $text_strings = array(
                'heading_title',
                'text_enabled',
                'text_disabled',
                'text_content_top',
                'text_content_bottom',
                'text_column_left',
                'text_column_right',
                'entry_status',
                'entry_sort_order',
                'button_save',
                'button_cancel',
        );
        
        foreach ($text_strings as $text) {
            $this->data[$text] = $this->language->get($text);
        }
        

        // store config data
        
        $config_data = array(

        'sellegance_status',

        'sellegance_skins',

        'sellegance_custom_colors',

        'sellegance_background_color',
        'sellegance_thickbar_color',

        'sellegance_title_color',
        'sellegance_bodytext_color',
        'sellegance_lighttext_color',

        'sellegance_menu_color',
        'sellegance_menu_color',
        'sellegance_menu_hover',
        'sellegance_dropdown_color',
        'sellegance_dropdown_hover',

        'sellegance_links_color',

        'sellegance_footer_text_color',
        'sellegance_footer_links_color',
        'sellegance_content_links_color',

        'sellegance_button_top_color',
        'sellegance_button_bottom_color',
        'sellegance_button_border_color',
        'sellegance_button_text_color',

        'sellegance_2button_top_color',
        'sellegance_2button_bottom_color',
        'sellegance_2button_border_color',
        'sellegance_2button_text_color',

        'sellegance_product_name_color',
        'sellegance_normal_price_color',
        'sellegance_old_price_color',
        'sellegance_new_price_color',
        'sellegance_onsale_background_color',
        'sellegance_onsale_text_color',

        'sellegance_hide_wishlist',
        'sellegance_hide_compare',

        'sellegance_categories_menu_color',
        'sellegance_categories_sub_color',
        'sellegance_categories_active_color',

        'sellegance_pattern_overlay',
        'sellegance_custom_image',
        'sellegance_custom_pattern',
        'sellegance_image_preview',
        'sellegance_pattern_preview',
        
        'sellegance_title_font',
        'sellegance_body_font',
        'sellegance_small_font',

        'sellegance_featured_carousel',
        'sellegance_bestseller_carousel',
        'sellegance_latest_carousel',
        'sellegance_special_carousel',

        'sellegance_facebook_id',
        'sellegance_facebook_page',
        'sellegance_twitter_username',
        'sellegance_gplus_id',
        'sellegance_youtube_username',
        'sellegance_tumblr_username',
        'sellegance_skype_username',
        'sellegance_pinterest_id',

        'sellegance_footer_info_text',

        'sellegance_payment_logos',
        'sellegance_footer_info_text',
        'sellegance_copyright',

        'sellegance_slider',

        'sellegance_responsive',
        'sellegance_cloud_zoom',
        'sellegance_subcat_thumbs',
        'sellegance_display_shadows',
        'sellegance_logo_left',
        'sellegance_search_navbar',

        'sellegance_custom_stylesheet',
        'sellegance_custom_css',

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
        
        //Set up breadcrumb trail.
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
            'href'      => $this->url->link('module/sellegance', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        
        $this->data['action'] = $this->url->link('module/sellegance', 'token=' . $this->session->data['token'], 'SSL');
        
        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
                
        $this->load->model('design/layout');
        
        $this->data['layouts'] = $this->model_design_layout->getLayouts();

        $this->load->model('localisation/language');
        
        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        
        if (isset($this->request->post['sellegance_module'])) {
            $this->data['sellegance_module'] = $this->request->post['sellegance_module'];
        } else {
            $this->data['sellegance_module'] = $this->config->get('sellegance_module');
        }

        //Choose which template file will be used to display this request.
        $this->template = 'module/sellegance.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        if (isset($this->data['sellegance_custom_pattern']) && $this->data['sellegance_custom_pattern'] != "" && file_exists(DIR_IMAGE . $this->data['sellegance_custom_pattern'])) {
            $this->data['sellegance_pattern_preview'] = $this->model_tool_image->resize($this->data['sellegance_custom_pattern'], 70, 70);
        } else {
            $this->data['sellegance_pattern_preview'] = $this->model_tool_image->resize('no_image.jpg', 70, 70);
        }
        
        
        if (isset($this->data['sellegance_custom_image']) && $this->data['sellegance_custom_image'] != "" && file_exists(DIR_IMAGE . $this->data['sellegance_custom_image'])) {
            $this->data['sellegance_image_preview'] = $this->model_tool_image->resize($this->data['sellegance_custom_image'], 70, 70);
        } else {
            $this->data['sellegance_image_preview'] = $this->model_tool_image->resize('no_image.jpg', 70, 70);
        }

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 70, 70);

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
        if (!$this->user->hasPermission('modify', 'module/sellegance')) {
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