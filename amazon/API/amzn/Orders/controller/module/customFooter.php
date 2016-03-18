<?php
class ControllerModuleCustomFooter extends Controller
{
    private $error = array();

    public function index()
    {

        //Load the language file for this module
        $this->load->language('module/customFooter');


        //Set the title from the language file $_['heading_title'] string
        $this->document->setTitle($this->language->get('heading_title'));

        //Load the settings model. You can also add any other models you want to load here.
        $this->load->model('setting/setting');

        //Save the settings if the user has submitted the admin form (ie if someone has pressed save).
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {


            $this->model_setting_setting->editSetting('customFooter', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');


            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->
                session->data['token']);
        }

        //This is how the language gets pulled through from the language file.
        //
        // If you want to use any extra language items - ie extra text on your admin page for any reason,
        // then just add an extra line to the $text_strings array with the name you want to call the extra text,
        // then add the same named item to the $_[] array in the language file.
        //
        // 'my_module_example' is added here as an example of how to add - see admin/language/english/module/my_module.php for the
        // other required part.

        $text_strings = array('heading_title', 'text_enabled', 'text_disabled',
            'text_left', 'text_right', 'text_home', 'entry_position', 'entry_status',
            'entry_sort_order', 'button_save', 'button_cancel'
                        //this is an example extra field added
            );

        foreach ($text_strings as $text) {
            $this->data[$text] = $this->language->get($text);
        }

        //END LANGUAGE


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }


        //SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER .
            'index.php?route=common/home&token=' . $this->session->data['token'], 'text' =>
            $this->language->get('text_home'), 'separator' => false);

        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER .
            'index.php?route=extension/module&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_module'), 'separator' => ' :: ');

        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER .
            'index.php?route=module/customFooter&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'), 'separator' => ' :: ');

        $this->data['action'] = HTTPS_SERVER . 'index.php?route=module/customFooter&token=' .
            $this->session->data['token'];

        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/module&token=' .
            $this->session->data['token'];


        // This array allows your module to appear in one of three positions: The home page, or the left or right column.
        // If yours is just a home page or just a left or just a right column, then remove the unwanted blocks of code here
        // to remove other choices
  


        //The following code pulls in the required data from either config files or user
        //submitted data (when the user presses save in admin). Add any extra config data
        // you want to store.
        //
        // NOTE: These must have the same names as the form data in your my_module.tpl file
        //
        $config_data = array(
        'customFooter_status',
        'about_status',
		'about_header', 
		'about_text',
        'contact_header',
	    'contact_status',
        'telephone1',
        'telephone2',
        'email1',
        'email2',
        'skype',
        'fax',
        'facebook_status',
        'facebook_id',
        'custom_footer_column_status',
        'custom_footer_column_header',
        'custom_footer_column_content',
          'twitter_column_status',
          'twitter_column_header',
        'twitter_number_of_tweets',
        'twitter_username',
        'categories_column_status'
        					);

        foreach ($config_data as $conf) {
            if (isset($this->request->post[$conf])) {
                $this->data[$conf] = $this->request->post[$conf];
            } else {
                $this->data[$conf] = $this->config->get($conf);
            }
        }

        //Choose which template file will be used to display this request, and send the output.
        $this->template = 'module/customFooter.tpl';
        $this->children = array('common/header', 'common/footer');

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }


    /*
    * 
    * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
    * You can add checks in here of your own.
    * 
    */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/customFooter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //Example validation check: makes sure the user has selected a position.
        //NOTE: You shouldn't use a string like I have for the warning, you should set this string in the language file
        // and call it from there.

        //		elseif (!isset($this->request->post('latest_position'))) {
        //			$this->error['warning'] = "You haven't set a position";
        //		}

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>