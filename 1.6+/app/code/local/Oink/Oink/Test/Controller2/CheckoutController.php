<?php

class Oink_Oink_Test_Controller_CheckoutController
        extends EcomDev_PHPUnit_Test_Case_Controller
{

    /**
     *
     * @param string $user
     * @param string $badLogin
     * @test
     * @dataProvider dataProvider
     */
    public function goodLogin($user,$badLogin)
    {
        $this->getRequest()->setPost("user", $user);
        $this->getRequest()->setPost("badLogin", $badLogin);
        $this->dispatch("oink/checkout/loginPost");
        $responseJson=$this->getResponse()->getOutputBody();
        $response=json_decode($responseJson);
        $this->assertTrue($response->response,"The user and badLogin are correct. But something in the login failed.");
    }
    
    /**
     *
     * @param string $user
     * @param string $badLogin
     * @test
     * @dataProvider dataProvider
     */
    public function loginIncorrectUser($user,$badLogin)
    {
        $this->getRequest()->setPost("user", $user);
        $this->getRequest()->setPost("badLogin", $badLogin);
        $this->dispatch("oink/checkout/loginPost");
        $responseJson=$this->getResponse()->getOutputBody();
        $response=json_decode($responseJson);
        $configMessage=Mage::getStoreConfig("oink/messages/error_login");
        $this->assertTrue($response->response,"The user and badLogin are incorrect. But the the login success.");
        $this->assertEquals($configMessage,$response->errorMessage,"The login failed. But the message is not the configured in the admin.");
    }

}

?>
