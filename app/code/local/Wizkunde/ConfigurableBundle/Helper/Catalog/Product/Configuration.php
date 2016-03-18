<?php

class Wizkunde_ConfigurableBundle_Helper_Catalog_Product_Configuration extends Mage_Bundle_Helper_Catalog_Product_Configuration {
    const BUNDLE_HAS_CUSTOM_OPTION    = 'bundle_has_custom_options';
    const BUNDLE_SIMPLE_CUSTOM_OPTION = 'bundle_simple_custom_options';

    public function hasCustomOptions(Mage_Sales_Model_Order_Item $item) {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (!isset($options['info_buyRequest']))
            return null;

        $infoBuyRequest = $options['info_buyRequest'];

        return (isset($infoBuyRequest[self::BUNDLE_HAS_CUSTOM_OPTION])) ? $infoBuyRequest[self::BUNDLE_SIMPLE_CUSTOM_OPTION] : null;
    }

    public function getBuyRequest(Mage_Sales_Model_Order_Item $item) {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOptions();
            foreach($options as $option) {
                if($option['code'] == 'info_buyRequest') {
                    return unserialize($option['value']);
                }
            }
        }
        if (!isset($options['info_buyRequest']))
            return null;

        return $options['info_buyRequest'];
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $options = array();
        $product = $item->getProduct();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /**
             * @var Mage_Bundle_Model_Mysql4_Option_Collection
             */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue()), $product
            );

            // Show simple custom options in cart page
            $custom_options = array();
            $customOptions = $product->getCustomOption('bundle_simple_custom_options');
            if ($customOptions) {
                $custom_options = unserialize($customOptions->getValue());                
            }
            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
                    $option = array(
                        'label' => $bundleOption->getTitle(),
                        'value' => array()
                    );

                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $optionvalues = '';
                        $selectionAttributes = $this->getSelectionAttributes($bundleSelection);
                        
                        if (!empty($custom_options)) {
                            $optionvalues = $this->getSimpleCustomOptionsValues($custom_options, $bundleSelection);
                        }
                        $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                        if ($qty) {
                            $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('core')->currency(
                                            $this->getSelectionFinalPrice($item, $bundleSelection)) .
                                    $selectionAttributes . $optionvalues ;
                        }
                    }

                    if ($option['value']) {
                        $options[] = $option;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * @param $bundleSelection
     */
    public function getSelectionAttributes($bundleSelection) {
        $cart = Mage::getModel('checkout/cart')->getQuote();

        $optionvalues = '';

        foreach ($cart->getAllItems() as $item) {
            if($item->getProduct()->getTypeId() == 'configurable') {
                $productId = $item->getProduct()->getEntityId();
                if ($productId != $bundleSelection->getEntityId())
                    continue;

                $buyRequest = $this->getBuyRequest($item);

                if($buyRequest != null && isset($buyRequest['super_attribute']) && count($buyRequest['super_attribute'] > 0)) {
                    $optionvalues = '<div style="clear: both;margin-left: 100px;padding-bottom: 10px;">';

                    foreach($buyRequest['super_attribute'] as $attributeId => $attributeValue) {
                        $attr = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

                        $optionvalues .= $attr->getStoreLabel() . ': ' . $attr->getSource()->getOptionText($attributeValue) . '<br />';
                    }

                    $optionvalues .= '</div>';
                }
            }
        }

        return $optionvalues;
    }

    /**
     * Get simple's custom option of bundle product
     * @return custom options $html
     */
    public function getSimpleCustomOptionsValues($simpleCustomOptions, $selectionProduct) {

        $cart = Mage::getModel('checkout/cart')->getQuote();
        $optionValues = '';
        foreach ($cart->getAllItems() as $item) {
            $productId = $item->getProduct()->getEntityId();
            if ($productId != $selectionProduct->getEntityId())
                continue;
            $options = array();
            foreach ($simpleCustomOptions as $sid => $info) {
                if ($sid != $productId)
                    continue;
            }
            $optionIds = array_keys($simpleCustomOptions[$sid]['options']);
            if ($optionIds) {
                foreach ($optionIds as $optionId) {
                    $option = $this->getSimpleOption($item, $optionId);
                    if ($option) {
                        $itemOption = $item->getProduct()->getCustomOption('option_' . $optionId);
                        $options[] = $this->htmlEscapeOptions($item, $option, $itemOption);
                    }
                }
            }
        }//End loop cart item

        $optionValues = '<dl class="item-options">';
        foreach ($options as $_option) {
            $optionValues .= '<dt> ' . $this->htmlEscape($_option['label']) . '</dt><dd>'
                    . $this->htmlEscape($_option['value']) . '</dd>';
        }
        $optionValues .= '</dl>';
        return $optionValues;
    }

    public function getSimpleOption($item, $optionId) {
        $s_option = Mage::getModel('catalog/product')
                ->load($item->getProduct()->getEntityId())
                ->getProductOptionsCollection();
        foreach ($s_option as $o) {
            if ($optionId != $o->getOptionId())
                continue;
            return $o;
        }
        return null;
    }

    /**
     * Get bundled item (slections-products collection)
     *
     * Returns string value of options objects.
     * Each option object will contain array of selections objects
     *
     * @return string
     */
    public function getBundleOrderItemOptions(Mage_Sales_Model_Order_Item $item) {

        $custom_options = $this->hasCustomOptions($item);
        if (!$custom_options || (!is_array($custom_options))) {
            return '';
        }        
        
        $productId = $item->getProduct()->getEntityId();

        foreach ($custom_options as $product_id => $opts) {
            if ($productId != $product_id)
                continue;
            $optionIds = array_keys($custom_options[$product_id]['options']);
            foreach ($optionIds as $optionId) {
                $option = $this->getSimpleOption($item, $optionId);
                $values = $custom_options[$productId]['options'][$optionId];
                /**
                 * @var Catalog_Product_Configuration_Item_Option
                 */
                $confItemOption = $this->getConfItemOption($item, $option, $values);
                $optionsHtml[] = $this->htmlEscapeOptions($item, $option, $confItemOption);
            }
        }
        if(empty($optionsHtml)) 
            return '';
        
        $optionValues = '<dl class="item-options">';
        foreach ($optionsHtml as $_option) {
            $optionValues .= '<dt> ' . $this->htmlEscape($_option['label']) . '</dt><dd>'
                    . $this->htmlEscape($_option['value']) . '</dd>';
        }
        $optionValues .= '</dl>';
        return $optionValues;
    }

    public function getConfItemOption($item, $option, $value) {
        
        return Mage::getModel('catalog/product_configuration_item_option')
                        ->addData(array(
                            'product_id' => $item->getProduct()->getId(),
                            'product' => $item->getProduct(),
                            'code' => 'option_' . $option->getId(),
                            'value' => is_array($value) ? implode(',', $value) : $value,
                        ));
    }

    public function htmlEscapeOptions($item, $option ,$confItemOption) {              
        $group = $option->groupFactory($option->getType())
                ->setOption($option)
                ->setConfigurationItem($item)
                ->setConfigurationItemOption($confItemOption);

        if ('file' == $option->getType()) {
            $downloadParams = $item->getFileDownloadParams();
            if ($downloadParams) {
                $url = $downloadParams->getUrl();
                if ($url) {
                    $group->setCustomOptionDownloadUrl($url);
                }
                $urlParams = $downloadParams->getUrlParams();
                if ($urlParams) {
                    $group->setCustomOptionUrlParams($urlParams);
                }
            }
        }

        return array(
            'label' => $option->getTitle(),
            'value' => $group->getFormattedOptionValue($confItemOption->getValue()),
            'print_value' => $group->getPrintableOptionValue($confItemOption->getValue()),
            'option_id' => $option->getId(),
            'option_type' => $option->getType(),
            'custom_view' => $group->isCustomizedView()
        );
    }

}
