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



class Mirasvit_Rewards_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('rewards/data');
    }

    /**
     * @test
     */
    public function exampleTest()
    {
        $this->assertEquals(1, 1);
    }

    /**
     * @test
     * @dataProvider exampleProvider
     */
    public function example2Test($expected, $input)
    {
        $result = $input;
        $this->assertEquals($expected, $result);
    }

    public function exampleProvider()
    {
        return array(
            array(1, 1),
        );
    }

    /**
     * @test
     * @loadFixture data
     */
    public function example3Test()
    {
        $product = Mage::getModel('catalog/product')->load(2);
        $this->assertEquals('Example Product', $product->getName());
    }
}
