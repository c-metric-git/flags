<?php
/**
 * Created by JetBrains PhpStorm.
 * User: db
 * Date: 8/30/12
 * Time: 2:28 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Addressvalidation_Block_Export_Renderer_Validationflag extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
      $row;
       mage::log($row);
        return $row;
    }
}
