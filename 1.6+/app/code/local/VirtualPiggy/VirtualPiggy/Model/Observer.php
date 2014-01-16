<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Model_Observer
{
    /**
     *
     * @param   Varien_Event_Observer $observer
     */
    public function checkExpiredOrders($observer)
    {
        $now = Mage::getModel('core/date')->timestamp(time());
        $expiredOrders = Mage::getModel("virtualpiggy/order")->getCollection();
        $expiredOrders->addFieldToFilter("expiry_date", array("lt" => $now));
        foreach ($expiredOrders as $key => $order) {
            Mage::helper("virtualpiggy/checkout")->cancelOrder($order);
        }
    }

    /**
     *
     * @param   Varien_Event_Observer $observer
     */
    public function removeVirtualpiggyPaymentMethod($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods) {
            $block->unsetChild("payment.method." . VirtualPiggy_VirtualPiggy_Helper_Checkout::PAYMENT_METHOD_CODE);
            if (!Mage::helper("virtualpiggy/checkout")->isEnabled()) {
                $methods = $block->getMethods();
                foreach ($methods as $key => $method) {
                    if ($method->getCode() == VirtualPiggy_VirtualPiggy_Helper_Checkout::PAYMENT_METHOD_CODE) {
                        unset($methods[$key]);
                    }
                }
                $block->setData('methods', $methods);
            }
        }
    }

    /**
     * TODO change this to invoice PRE paid
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminOrderStatusChange($observer)
    {
    	$result = '';
        $order = $observer->getEvent()->getOrder();
        $state = $observer->getEvent()->getState();

        if (Mage::helper("virtualpiggy/checkout")->isTwoStepsAuthorizationEnabled()) {
            $vpOrder = Mage::helper("virtualpiggy/checkout")->getVirtualPiggyOrder($order->getId());
            $transactionIdentifier = $vpOrder->getTransactionIdentifier();

            /**
             * @var VirtualPiggyPaymentService $paymentService
             */
            $paymentService = Mage::helper("virtualpiggy")->getVirtualPiggyPaymentService();

            if ($transactionIdentifier) {
                if ($state == Mage_Sales_Model_Order::STATE_CANCELED) {
                    $result = $paymentService->VoidCaptureTransactionByIdentifier($transactionIdentifier);
                }

                if ($result) {
                    Mage::helper("virtualpiggy")->log($result, "resultOfProcessTwoSteps");
                    if (!(bool)$result->Status) {
                        $errorMessage = Mage::getSingleton("virtualpiggy/errorHandler")->rewriteError($result->ErrorMessage);
                        Mage::getSingleton("core/session")->addError($errorMessage);
                    }
                }
            }
        }
    }

}
