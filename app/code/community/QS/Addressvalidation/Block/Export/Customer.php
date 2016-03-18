<?php
/**
 * Created by JetBrains PhpStorm.
 * User: inknex
 * Date: 8/28/12
 * Time: 7:11 PM
 * To change this template use File | Settings | File Templates.
 */

class QS_Addressvalidation_Block_Export_Customer  extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/address_collection')
            ->addAttributeToSelect('*')
            ->AddAttributeToFilter('validation_flag','1');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('customer')->__('First Name'),
            'index'     => 'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('Last Name'),
            'index'     => 'lastname'
        ));

        $this->addColumn('street', array(
            'header'    => Mage::helper('customer')->__('Street'),
            'index'     => 'street'
        ));

        $this->addColumn('city', array(
            'header'    => Mage::helper('customer')->__('City'),
            'index'     => 'city'
        ));

        $this->addColumn('city', array(
            'header'    => Mage::helper('customer')->__('City'),
            'index'     => 'city'
        ));

        $this->addColumn('region', array(
            'header'    => Mage::helper('customer')->__('Region'),
            'index'     => 'region'
        ));

        $this->addColumn('country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'index'     => 'country_id'
        ));

        $this->addColumn('postcode', array(
            'header'    => Mage::helper('customer')->__('Zip Code'),
            'index'     => 'postcode'
        ));

        $this->addColumn('telephone', array(
            'header'    => Mage::helper('customer')->__('Phone'),
            'index'     => 'telephone'
        ));

        $this->addColumn('validation_flag', array(
            'header'    => Mage::helper('customer')->__('Validation Flag'),
            'renderer' => 'QS_Addressvalidation_Block_Adminhtml_Export_Renderer_Validationflag',
            'index'     => 'validation_flag',


        ));

                //validation_flag
        return parent::_prepareColumns();
    }

}
