<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /*
     * Virtual Piggy helper
     * @var VirtualPiggy_VirtualPiggy_Helper_Data
     */
    protected $_helper;
    
    public function setUp()
    {
        $this->_helper=Mage::helper("virtualpiggy");
        parent::setUp();
    }

    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function loginCorrectUser($user,$password)
    {
        $helper=$this->_helper;
        $result = $helper->authenticateChild($user, $password);
        $this->assertTrue((bool)$result,"The user and password are correct, but the login fails.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function loginIncorrectUser($user,$password)
    {
        $helper=$this->_helper;
        $result = $helper->authenticateChild($user, $password);
        $this->assertFalse($result,"The user and password are incorrect, but the login success.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function loginIncorrectUserMultipleTimes($user,$password)
    {
        $helper=$this->_helper;
        for($i=0;$i<11;$i++){
            $result = $helper->authenticateUser($user, $password);
        }
        $this->assertFalse($result,"The user and password are incorrect, but the login success.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function getUserAddress($user,$password)
    {
        $helper=$this->_helper;
        $helper->authenticateChild($user, $password);
        $children=Mage::helper("virtualpiggy")->getUser();
        $address=$children->getAddress();
        $this->assertEquals("",(string)$address->ErrorMessage,"The user and password are correct, but the address have an error.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function processTransaction($user,$password){
		/*
		 * Test not working
		 * Test not obtaining same result as in normal magento process
		 * Dispatch coupled to magento's framework
		 * Will fix this when last phase of project is complete
		 * */
        $helper=$this->_helper;
        $helper->authenticateChild($user, $password);
        $quote=$helper->getQuote();
        $product1=Mage::getModel("catalog/product")->load(1);
        $product2=Mage::getModel("catalog/product")->load(2);
        $quote->addProduct($product1);
        $quote->addProduct($product2);
        Mage::helper("virtualpiggy/checkout")->populateQuote();
        $cart=Mage::helper("virtualpiggy/checkout")->getVirtualPiggyCart();
        $result=Mage::helper("virtualpiggy/checkout")->sendCartToVirtualPiggy($cart);
        var_dump($result);
//      $controllerTestCase=new EcomDev_PHPUnit_Test_Case_Controller();
//      $controllerTestCase->dispatch("virtualpiggy/checkout/index");
    }
    
    
}