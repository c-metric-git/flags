<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Backup_Comments_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $deletion_row = $row['deletion_row'];
        $comment_obj = unserialize($deletion_row);
        $result = "";

        if(isset($comment_obj['status']))
            $result .= '<b>'.$comment_obj['status'].':</b> ';
        if(isset($comment_obj['comment']))
            $result .= $comment_obj['comment'];

        return $result;
    }
}
