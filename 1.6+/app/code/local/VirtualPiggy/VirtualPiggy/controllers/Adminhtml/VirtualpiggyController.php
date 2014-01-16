<?php

/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Adminhtml_VirtualpiggyController
        extends Mage_Adminhtml_Controller_Action
{
    public function testConnectionAction(){
		$TransactionServiceEndpointAddress = $this->getRequest()->getParam("transactionServiceEndpointAddress");
		$TransactionServiceEndpointAddressWsdl = $TransactionServiceEndpointAddress."?wsdl";
        $config=array(
			"TransactionServiceEndpointAddress" => $TransactionServiceEndpointAddress,
			"TransactionServiceEndpointAddressWsdl" => $TransactionServiceEndpointAddressWsdl,
            "MerchantIdentifier" => $this->getRequest()->getParam("merchantIdentifier"),
            "APIkey" => $this->getRequest()->getParam("apiKey"),
        );
        $paymentService = Mage::helper("virtualpiggy")->getVirtualPiggyPaymentService($config);
        $result=$paymentService->PingHeaders();
        $this->getResponse()->setBody($result);
    }


}

?>
