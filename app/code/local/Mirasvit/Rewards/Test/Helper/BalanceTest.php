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


class Mirasvit_Rewards_Helper_BalanceTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;

    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('rewards/balance_order');
    }

    /**
     * @test
     * @loadFixture data
     */
    public function earnOrderPointsTest() {
        $order = Mage::getModel('sales/order')->load(2);

        $total = Mage::helper('rewards/balance_order')->earnOrderPoints($order);
        $this->assertEquals(20, $total);

        $balance = Mage::helper('rewards/balance')->getBalancePoints(2);
        $this->assertEquals(20, $balance);
    }


    /**
     * @test
     * @loadFixture data2
     */
    public function earnOrderPointsTest2() {
        $order = Mage::getModel('sales/order')->load(2);
        $total = Mage::helper('rewards/balance_order')->earnOrderPoints($order);
        $this->assertEquals(10, $total);

        $balance = Mage::helper('rewards/balance')->getBalancePoints(2);
        $this->assertEquals(10, $balance);
    }

    /**
     * @test
     * @loadFixture data2
     */
    public function spendOrderPointsTest() {
        $order = Mage::getModel('sales/order')->load(2);
        $total = Mage::helper('rewards/balance_order')->spendOrderPoints($order);
        $this->assertEquals(100, $total);

        $balance = Mage::helper('rewards/balance')->getBalancePoints(2);
        $this->assertEquals(-100, $balance);
    }
}