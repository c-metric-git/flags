<?php
/**
 * MageWorx
 * Others Also Bought Extension
 *
 * @category   MageWorx
 * @package    MageWorx_AlsoBought
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_AlsoBought_Block_List extends Mage_Catalog_Block_Product_List_Related
{
    /**
     * List items
     *
     * @var array
     */
    protected $_items;

    /**
     * Prepare products list
     * @return $this
     */
    protected function _prepareData()
    {
        $this->_items = new Varien_Data_Collection();

        $products = $this->getAlsoBoughtItems();
        if (!$products) {
            return $this;
        }

        $productsInList = $count = Mage::helper('mageworx_alsobought')->getProductsNumber();
        foreach ($products as $_productId) {
            /* @var $_product Mage_Catalog_Model_Product */
            $_product = Mage::getModel('catalog/product')->load($_productId);

            if (!$this->isShowInList($_product)) {
                continue;
            }

            $_product->setDoNotUseCategoryId(true);

            if ($count-- > 0) {
                $this->_items->addItem($_product);
            } else {
                break;
            }
        }

        $relatedInList = $productsInList - $this->_items->count();
        if ($relatedInList <= 0) {
            return $this;
        }

        parent::_prepareData();
        foreach ($this->_itemCollection as $item) {
            if ($this->_items->getItemById($item->getId())) {
                continue;
            }
            if ($relatedInList-- > 0) {
                $this->_items->addItem($item);
            } else {
                break;
            }
        }

        return $this;
    }

    /**
     * Check if product is shown in list
     * @param \Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function isShowInList(Mage_Catalog_Model_Product $product)
    {
        $cartProducts = Mage::getSingleton('checkout/cart')->getQuoteProductIds();

        return $product->isVisibleInCatalog()
            && $product->isVisibleInSiteVisibility()
            && $product->isSalable()
            && $product->isInStock()
            && !in_array($product->getId(), $cartProducts);
    }

    /**
     * List items
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Get main list items
     * @return array
     */
    protected function getAlsoBoughtItems()
    {
        $product = Mage::registry('product');
        if ($product && $product->getId()) {
            $product = Mage::getModel('catalog/product')->load($product->getId());
        }
        /* @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderStatus = Mage::helper('mageworx_alsobought')->getOrderStatus();
        $sortOrder = Mage::helper('mageworx_alsobought')->getSortOrder();
        $orderCollection->addAttributeToFilter('status', array('in' => array($orderStatus)));
        if (method_exists($orderCollection->getResource(), 'getMainTable')) {
            $mainTable = $orderCollection->getResource()->getMainTable();
        } else {
            $mainTable = $orderCollection->getEntity()->getEntityTable();
        }
        if (false !== strpos($mainTable, 'flat')) {
            $mainTableAlias = 'main_table';
        } else {
            $mainTableAlias = 'e';
        }

        if ($product->getTypeId() == 'grouped') {
            $children = Mage::getSingleton('catalog/product_type_grouped')->getChildrenIds($product->getId());
            foreach ($children as $group) {
                $ids[] = implode(',', array_keys($group));
            }
            $cond1 = 'items1.product_id IN (' . implode(',', $ids) . ')';
            $cond2 = 'items2.product_id NOT IN (' . implode(',', $ids) . ')';
        } else {
            $cond1 = 'items1.product_id=' . $product->getId();
            $cond2 = 'items2.product_id <> ' . $product->getId();
        }
        $select = $orderCollection->getSelect()->reset('columns')->join(
            array('items1' => Mage::getResourceSingleton('sales/order_item')->getMainTable()),
            $mainTableAlias . '.entity_id=items1.order_id AND ' . $cond1, '')
                ->join(
            array('items2' => Mage::getResourceSingleton('sales/order_item')->getMainTable()),
            'items1.order_id = items2.order_id AND ' . $cond2,
            array('product_id' => 'items2.product_id', 'items_count' => new Zend_Db_Expr('COUNT(items2.product_id)')))
                ->group('items2.product_id')
                ->order($sortOrder == 1 ? 'items_count DESC' : 'RAND()');

        $products = $orderCollection->getResource()->getReadConnection()->fetchCol($select);
        return $products;
    }

    /**
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        if ('Related Products' == $args[0]) {
            return Mage::helper('mageworx_alsobought')->getTitle();
        }
        return call_user_func_array(array('parent',__FUNCTION__), $args);
    }
}