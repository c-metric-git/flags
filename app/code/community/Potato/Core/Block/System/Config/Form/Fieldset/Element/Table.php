<?php

class Potato_Core_Block_System_Config_Form_Fieldset_Element_Table extends Varien_Data_Form_Element_Abstract
{
    protected $_extension = array();

    public function getElementHtml()
    {
        $html = '<table id="table_extension_list" cellspacing="0" class="data"><thead><tr class="headings">'
            . '<th>' . Mage::helper('po_core')->__('Name') . '</th><th>' . Mage::helper('po_core')->__('Readme')
            . '</th><th>' . Mage::helper('po_core')->__('Version') . '</th><th>'
            . Mage::helper('po_core')->__('Update') . '</th></tr></thead>';
        foreach ($this->_extension as $extension) {
            $html .= '<tr><td class="a-left">'
                . $this->_getExtensionName($extension) . '</td><td style="width:50px" class="a-center">'
                . $this->_getExtensionReadme($extension) . '</td><td style="width:50px" class="a-center">'
                . $this->_getExtensionVersion($extension) . '</td><td class="a-left">'
                . $this->_getExtensionUpdate($extension) . '</td></tr>'
            ;
        }
        $html .= '</table>';
        return $html . $this->getAfterElementHtml();
    }

    public function addExtension($extInfo)
    {
        $this->_extension[] = $extInfo;
        return $this;
    }

    protected function _getExtensionReadme($extension)
    {
        if (!$extension['readme_url']) {
            return '';
        }
        return '<a class="po-core-doc" target="_blank" title="'
            . Mage::helper('po_core')->__('Readme for %s', $extension['title'])
            . '" href="' . $extension['readme_url'] . '"><img src="'
            . Mage::getDesign()->getSkinUrl('po_core/images/readme.png') . '"/></a>'
        ;
    }

    protected function _getExtensionVersion($extension)
    {
        $versionCSSClass = "po-core-last-version";
        if (version_compare($extension['version'], $extension['last_version']) === -1) {
            $versionCSSClass = "po-core-old-version";
        }
        return "<span class='po-core-table-version {$versionCSSClass}'>" . $extension['version'] . "</span>";
    }

    protected function _getExtensionUpdate($extension)
    {
        $html = '<p class="po-core-last-version">' . Mage::helper('po_core')->__('You have up-to-date version!');

        if (version_compare($extension['version'], $extension['last_version']) === -1) {
            $html = '<p class="po-core-old-version">' . Mage::helper('po_core')->__('Your version is outdated.');
            if ($extension['extension_url']) {
                $html .= ' <a target="_blank" title="' . Mage::helper('po_core')->__('Go to extension page for update')
                    . '" class="po-core-table-update-extension" href="' . $extension['extension_url']
                    . '"></a>'
                ;
            }
        }

        if ($extension['review_url']) {
            $html .= '<a class="po-core-review" title="' . Mage::helper('po_core')->__('Leave Review')
                . '" target="_blank" href="' . $extension['review_url'] . '">'
                . Mage::helper('po_core')->__('Leave Review') . '</a>'
            ;
        }

        $html .= '</p>';
        return $html;
    }

    protected function _getExtensionName($extension)
    {
        if (!$extension['extension_url']) {
            return '<p class="name">' . $extension['title'] . '</p>';
        }
        return '<a target="_blank" title="' . $extension['title'] . '" href="' . $extension['extension_url'] . '">'
            . $extension['title'] . '</a>'
        ;
    }

    public function getAfterElementHtml()
    {
        return '<script type="text/javascript">decorateTable("table_extension_list")</script>';
    }
}