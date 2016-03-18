<?php
class IWD_OrderManager_Model_Logger_Abstract extends Mage_Core_Model_Abstract
{
    protected $delete_log_success = array();
    protected $delete_log_error = array();
    protected $notices = array();
    protected $changes_log = array();
    protected $order_address_log = array();
    protected $edited_order_items = array();
    protected $added_order_items = array();
    protected $ordered_items_name = array();
    protected $remove_order_items = array();

    protected $new_totals = array();
    protected $log_output = "";
    protected $log_notices = "";

    const BR = "<br/>&nbsp;";

    protected $order_params = array(
        'order_status' => "Changed status from '%s' to '%s'",
        'order_state' => "Changed state from '%s' to '%s'",
        'order_store_name' => "Changed purchased from store '%s' to '%s'",
        'created_at' => "Changed order date from '%s' to '%s'",

        'payment_method' => "Payment method was changed from '%s' to '%s'",
        'shipping_method' => "Shipping method was changed from '%s' to '%s'",
        'shipping_amount' => "Shipping amount was changed from '%s' to '%s'",

        'customer_group_id' => "Order customer group was changed from '%s' to '%s'",
        'customer_prefix' => "Order customer prefix was changed from '%s' to '%s'",
        'customer_firstname' => "Order customer first name was changed from '%s' to '%s'",
        'customer_middlename' => "Order customer middle name was changed from '%s' to '%s'",
        'customer_lastname' => "Order customer last name was changed from '%s' to '%s'",
        'customer_suffix' => "Order customer suffix was changed from '%s' to '%s'",
        'customer_email' => "Order customer e-mail was changed from '%s' to '%s'",
    );

    /* add to log */
    public function addOrderItemEdit($order_item, $description, $old, $new=null)
    {
        $description = Mage::helper('iwd_ordermanager')->__($description);
        $this->ordered_items_name[$order_item->getId()] = $order_item->getName();

        if($new === null){
            $this->edited_order_items[$order_item->getId()][] = sprintf(' - %s: "%s"', $description, $old) . self::BR;
            return;
        }

        if ($old != $new) {
            $this->edited_order_items[$order_item->getId()][] = sprintf(' - %s: "%s" to "%s"', $description, $old, $new) . self::BR;
        }


        /*if ($old != $new) {
            switch($type){
                case "currency":
                    $this->_edited_order_items[$order_item->getId()][] = " - "
                        . Mage::helper('iwd_ordermanager')->__($description) . ": "
                        . Mage::helper('core')->currency($old, true, false) . " to "
                        . Mage::helper('core')->currency($new, true, false) . self::BR;
                    break;
                case "percent":
                    $this->_edited_order_items[$order_item->getId()][] = " - "
                        . Mage::helper('iwd_ordermanager')->__($description) . ": "
                        . $old . "% to "
                        . $new . "%" . self::BR;
                    break;

                default:
                    $this->_edited_order_items[$order_item->getId()][] =
                        " - " . Mage::helper('iwd_ordermanager')->__($description) . ": '"
                        . $old . "' to '" . $new . "'" . self::BR;
            }

            $this->_ordered_items_name[$order_item->getId()] = $order_item->getName();
        }*/
    }

    public function addOrderItemAdd($order_item)
    {
        $this->added_order_items[$order_item->getId()] = $order_item->getName();
        $this->ordered_items_name[$order_item->getId()] = $order_item->getName();
    }

    public function addOrderItemRemove($order_item, $refund = false)
    {
        $this->remove_order_items[$order_item->getId()] = $refund;
        $this->ordered_items_name[$order_item->getId()] = $order_item->getName();
    }

    public function addChangesToLog($item, $old_value, $new_value)
    {
        if ($new_value != $old_value) {
            $this->changes_log[$item] = array(
                "new" => $new_value,
                "old" => $old_value,
            );
        }
    }

