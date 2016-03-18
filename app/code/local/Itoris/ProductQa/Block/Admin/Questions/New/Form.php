<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTQA
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class Itoris_ProductQa_Block_Admin_Questions_New_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$products = array();
		$collection = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToSelect('sku')
						->addAttributeToSelect('name')
						->addAttributeToSort('name', 'ASC');
		foreach ($collection as $item) {
			$products[] = array(
							'value' => $item->getEntityId(),
							'label' => $item->getName() . ' (sku: ' . $item->getSku() . ')',
							'title' => $item->getName(),
			);
		}

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/add'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->__('Question Details')));

		$fieldset->addField('product', 'multiselect', array(
                'name'     => 'product',
                'label'    => $this->__('Product'),
                'title'    => $this->__('Product'),
                'required' => true,
				'values'   => $products,
				'style'    => 'width: 400px',
            )
        );

		$fieldset->addField('status', 'select', array(
                'name'     => 'status',
                'label'    => $this->__('Status'),
                'title'    => $this->__('Status'),
                'required' => true,
				'values'   => array(
					Itoris_ProductQa_Model_Questions::STATUS_PENDING => $this->__('Pending'),
					Itoris_ProductQa_Model_Questions::STATUS_APPROVED => $this->__('Approved'),
					Itoris_ProductQa_Model_Questions::STATUS_NOT_APPROVED => $this->__('Not Approved')
				),
				'style'    => 'width: 400px',
            )
        );

        $fieldset->addField('visible', 'multiselect', array(
                'name'     => 'visible',
                'label'    => $this->__('Question is visible in'),
                'title'    => $this->__('Question is visible in'),
                'required' => true,
				'values'   => Mage::helper('itoris_productqa/form')->getStoreSelectOptions(),
				'style'    => 'width: 400px',
            )
        );

		$fieldset->addField('nickname', 'text', array(
				'name'     => 'nickname',
				'label'    => $this->__('Nickname'),
				'title'    => $this->__('Nickname'),
				'required' => true,
				'style'    => 'width: 400px',
		));

		$fieldset->addField('question', 'text', array(
				'name'     => 'question',
				'label'    => $this->__('Your Question'),
				'title'    => $this->__('Your Question'),
				'required' => true,
				'style'    => 'width: 400px',
		));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
?>