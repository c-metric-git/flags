<?php 
class SolrBridge_Searchrequest_Block_Adminhtml_Request_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array (
                    'id' => $this->getRequest()
                        ->getParam('id')
                )),
                'method' => 'post',
            	'enctype' => 'multipart/form-data',
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('search_request_form', array('legend'=>Mage::helper('searchrequest')->__('Search Request')));
        $fieldset->addField('mobile_number', 'text', array(
        		'label'     => Mage::helper('searchrequest')->__('Mobile.No'),
        		'name'      => 'mobile_number',
        		'class'		=> 'required-entry validate-phoneLax',
        ));
        $fieldset->addField('email_id', 'text', array(
        		'label'     => Mage::helper('searchrequest')->__('Email'),
        		'name'      => 'email_id',
        		'class'		=>'required-entry validate-email',
        ));
        $fieldset->addField('category', 'select', array(
        		'label'     => Mage::helper('searchrequest')->__('Category'),
        		'name'      => 'selection',
        		'class'     => 'required-entry',
        		'required'  => true,
        		'values' 	=> $this->getSelections(),
        ));
        $fieldset->addField('message', 'textarea', array(
        		'label'     => Mage::helper('searchrequest')->__('Message'),
        		'name'      => 'message',
        		'class'     => 'required-entry',
        		'required'  => true,
        ));
        if ( Mage::getSingleton('adminhtml/session')->getRequestData() )
        {
        	$form->setValues(Mage::getSingleton('adminhtml/session')->getRequestData());
        	Mage::getSingleton('adminhtml/session')->setRequestData(null);
        } elseif ( Mage::registry('request_data') ) {
        	$data = Mage::registry('request_data')->getData();
        	if(!isset($data['category']))
        	{
        		$data['category'] = 0;
        	}else{
        		$key = $this->showCate($data['category']);
        		//$stringValue = (string)$data['approve'];
        		$data['category'] = $key;
        	}
        
        	$form->setValues($data);
        }
        return parent::_prepareForm();
    }
    public function getSelections()
    {
    	return array('1'=>'Can\'t find what I want','2' => 'Technical issue with website','3' => 'Product description is incorrect', '4' => 'Feature request','5'=>'Others');
    }
    public function showCate($string)
    {
    	$collection = $this->getSelections();
    	foreach ($collection as $key => $value)
    	{
    		if(strcmp($value, $string) === 0)
    			return $key;
    	}
    	return null;
    }
}
