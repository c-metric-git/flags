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



class Mirasvit_Rewards_Helper_MailTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected function getExpectedMail($code)
    {
        return file_get_contents(dirname(__FILE__)."/MailTest/expected/$code.html");
    }

    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('rewards/mail');
        $this->helper->emails = array();
    }

    /**
     * @test
     * @loadFixture data
     */
    public function sendNotificationBalanceUpdateEmailTest()
    {
        $transaction = Mage::getModel('rewards/transaction')->load(2);
        $this->helper->sendNotificationBalanceUpdateEmail($transaction, 'balance update email');
        $result = Mage::helper('msttest/string')->html2txt($this->helper->emails[0]['text']);
        // echo $result;die;
        $this->assertEquals($this->getExpectedMail('notification_balance_update_email_template'), $result);
        $this->assertEquals('john_test@example.com', $this->helper->emails[0]['recipient_email']);
        $this->assertEquals('John Doe', $this->helper->emails[0]['recipient_name']);
    }

    /**
     * @test
     * @loadFixture data
     */
    public function sendNotificationBalanceUpdateEmailTest2()
    {
        $transaction = Mage::getModel('rewards/transaction')->load(2);
        $this->helper->sendNotificationBalanceUpdateEmail($transaction);
        $result = Mage::helper('msttest/string')->html2txt($this->helper->emails[0]['text']);
        // echo $result;die;
        $this->assertEquals($this->getExpectedMail('notification_balance_update_email_template2'), $result);
        $this->assertEquals('john_test@example.com', $this->helper->emails[0]['recipient_email']);
        $this->assertEquals('John Doe', $this->helper->emails[0]['recipient_name']);
    }

    /**
     * @test
     * @loadFixture data
     */
    public function sendNotificationPointsExpireEmailTest()
    {
        $transaction = Mage::getModel('rewards/transaction')->load(2);
        $transaction->setExpiresAt(Mage::getSingleton('core/date')->gmtDate(null, time() + 5 * 24 * 60 * 60));
        $transaction->save();

        $this->helper->sendNotificationPointsExpireEmail($transaction);
        $result = Mage::helper('msttest/string')->html2txt($this->helper->emails[0]['text']);
        // echo $result;die;
        $this->assertEquals($this->getExpectedMail('notification_points_expire_email_template'), $result);
        $this->assertEquals('john_test@example.com', $this->helper->emails[0]['recipient_email']);
        $this->assertEquals('John Doe', $this->helper->emails[0]['recipient_name']);
    }

    /**
     * @test
     * @loadFixture data
     */
    public function sendReferralInvitationEmailTest()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $referral = Mage::getModel('rewards/referral')->load(2);
        $this->helper->sendReferralInvitationEmail($referral, 'some message');
        // echo $this->helper->emails[0]['text'];
        $result = Mage::helper('msttest/string')->html2txt($this->helper->emails[0]['text']);
        // echo $result;die;
        $this->assertEquals($this->getExpectedMail('referral_invitation_email_template'), $result);
        $this->assertEquals('bill@example.com', $this->helper->emails[0]['recipient_email']);
        $this->assertEquals('Bill Gates', $this->helper->emails[0]['recipient_name']);
    }
}
