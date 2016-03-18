<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rewards_Helper_Rule extends Mage_Core_Helper_Abstract
{
    protected $_operatorInputByType = array(
        'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}'),
        'numeric' => array('==', '!=', '>=', '>', '<=', '<'),
        'date' => array('==', '>=', '<='),
        'select' => array('==', '!='),
        'boolean' => array('==', '!='),
        'multiselect' => array('{}', '!{}', '()', '!()'),
        'grid' => array('()', '!()'),
    );

    protected $_operatorOptions = array(
        '==' => 'is',
        '!=' => 'is not',
        '>=' => 'equals or greater than',
        '<=' => 'equals or less than',
        '>' => 'greater than',
        '<' => 'less than',
        '{}' => 'contains',
        '!{}' => 'does not contain',
        '()' => 'is one of',
        '!()' => 'is not one of',
    );

    public function getAttributeSelectHtml($name, $current, $style, $tags = null)
    {
        $attributes = Mage::getSingleton('rewards/system_config_source_attribute')->toOptionArray();

        $options = array();
        $options['-'][] = '<option value="">'.Mage::helper('rule')->__('not set').'</option>';

        foreach ($attributes as $attribute) {
            $selected = '';
            if ($attribute['value'] == $current) {
                $selected = 'selected="selected"';
            }
            $value = $attribute['value'];
            $group = $this->getAttributeGroup($value);

            $options[$group][] = '<option value="'.$value.'" '.$selected.'>'.$attribute['label'].'</option>';
        }

        $id = preg_replace('/[^a-zA-z_]/', '_', $name);

        $html = '<select name="'.$name.'" id="'.$id.'" style="'.$style.'" '.$tags.'>';
        foreach ($options as $group => $items) {
            if ($group == '-') {
                $html .= implode('', $items);
            } else {
                $html .= '<optgroup label="'.$group.'">';
                $html .= implode('', $items);
                $html .= '</optgroup>';
            }
        }

        $html .= '</select>';

        return $html;
    }

    public function getConditionSelectHtml($name, $current = null, $attributeCode = null)
    {
        $conditions = array();

        if ($attributeCode != null) {
            $entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($entityTypeId, $attributeCode);
            $type = 'string';
            if ($attributeCode === 'attribute_set_id') {
                $type = 'select';
            } elseif ($attributeCode === 'tracker') {
                $type = 'numeric';
            } else {
                switch ($attribute->getFrontendInput()) {
                    case 'select':
                        $type = 'select';
                        break;

                    case 'multiselect':
                        $type = 'multiselect';
                        break;

                    case 'date':
                        $type = 'date';
                        break;

                    case 'boolean':
                        $type = 'boolean';
                        break;

                    default:
                        $type = 'string';
                }
            }

            foreach ($this->_operatorInputByType[$type] as $operator) {
                $operatorTitle = Mage::helper('rule')->__($this->_operatorOptions[$operator]);
                $selected = $current == $operator ? 'selected="selected"' : '';
                $conditions[] = '<option '.$selected.' value="'.$operator.'">'.$operatorTitle.'</option>';
            }
        }

        return '<select style="width:100px" name="'.$name.'">'.implode('', $conditions).'</select>';
    }

    public function getOutputTypeHtml($name, $current, $tags = null)
    {
        $element = new Varien_Data_Form_Element_Select();
        $element
            ->setForm(new Varien_Object())
            ->setValue($current)
            ->setName($name)
            ->addData($tags)
            ->setValues(array(
                'pattern' => Mage::helper('rule')->__('Pattern'),
                'attribute' => Mage::helper('rule')->__('Attribute Value'),
            ));

        return $element->getElementHtml();
    }

    public function getAttributeValueHtml($name, $current = null, $attribute = null, $tags = null)
    {
        $html = '';

        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attribute);
        if ($attribute) {
            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                $options = array();

                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $selected = '';
                    if ($option['value'] == $current) {
                        $selected = 'selected="selected"';
                    }
                    $options[] = '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
                }

                $html = '<select style="width:250px" name="'.$name.'" '.$tags.'>';
                $html .= implode('', $options);
                $html .= '</select>';
            }
        }

        if (!$html) {
            $html = '<input style="width:244px" class="input-text" type="text" name="'.$name.'" value="'.$current.'">';
        }

        return $html;
    }

    public function getFormattersHtml($name, $value = null)
    {
        $element = new Varien_Data_Form_Element_Select();
        $element
            ->setForm(new Varien_Object())
            ->setValue($value)
            ->setName($name)
            ->setValues(array(
                '' => Mage::helper('rule')->__('Default'),
                'intval' => Mage::helper('rule')->__('Integer'),
                'price' => Mage::helper('rule')->__('Price'),
                'strip_tags' => Mage::helper('rule')->__('Strip Tags'),
            ));

        return $element->getElementHtml();
    }

    public function getAttributeGroup($attributeCode)
    {
        $group = '';

        $primary = array(
            'attribute_set',
            'attribute_set_id',
            'entity_id',
            'full_description',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'name',
            'short_description',
            'description',
            'sku',
            'status',
            'url',
            'url_key',
            'visibility',
        );

        $stock = array(
            'is_in_stock',
            'qty',
        );

        $price = array(
            'tax_class_id',
            'special_from_date',
            'special_to_date',
            'cost',
            'msrp',
        );

        if (in_array($attributeCode, $primary)) {
            $group = Mage::helper('rule')->__('Primary Attributes');
        } elseif (in_array($attributeCode, $stock)) {
            $group = Mage::helper('rule')->__('Stock Attributes');
        } elseif (in_array($attributeCode, $price) || strpos($attributeCode, 'price') !== false) {
            $group = Mage::helper('rule')->__('Prices & Taxes');
        } elseif (strpos($attributeCode, 'image') !== false || strpos($attributeCode, 'thumbnail') !== false) {
            $group = Mage::helper('rule')->__('Images');
        } elseif (substr($attributeCode, 0, strlen('custom:')) == 'custom:') {
            $group = Mage::helper('rule')->__('Custom Attributes');
        } elseif (substr($attributeCode, 0, strlen('mapping:')) == 'mapping:') {
            $group = Mage::helper('rule')->__('Mapping');
        } elseif (strpos($attributeCode, 'category') !== false) {
            $group = Mage::helper('rule')->__('Category');
        } elseif (strpos($attributeCode, 'ammeta') !== false) {
            $group = Mage::helper('rule')->__('Amasty Meta Tags');
        } else {
            $group = Mage::helper('rule')->__('Others Attributes');
        }

        return $group;
    }

    /************************/
}
