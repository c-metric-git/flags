<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Coupongenerator_Block_Adminhtml_Coupons_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $_massactionIdFilter = 'main_table.coupon_id';

    public function __construct()
    {
        parent::__construct();
        $this->setId('QG_Coupons')
            ->setDefaultSort('coupon_id')
            ->setDefaultDir('DESC')
            ->setSaveParametersInSession(true)
            ->setUseAjax(false)
        ;
    }

    protected function _prepareLayout()
    {
        $form = new Varien_Data_Form();
        $input = new Varien_Data_Form_Element_Text(
            array(
                'html_id' => 'coupon_generation_email',
                'label' => Mage::helper('coupongenerator')->__('Email'),
                'name'  => 'coupon_generation_email',
            )
        );
        $input->setForm($form);
        $select = new Varien_Data_Form_Element_Select(
            array(
                'html_id' => 'coupon_generation_rule',
                'name'  => 'coupon_generation_rule',
                'values'=> Mage::getSingleton('coupongenerator/salesrule')->toOptionArray()
            )
        );
        $select->setForm($form);
        $this->setChild('rules', $select)
            ->setChild('email', $this->getLayout()->createBlock('adminhtml/widget_form_element')->setElement($input))
        ;
        $urlToCouponGeneration = $this->getUrl('adminhtml/awqcg_coupons/generate');
        $this->setChild('generate_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id' => 'coupon_generation',
                    'label'     => Mage::helper('coupongenerator')->__('Generate'),
                    'onclick'   => "awQuickCouponGeneration.runFromCouponsGrid('$urlToCouponGeneration')",
                ))
        );
        return parent::_prepareLayout();
    }

    public function getRulesHtml()
    {
        return $this->getChild('rules')->getElementHtml();
    }

    /**
     * @return string
     */
    public function getEmailHtml()
    {
        return $this->getChildHtml('email') . $this->_getEmailAutocompleterHtml();
    }

    /**
     * @return string
     */
    protected function _getEmailAutocompleterHtml()
    {
        $url = $this->getUrl('adminhtml/awqcg_coupons/emailautocompleteAjax');
        return '<div id="awcoupon-findemail-autocomplete"></div>'
            . '<script type="text/javascript">'
            . 'new AWCouponAutocompleter("coupon_generation_email", "awcoupon-findemail-autocomplete", "' . $url . '", {'
            .     'paramName: "search",'
            .     'minChars: 3,'
            .     'tokens: ","'
            . '});'
            . '</script>'
        ;
    }

    public function getGenerateButtonHtml()
    {
        return $this->getChildHtml('generate_button');
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getRulesHtml()
              . $this->getEmailHtml()
              . $this->getGenerateButtonHtml()
              . parent::getMainButtonsHtml()
        ;
        return $html;
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesrule/coupon')->getCollection();
        $statusColumn = Mage::getResourceModel('coupongenerator/coupon_status')->getColumnExpression();
        $collection->getSelect()
            ->columns(array('status' => $statusColumn))
            ->joinLeft(
                array('scr' => $collection->getTable('salesrule/rule')),
                'main_table.rule_id = scr.rule_id',
                array('coupon_type', 'name')
            )
            ->joinInner(
                array('aw_coupons' => $collection->getTable('coupongenerator/coupon')),
                'main_table.coupon_id = aw_coupons.coupon_id',
                array('TRIM(recipient_email) AS sent_to', 'customer_id')
            )
            ->joinLeft(
                array('admins' => $collection->getTable('admin/user')),
                'aw_coupons.admin_user_id = admins.user_id',
                array('CONCAT_WS(" ", firstname, lastname) AS admin_name')
            )
            ->where('scr.coupon_type = ?', Mage::getSingleton('coupongenerator/salesrule')->getCouponType())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('coupon_id');
        $this->getMassactionBlock()->setFormFieldName('coupons');
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> $this->__('Deactivate'),
            'url'  => $this->getUrl('*/*/massExpire'),
            'confirm' => $this->__('Deactivate?')
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => $this->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Delete?')
        ));
        return $this;
    }

    /**
     * Set filter index (Magento < 1.7 issue)
     *
     * @return AW_Coupongenerator_Block_Adminhtml_Coupons_Grid
     */
    protected function _prepareMassactionColumn()
    {
        parent::_prepareMassactionColumn();
        $this->getColumn('massaction')->setFilterIndex($this->_massactionIdFilter);
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'coupon_id',
            array(
                'index' => 'coupon_id',
                'header' => $this->__('ID'),
                'width' => '100px',
                'filter_index' => 'main_table.coupon_id'
            )
        );

        $this->addColumn(
            'code',
            array(
                'index' => 'code',
                'header' => $this->__('Coupon Code')
            )
        );

        $this->addColumn(
            'rule_name',
            array(
                'index' => 'name',
                'header' => $this->__('Rule name')
            )
        );

        $this->addColumn(
            'times_used',
            array(
                'index' => 'times_used',
                'header' => $this->__('Times Used'),
                'width' => '150px',
                'filter_index' => 'main_table.times_used'
            )
        );

        $this->addColumn(
            'sent_to',
            array(
                'index' => 'sent_to',
                'header' => $this->__('Generated For'),
                'renderer' => 'coupongenerator/adminhtml_coupons_grid_column_renderer_customer',
                'filter_condition_callback' => array($this, '_filterBySentTo'),
                'sortable' => true,
            )
        );

        $this->addColumn(
            'created_by',
            array(
                'index' => 'admin_name',
                'header' => $this->__('Created By'),
                'width' => '250px',
                'filter_condition_callback' => array($this, '_filterByAdminName')
            )
        );

        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        $versionAdapter->call('addColumnsToCouponsGrid', array($this));

        $this->addColumn(
            'expiration_date',
            array(
                'index' => 'expiration_date',
                'header' => $this->__('Expiration Date'),
                'type' => 'datetime',
                'width' => '150px'
            )
        );

        $couponStatusResource = Mage::getResourceModel('coupongenerator/coupon_status');
        $this->addColumn(
            'status',
            array(
                'header' => $this->__('Status'),
                'index' => 'status',
                'width' => '100px',
                'type' => 'options',
                'options' => AW_Coupongenerator_Model_Source_Coupon_Status::toOptionHash(),
                'renderer' => 'coupongenerator/adminhtml_coupons_grid_column_renderer_status',
                'filter_condition_callback' => array($couponStatusResource, 'filter')
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => $this->__('Actions'),
                'width' => '100px',
                'getter' => 'getId',
                'renderer' => 'coupongenerator/adminhtml_coupons_grid_column_renderer_action',
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'actions' => array(
                    array(
                        'caption' => $this->__('Delete'),
                        'title'   => $this->__('Delete coupon from the system'),
                        'url' => array('base' => '*/*/delete'),
                        'field' => 'id',
                        'confirm' => $this->__('Delete?')
                    ),
                    array(
                        'caption' => $this->__('Deactivate'),
                        'title'   => $this->__('Set coupon inactive'),
                        'url' => array('base' => '*/*/expire'),
                        'field' => 'id',
                        'filter' => '_isNotExpired',
                        'confirm' => $this->__('Deactivate?')
                    ),
                ),
            )
        );
    }

    protected function _filterBySentTo($collection, $column)
    {
        if ( ! $value = trim($column->getFilter()->getValue())) {
            return $this;
        }
        if (Zend_Validate::is($value, 'EmailAddress')) {
            $collection->getSelect()->where('TRIM(aw_coupons.recipient_email) = ?', $value);
        } else {
            $customer = Mage::getModel('customer/customer')->getCollection()
                ->addExpressionAttributeToSelect('full_name',
                    'LOWER(CONCAT({{firstname}}, " ", {{lastname}}))', array('firstname', 'lastname'))
                ->addAttributeToFilter('full_name', array('like' => $value))
                ->getFirstItem()
            ;
            $collection->getSelect()->where('aw_coupons.customer_id = ?', $customer->getId());
        }
        return $this;
    }

    protected function _filterByAdminName($collection, $column)
    {
        if ( ! $value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where('CONCAT_WS(" ", firstname, lastname) LIKE ?', '%' . $value . '%');
        return $this;
    }
}