    public function addAddressFieldChangesToLog($address_type, $filed, $title, $old_value, $new_value)
    {
        if ($new_value != $old_value) {
            if ($filed == "region_id") {
                $filed = "region";
                $new_value = Mage::getModel('directory/region')->load($new_value)->getName();
                $old_value = Mage::getModel('directory/region')->load($old_value)->getName();

                if (isset($this->order_address_log[$address_type][$filed]['new']) && !empty($this->order_address_log[$address_type][$filed]['new'])) {
                    $new_value = $this->order_address_log[$address_type][$filed]['new'];
                }
                if (isset($this->order_address_log[$address_type][$filed]['old']) && !empty($this->order_address_log[$address_type][$filed]['old'])) {
                    $old_value = $this->order_address_log[$address_type][$filed]['old'];
                }
            }

            if ($filed == "country_id") {
                $filed = "country";
                $new_value = Mage::getModel('directory/country')->loadByCode($new_value)->getName();
                $old_value = Mage::getModel('directory/country')->loadByCode($old_value)->getName();
            }

            $this->order_address_log[$address_type][$filed] = array(
                "new" => $new_value,
                "old" => $old_value,
                "title" => $title
            );
        }
    }

    public function itemDeleteSuccess($item, $item_increment_id)
    {
        $this->delete_log_success[$item][] = $item_increment_id;
    }

    public function itemDeleteError($item, $item_increment_id)
    {
        $this->delete_log_error[$item][] = $item_increment_id;
    }

    public function addNoticeMessage($notice_id, $message)
    {
        $this->notices[$notice_id] = $message;
    }

    protected function addInfoAboutSuccessAddedItemsToMessage($item)
    {
        $count = isset($this->delete_log_success[$item]) ? count($this->delete_log_success[$item]) : 0;

        if ($count > 0) {
            if ($count == 1) {
                $message = Mage::helper('iwd_ordermanager')->__("The sale %s #%s has been deleted successfully.");
                $item_title = Mage::helper('iwd_ordermanager')->__($item);
                $message = sprintf($message, $item_title, $this->delete_log_success[$item][0]);
            } else {
                $message = Mage::helper('iwd_ordermanager')->__("%i %s have been deleted successfully: %s");
                $ids = '#' . implode(', #', $this->delete_log_success[$item]);
                $item_title = Mage::helper('iwd_ordermanager')->__($item);
                $message = sprintf($message, $count, $item_title, $ids);
            }
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        }
    }

    protected function addInfoAboutErrorAddedItemsToMessage($item)
    {
        $count = isset($this->delete_log_error[$item])? count($this->delete_log_error[$item]) : 0;

        if ($count > 0) {
            if ($count == 1) {
                $message = Mage::helper('iwd_ordermanager')->__("The sale %s #%s can not be deleted.");
                $item_title = Mage::helper('iwd_ordermanager')->__($item);
                $message = sprintf($message, $item_title, $this->delete_log_error[$item][0]);
            } else {
                $message = Mage::helper('iwd_ordermanager')->__("%i %s can not be deleted: %s");
                $ids = '#' . implode(', #', $this->delete_log_error[$item]);
                $item_title = Mage::helper('iwd_ordermanager')->__($item);
                $message = sprintf($message, $count, $item_title, $ids);
            }
            Mage::getSingleton('adminhtml/session')->addError($message);
        }
    }


    /* output */
    public function addMessageToPage()
    {
        $items = array('order', 'invoice', 'shipment', 'creditmemo');
        foreach ($items as $item) {
            $this->addInfoAboutSuccessAddedItemsToMessage($item);
            $this->addInfoAboutErrorAddedItemsToMessage($item);
        }

        foreach ($this->notices as $notice) {
            Mage::getSingleton('adminhtml/session')->addNotice($notice);
        }
    }

    public function getLogOutput($order_id = null)
    {
        $this->log_output = "";

        $this->addToLogOutputInfoAboutOrderChanges();
        $this->addToLogOutputInfoAboutOrderAddress();
        $this->addToLogOutputInfoAboutOrderItems();
        $this->addtoLogOutputInfoAboutOrderTotals($order_id);
        $this->addToLogOutputNotices();

        if(!empty($this->log_output)){
            $author = $this->addToLogOutputInfoAboutAuthor();
            return $this->log_output . $author;
        }

        return null;
    }

