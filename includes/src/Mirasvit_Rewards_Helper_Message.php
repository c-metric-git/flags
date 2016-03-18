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


class Mirasvit_Rewards_Helper_Message extends Mirasvit_MstCore_Helper_Help
{

    public function getNoteWithVariables()
    {
        $note = Mage::helper('rewards')->__('You can use the following variables:').'<br>';
        $note .= '{{htmlescape var=$customer.name}} - customer name<br>';
        $note .= '{{store url=""}} - store URL<br>';
        $note .= '{{var store.getFrontendName()}} - store name<br>';
        $note .= '{{var transaction_amount}} - formatted amount of current transaction (e.g 10 Rewards Points)<br>';
        $note .= '{{var balance_total}} - formatted balance of customer account (e.g. 100 Rewards Points)<br>';
        $note .= 'Leave empty to use default notification email. <br>';
        return $note;
    }

}