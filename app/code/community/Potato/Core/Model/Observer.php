<?php

class Potato_Core_Model_Observer
{
    const TMP_FILE_NAME = 'info.txt';
    const TMP_FILE_PATH = '/tmp/info.txt';

    public function systemConfigSectionSaveAfter()
    {
        $formData = Mage::app()->getRequest()->getPost();
        if (!$formData || trim($formData['contact_us_name']) === "" || trim($formData['contact_us_email']) === ""
            || trim($formData['contact_us_subject']) === "" || trim($formData['contact_message']) === ""
        ) {
            return $this;
        }
        $mailTemplate = Mage::getModel('core/email_template');
        try {
            $mailTemplate
                ->sendTransactional(
                    'po_core_notification_new_question_to_support_template',
                    array('name' => $formData['contact_us_name'], 'email' => $formData['contact_us_email']),
                    Potato_Core_Helper_Data::POTATO_SUPPORT_EMAIL,
                    Potato_Core_Helper_Data::POTATO_SUPPORT_NAME,
                    array(
                        'content'              => $formData['contact_message'],
                        'subject'              => $formData['contact_us_subject'],
                        'base_url'             => Mage::getBaseUrl(),
                        'magento_version'      => Mage::getVersion(),
                        'installed_extensions' => $this->_getInstalledModules()
                    )
                )
            ;
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('po_core')->__('Message has been sent'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $response = Mage::app()->getFrontController()->getResponse();
        $response
            ->setRedirect(Mage::helper('adminhtml')->getUrl('*/*/edit',
                array('_current' => array('section', 'website', 'store')))
            )
            ->sendHeaders()
        ;
        exit;
    }

    protected function _getInstalledModules()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);
        $moduleList = "";
        foreach ($modules as $moduleName) {
            $moduleList .= $moduleName . "<br/>";
        }
        return $moduleList;
    }

    public function preparePotatoTabs($observer)
    {
        $tabsBlock = $observer->getBlock();
        if ($tabsBlock instanceof Mage_Adminhtml_Block_System_Config_Tabs) {
            foreach ($tabsBlock->getTabs() as $tab) {
                if ($tab->getId() != 'po_core' || null === $tab->getSections()) {
                    continue;
                }
                $_sections = $tab->getSections()->getItems();
                $tab->getSections()->clear();

                $_sectionLabelList = array();
                $_sectionList = array();
                foreach ($_sections as $key => $_section) {
                    if (!in_array($key, array('po_core'))) {
                        $_sectionLabelList[] = strtolower(str_replace(' ', '_', $_section->getLabel()));
                        $_sectionList[] = $_section;
                    }
                }
                array_multisort($_sectionLabelList, SORT_ASC, SORT_STRING, $_sectionList);

                foreach ($_sectionList as $_section) {
                    $tab->getSections()->addItem($_section);
                }
                if (array_key_exists('po_core', $_sections)) {
                    $tab->getSections()->addItem($_sections['po_core']);
                }
            }
        }
        return $this;
    }
}