    protected function addToLogOutputInfoAboutAuthor()
    {
        $helper = Mage::helper('iwd_ordermanager');
        $user = Mage::getSingleton('admin/session')->getUser();

        if (!empty($user)) {
            $message = $helper->__("Order was edited by %s %s (%s)");
            $message = sprintf($message, $user->getFirstname(), $user->getLastname(), $user->getUsername());
        } else {
            $message = $helper->__("Order was edited");
        }

        return "<i>{$message}</i>" . self::BR;
    }

    protected function addToLogOutputInfoAboutOrderChanges()
    {
        $helper = Mage::helper('iwd_ordermanager');
        foreach ($this->order_params as $item_code => $item_message) {
            if (isset($this->changes_log[$item_code])) {
                $this->log_output .= sprintf($helper->__($item_message), $this->changes_log[$item_code]['old'], $this->changes_log[$item_code]['new']) . self::BR;
            }
        }
    }

    protected function addToLogOutputInfoAboutOrderAddress()
    {
        $helper = Mage::helper('iwd_ordermanager');

        foreach (array("billing", "shipping") as $address_type) {
            if (isset($this->order_address_log[$address_type]) && !empty($this->order_address_log[$address_type])) {
                $this->log_output .= $helper->__("Order {$address_type} address updated: ") . self::BR;
                foreach ($this->order_address_log[$address_type] as $id => $field) {
                    $this->log_output .= sprintf(' - %s from "%s" to "%s"', $field['title'], $field['old'], $field['new']) . self::BR;
                }
            }
        }
    }

    protected function addToLogOutputInfoAboutOrderItems()
    {
        $helper = Mage::helper('iwd_ordermanager');

        /*** add order items ***/
        if (!empty($this->added_order_items)) {
            foreach ($this->added_order_items as $item_id => $item_name) {
                $this->log_output .= "<b>{$item_name}</b> {$helper->__('was added')}" . self::BR;
            }
        }

        /*** edit order items ***/
        if (!empty($this->edited_order_items)) {
            foreach ($this->edited_order_items as $item_id => $_edited) {
                $this->log_output .= '<b>' . $this->ordered_items_name[$item_id] . '</b> ' . $helper->__('was edited') . ':' . self::BR;
                foreach ($_edited as $e) {
                    $this->log_output .= $e;
                }
            }
        }

        /*** remove order items ***/
        if (!empty($this->remove_order_items)) {
            foreach ($this->remove_order_items as $item_id => $refunded) {
                $message = ($refunded) ? $helper->__('was removed (refunded)') : $helper->__('was removed');
                $this->log_output .= "<b>{$this->ordered_items_name[$item_id]}</b> {$message}" . self::BR;
            }
        }
    }

    public function addtoLogOutputInfoAboutOrderTotals($order_id)
    {
        if (empty($order_id) || empty($this->new_totals)) {
            return;
        }

        $order = Mage::getModel('sales/order')->load($order_id);
        $helper = Mage::helper('iwd_ordermanager');

        $this->log_output .= self::BR .
            $helper->__('Old grand total: ') . Mage::helper('core')->currency($order->getBaseGrandTotal(), true, false) . self::BR .
            $helper->__('New grand total: ') . Mage::helper('core')->currency($this->new_totals['base_grand_total'], true, false) . self::BR .
            $helper->__('Changes: ') . Mage::helper('core')->currency($this->new_totals['base_grand_total'] - $order->getBaseGrandTotal(), true, false) . self::BR;
    }

    public function addNewTotalsToLog($totals)
    {
        $this->new_totals = $totals;
    }

    public function addNoticeToLog($message)
    {
        $this->log_notices .= $message  .  self::BR ;
    }

    public function addToLogOutputNotices()
    {
        if(empty($this->log_notices)){
            return $this->log_output;
        }

        return $this->log_output .= self::BR . $this->log_notices;
    }
}