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

class Itoris_ProductQa_Model_Mysql4_Answers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	protected $tableAnswers = 'itoris_productqa_answers';
	protected $tableQuestions = 'itoris_productqa_questions';
	protected $tableProduct = 'catalog_product_entity_varchar';
	protected $tableCustomer = 'customer_entity_varchar';
	protected $tableQuestionsVisibility = 'itoris_productqa_questions_visibility';
	protected $tableEavEntityType = 'eav_entity_type';
	protected $tableEavAttribute = 'eav_attribute';

	protected function _construct() {
		$this->_init('itoris_productqa/answers');
		$this->tableAnswers = Mage::getSingleton('core/resource')->getTableName($this->tableAnswers);
		$this->tableQuestions = Mage::getSingleton('core/resource')->getTableName($this->tableQuestions);
		$this->tableProduct = Mage::getSingleton('core/resource')->getTableName($this->tableProduct);
		$this->tableCustomer = Mage::getSingleton('core/resource')->getTableName($this->tableCustomer);
		$this->tableQuestionsVisibility = Mage::getSingleton('core/resource')->getTableName($this->tableQuestionsVisibility);
		$this->tableEavEntityType = Mage::getSingleton('core/resource')->getTableName($this->tableEavEntityType);
		$this->tableEavAttribute = Mage::getSingleton('core/resource')->getTableName($this->tableEavAttribute);
	}

	 protected function _initSelect() {
		 $this->getSelect()->from(array('main_table' => $this->tableAnswers))
					->joinLeft(
			 			array('q' => $this->tableQuestions),
						'q.id = main_table.q_id',
			 			array(
							 'question'       => 'q.content',
							'question_inappr' => 'q.inappr',
							'question_id'     => 'q.id'
						)
		 			)
		 			->joinLeft(array('eType' => $this->tableEavEntityType), "eType.entity_type_code = 'catalog_product'")
		 			->joinLeft(array('eAttr' => $this->tableEavAttribute), "eAttr.attribute_code = 'name' and eAttr.entity_type_id = eType.entity_type_id")
					->joinLeft(array('p' => $this->tableProduct), 'p.entity_id = q.product_id and p.attribute_id = eAttr.attribute_id', 'value');

		 if (Mage::registry('answersPage')) {
			 switch (Mage::registry('answersPage')) {
				 case Itoris_ProductQa_Block_Admin_Answers::PAGE_PENDING:
				 	$this->getSelect()->where('main_table.status = '. Itoris_ProductQa_Model_Answers::STATUS_PENDING);
				 	break;
				 case Itoris_ProductQa_Block_Admin_Answers::PAGE_INAPPR:
					$this->getSelect()->where('main_table.inappr = 1');
					break;
			 }
		 }

		 return $this;
	 }

	/**
	 * Select customer answers
	 *
	 * @param $id
	 * @return Itoris_ProductQa_Model_Mysql4_Answers_Collection
	 */
	public function getCustomerAnswers($id) {
		$this->getSelect()->reset(null)
			->from(array('main_table' => $this->tableAnswers),
				  array('main_table.id', 'main_table.created_datetime', 'main_table.content', 'main_table.status',)
			)
			->joinLeft(array('q' => $this->tableQuestions), 'main_table.q_id = q.id', array('question' => 'q.content', 'product_id' => 'q.product_id'))
			->joinLeft(array('eType' => $this->tableEavEntityType), "eType.entity_type_code = 'catalog_product'")
		 	->joinLeft(array('eAttr' => $this->tableEavAttribute), "eAttr.attribute_code = 'name' and eAttr.entity_type_id = eType.entity_type_id")
			->joinLeft(array('p' => $this->tableProduct), 'p.entity_id = q.product_id and p.attribute_id = eAttr.attribute_id', array('product_name' => 'p.value'))
			->joinLeft(array('v' => $this->tableQuestionsVisibility), 'v.q_id = q.id',array('store_id' => 'v.store_id'))
			->where('main_table.inappr = 0')
			->where('main_table.submitter_type =?', Itoris_ProductQa_Model_Answers::SUBMITTER_CUSTOMER)
			->where('main_table.customer_id = ?', $id)
			->where('main_table.status != ?', Itoris_ProductQa_Model_Answers::STATUS_NOT_APPROVED)
			->order('main_table.created_datetime desc');

		return $this;
	}

	/**
	 * Select question answers
	 *
	 * @param $questionId
	 * @return Itoris_ProductQa_Model_Mysql4_Answers_Collection
	 */
	public function questionAnswers($questionId) {
		$this->getSelect()->reset(null)->from(array('main_table' => $this->tableAnswers))
							->joinLeft(array('c' => $this->tableCustomer),
									'c.entity_id = main_table.customer_id and
									(c.attribute_id = 5 or c.attribute_id = 7)',
									array('user_name' => 'group_concat(c.value SEPARATOR " ")')
							)
							->where('main_table.q_id = ?', $questionId)
							->group('main_table.id')
							->order('main_table.created_datetime desc');
		return $this;
	}
}
?>
 
