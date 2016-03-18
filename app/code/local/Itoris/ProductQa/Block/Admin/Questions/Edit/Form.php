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

class Itoris_ProductQa_Block_Admin_Questions_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$question = Mage::registry('question');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

		$questionFieldset = $form->addFieldset('question_fieldset', array('legend'=>$this->__('Question Details')));

		$questionFieldset->addField('id', 'hidden', array(
                'name'  => 'id',
				'value' => $question['id'],
            )
        );

		$questionFieldset->addField('product', 'label', array(
                'name'  => 'product',
                'label' => $this->__('Product'),
                'title' => $this->__('Product'),
				'value' => array('name' => $question['product_name'],
								 'url' => Mage::getUrl('adminhtml/catalog_product/edit/id/' . $question['product_id'])
							),
            )
        )->setRenderer(new Itoris_ProductQa_Block_Admin_Renderer_Element_Link());

		$questionFieldset->addField('posted_by', 'label', array(
                'name'  => 'posted_by',
                'label' => $this->__('Posted By'),
                'title' => $this->__('Posted By'),
				'value' => array(
					'user_name'       => (isset($question['user_name'])) ? $question['user_name'] : '',
					'user_url'        => $this->getUrl('adminhtml/customer/edit/id/'. $question['customer_id'] .'/'),
					'user_email'      => (isset($question['user_email'])) ? $question['user_email'] : '',
					'user_type'       => $question['user_type'],
					'posted_on_label' => $this->__('Posted On'),
					'posted_on_date'  => $question['created_datetime'],
				),
            )
        )->setRenderer(new Itoris_ProductQa_Block_Admin_Renderer_Element_PostedBy());

		$questionFieldset->addField('rating', 'label', array(
                'name'  => 'rating',
                'label' => $this->__('Rating'),
                'title' => $this->__('Rating'),
				'value' => array(
					'good'         => $question['good'],
					'bad'          => $question['bad'],
					'inappr'       => $question['inappr'],
					'good_label'   => $this->__('helpful'),
					'bad_label'    => $this->__('not helpful'),
					'inappr_label' => $this->__('Rated as Inappropriate!'),
					'remove_flag'  => $this->__('remove flag'),
				),
            )
        )->setRenderer(new Itoris_ProductQa_Block_Admin_Renderer_Element_Rating());

		$questionFieldset->addField('status', 'select', array(
                'name'     => 'status',
                'label'    => $this->__('Status'),
                'title'    => $this->__('Status'),
				'value'    => $question['status'],
                'required' => true,
				'values'   => array(
					Itoris_ProductQa_Model_Questions::STATUS_PENDING      => $this->__('Pending'),
					Itoris_ProductQa_Model_Questions::STATUS_APPROVED     => $this->__('Approved'),
					Itoris_ProductQa_Model_Questions::STATUS_NOT_APPROVED => $this->__('Not Approved')
				),
            )
        );

        $questionFieldset->addField('visible', 'multiselect', array(
                'name'     => 'visible',
                'label'    => $this->__('Question is visible in'),
                'title'    => $this->__('Question is visible in'),
                'required' => true,
				'value'    => explode(',', $question['visible']),
				'values'   => Mage::helper('itoris_productqa/form')->getStoreSelectOptions(),
            )
        );

		$questionFieldset->addField('nickname', 'text', array(
				'name'     => 'nickname',
				'label'    => $this->__('Nickname'),
				'title'    => $this->__('Nickname'),
				'required' => true,
				'value'    => $question['nickname'],
		));

		$questionFieldset->addField('question', 'textarea', array(
				'name'     => 'question',
				'label'    => $this->__('Your Question'),
				'title'    => $this->__('Your Question'),
				'required' => true,
				'value'    => $question['content'],
				'style'    => 'height:auto;'
 		))->setRows(4);

		$block = Mage::getBlockSingleton('itoris_productqa/admin_questions_edit_grid');
		Mage::register('answersGrid',$block);

		$answersFieldset = $form->addFieldset('answers_fieldset', array('legend'=>$this->__('Answers')));
		$answersFieldset->getRenderer()->setTemplate('itoris/productqa/renderer/fieldset.phtml');

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
?>