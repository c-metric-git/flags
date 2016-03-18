<?php
class IWD_OrderManager_Model_Notify_Notification extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order/copy_method';

    const EMAIL_NOTIFY_TEMPLATE = 'iwd_ordermanager/edit/transaction_email';
    const EMAIL_NOTIFY_TEMPLATE_GUEST = 'iwd_ordermanager/edit/transaction_email_guest';
    const EMAIL_CONFIRM_TEMPLATE = 'iwd_ordermanager/edit/confirm_transaction_email';
    const EMAIL_CONFIRM_TEMPLATE_GUEST = 'iwd_ordermanager/edit/confirm_transaction_email_guest';

    protected $order = null;
    protected $message = "";
    protected $template_params = array();
    protected $template = null;
    protected $template_guest = null;
    protected $email = null;

    public function sendConfirmEmail($order_id, $log, $message = null)
    {
        $this->order = Mage::getModel('sales/order')->load($order_id);
        $store_id = $this->order->getStore()->getId();
        $cancel_link = Mage::getUrl('iwd_order_manager/confirm/edit', array('action'=>'cancel', 'pid'=>$log->getConfirmLink(), "_store"=>$store_id));
        $confirm_link = Mage::getUrl('iwd_order_manager/confirm/edit', array('action'=>'confirm', 'pid'=>$log->getConfirmLink(), "_store"=>$store_id));

        $this->message = $message;
        $this->email = $log->customer_email;
        $this->template_params = array(
            "note_message" => $message,
            "changes_log"=> $log->getLogOperations(),
            "cancel_link"=> $cancel_link,
            "confirm_link"=> $confirm_link
        );
        $this->template = self::EMAIL_CONFIRM_TEMPLATE;
        $this->template_guest = self::EMAIL_CONFIRM_TEMPLATE_GUEST;

        return $this->sendEmailBase();
    }

    public function sendNotifyEmail($order_id, $email, $message = "")
    {
        $this->order = Mage::getModel('sales/order')->load($order_id);
        $this->message = $message;
        $this->email = $email;
        $this->template_params = array(
            'note_message' => $message
        );
        $this->template = self::EMAIL_NOTIFY_TEMPLATE;
        $this->template_guest = self::EMAIL_NOTIFY_TEMPLATE_GUEST;

        return $this->sendEmailBase();
    }

    protected function sendEmailBase()
    {
       try {
            $store_id = $this->order->getStore()->getId();

            if ($this->order->getCustomerIsGuest()) {
                $template = Mage::getStoreConfig($this->template_guest, $store_id);
                $customer_name = $this->order->getBillingAddress()->getName();
            } else {
                $template = Mage::getStoreConfig($this->template, $store_id);
                $customer_name = $this->order->getCustomerName();
            }

            $this->template_params['order'] = $this->order;
            $this->template_params['billing'] = $this->order->getBillingAddress();
            $this->template_params['payment_html'] = $this->paymentBlockHtml();

            $this->sendEmail($template, $customer_name);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return false;
        }
        return true;
    }
    protected function sendEmail($template, $customer_name)
    {
        $store_id = $this->order->getStore()->getId();
        $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store_id);

        $copy_to_emails = $this->getEmails();
        $customer_email = array_shift($copy_to_emails);
        $copy_method = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $store_id);

        $mailer = Mage::getModel('core/email_template_mailer');

        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customer_email, $customer_name);

        /** bcc **/
        if ($copy_to_emails && $copy_method == 'bcc') {
            foreach ($copy_to_emails as $email) {
                if(!empty($email)) {
                    $emailInfo->addBcc($email);
                }
            }
        }
        $mailer->addEmailInfo($emailInfo);

        /** copy **/
        if ($copy_to_emails && $copy_method == 'copy') {
            foreach ($copy_to_emails as $email) {
                if(!empty($email)) {
                    $emailInfo = Mage::getModel('core/email_info');
                    $emailInfo->addTo($email);
                    $mailer->addEmailInfo($emailInfo);
                }
            }
        }

        $mailer->setSender($sender);
        $mailer->setStoreId($store_id);
        $mailer->setTemplateId($template);
        $mailer->setTemplateParams($this->template_params);
        $mailer->send();
    }

    protected function getEmails()
    {
        $store_id = $this->order->getStore()->getId();
        $data = $this->email . "," . Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_TO, $store_id);

        if (!empty($data) && strlen($data)>5) {
            return explode(',', $data);
        }

        $email = $this->order->getCustomerEmail();
        return array($email);
    }

    protected function paymentBlockHtml()
    {
        $store_id = $this->order->getStore()->getId();
        $payment = $this->order->getPayment();

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store_id);

        try {
            $paymentBlock = Mage::helper('payment')->getInfoBlock($payment)->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($store_id);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            Mage::log($exception->getMessage(), null, 'iwd_order_manager.log');
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $paymentBlockHtml;
    }
}