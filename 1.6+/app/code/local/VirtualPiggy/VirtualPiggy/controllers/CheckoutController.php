<?php

/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_CheckoutController
    extends Mage_Core_Controller_Front_Action
{

    /**
     * Confirmation checkout page
     */
    public function indexAction()
    {
        if (!Mage::helper("virtualpiggy/checkout")->customerHasProductsInCart()) {
            Mage::getSingleton("core/session")->addError($this->__("You need to have products in your cart."));
            $this->_redirect("checkout/cart/index");
        } elseif (!Mage::helper("virtualpiggy")->isUserLogged()) {
            Mage::getSingleton("core/session")->addError($this->__("You need to be logged in Oink."));
            $this->_redirect("checkout/cart/index");
        } else {
            if ($this->_isOrderReadyForConfirmation()) {
                $this->_redirect("virtualpiggy/checkout/parentConfirmation");
            } else {
                Mage::helper("virtualpiggy/checkout")->populateQuote();
                $this->loadLayout()
                    ->renderLayout();
            }
        }
    }

    /**
     * Parent Confirmation page
     */
    public function parentConfirmationAction()
    {
        Mage::getSingleton("customer/session")->unsParentConfirmation();
        try {
            $this->loadLayout()->renderLayout();
        } catch (Exception $e) {
            $errorMessage = Mage::getSingleton("virtualpiggy/errorHandler")->rewriteError($e->getMessage());
            Mage::getSingleton("core/session")->addError($errorMessage);
            $this->_redirect("checkout/cart/index");
        }
    }

    /**
     * Login Virtual Piggy user page
     */
    public function loginPostAction()
    {
        $user = $this->getRequest()->getPost("user");
        $password = $this->getRequest()->getPost("password");
        $loginResponse = array();
        try {
            $loginResponse["response"] = (bool)Mage::helper("virtualpiggy")->authenticateUser($user, $password);

        } catch (Exception $e) {
            if (strpos($e->getMessage(), "temporarily disabled") !== false) {
                $loginResponse["errorMessage"] = Mage::getStoreConfig("virtualpiggy/messages/max_login_attemps");
            } else {
                $loginResponse["errorMessage"] = Mage::getStoreConfig("virtualpiggy/messages/login_error");
            }
        }
        /*
           * Zend_Json_Encoder is required for magento to work. This will always be available in magento installations.
           * */
        $this->getResponse()->setBody(Zend_Json_Encoder::encode($loginResponse));
    }

    /**
     * Process order page
     */
    public function placeOrderAction()
    {

        /**
         * @var VirtualPiggy_VirtualPiggy_Helper_Checkout $vpCheckoutHelper
         */
        $vpCheckoutHelper = Mage::helper("virtualpiggy/checkout");

        try {
            $cart = $vpCheckoutHelper->getVirtualPiggyCart();
            $result = $vpCheckoutHelper->sendCartToVirtualPiggy($cart);

            /*
             * TODO: Review. Should we be saving all raw xml response from Virtual Piggy to database?
             */
            Mage::helper("virtualpiggy")->log($result, "resultOfProcessTransaction");
            if ((bool)$result->Status) {
                $this->_placeOrder();
                $order = $vpCheckoutHelper->getVirtualPiggyOrder();
                $order->setOrderId($this->getCheckout()->getLastOrderId());
                $createdAt = strtotime($vpCheckoutHelper->getOrder()->getCreatedAt());
                $expiryDate = $createdAt + Mage::helper("virtualpiggy")->getExpiryTime();

                $order->setExpiryDate($expiryDate);
                $order->setTransactionIdentifier($result->TransactionIdentifier);
                $order->save();

                $this->getCheckout()->clear();

                // TODO fix this!
                $originalOrder = Mage::getModel('sales/order')->load($order->getOrderId());

                if ($result->TransactionStatus == VirtualPiggy_VirtualPiggy_Helper_Checkout::APPROVAL_PENDING_CODE) {
                    $message = Mage::getStoreConfig("virtualpiggy/messages/approval_required");
                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, 'Awaiting parent approval - TXID: '.$result->TransactionIdentifier);

                    $originalOrder->setVirtualPiggyStatus(
                        VirtualPiggy_VirtualPiggy_Helper_Checkout::ORDER_STATUS_APPROVAL_PENDING
                    );
                } else {
                    $message = Mage::getStoreConfig("virtualpiggy/messages/success_transaction");
                    $vpCheckoutHelper->completeOrder($order);
                	$originalOrder->sendNewOrderEmail();

                    $originalOrder->setVirtualPiggyStatus(
                        VirtualPiggy_VirtualPiggy_Helper_Checkout::ORDER_STATUS_APPROVED
                    );

                    $originalOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'TXID: '.$result->TransactionIdentifier);
                }

                $order->save();
                $originalOrder->save();

                Mage::getSingleton("core/session")->addSuccess($message);
                $path = "*/*/success";
                Mage::getSingleton("customer/session")->unsParentConfirmation();
            } else {
                $errorMessage = Mage::getSingleton("virtualpiggy/errorHandler")->rewriteError($result->ErrorMessage);
                Mage::getSingleton("core/session")->addError($errorMessage);
                $path = "*/*/index";
            }
        } catch (Exception $e) {
            Mage::getSingleton("core/session")->addError($e->getMessage());
            $path = "*/*/index";
        }

        $this->_redirect($path);
    }

    /*
    * @return bool
    */
    protected function _isOrderReadyForConfirmation()
    {
        return Mage::helper("virtualpiggy")->getUserType() == VirtualPiggy_VirtualPiggy_Model_User::USER_CODE_TYPE_PARENT
            && !(bool)Mage::getSingleton("customer/session")->getParentConfirmation();
    }

    protected function _placeOrder()
    {
        $quote = $this->_prepareGuestQuote();

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $checkoutSession = $this->getCheckout();

        $checkoutSession->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();

        $checkoutSession->setLastOrderId($order->getId())
            ->setRedirectUrl($redirectUrl)
            ->setLastRealOrderId($order->getIncrementId());

        $agreement = $order->getPayment()->getBillingAgreement();
        if ($agreement) {
            $checkoutSession->setLastBillingAgreementId($agreement->getId());
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after', array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
        );
    }

    /**
     * Callback page
     */
    public function callbackAction()
    {
        $params = $this->getRequest()->getParams();
        $transactionId = $params["TransactionIdentifier"];
        Mage::helper("virtualpiggy")->log($params, "receivedCallbackMessage");
        if ($transactionId) {
            /**
             * @var VirtualPiggy_VirtualPiggy_Helper_Checkout $checkoutHelper
             */
            $checkoutHelper = Mage::helper("virtualpiggy/checkout");

            $order = $checkoutHelper->getVirtualPiggyOrder($transactionId, "transaction_identifier");
            if ($order->getId()) {
                $order->addAdditionalInformation("parentApproval", $params);

                // TODO fix this!
                $originalOrder = Mage::getModel('sales/order')->load($order->getOrderId());

                $originalOrder->setData(
                    'virtual_piggy_status',
                    VirtualPiggy_VirtualPiggy_Helper_Checkout::ORDER_STATUS_APPROVED
                );
                $originalOrder->sendNewOrderEmail();

                $originalOrder->save();

                // TODO is completOrder?
                $checkoutHelper->completeOrder($order);
            }
        }
    }

    /**
     * Checkout success page
     */
    public function successAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /*
    * Quick Connect page
    */
    public function quickconnecetAction()
    {

    }

    /**
     * Process Parent Confirmation page
     */
    public function processParentConfirmationAction()
    {
        $params = $this->getRequest()->getParams();
        $errors = array();
        if (!isset ($params["children"])) {
            $errors[] = $this->__("You need to select one children");
        }
        if (!isset ($params["paymentAccount"])) {
            $errors[] = $this->__("You need to select one payment account");
        }
        if ((bool)count($errors)) {
            foreach ($errors as $error) {
                Mage::getSingleton("core/session")->addError($error);
            }
            $this->_redirect("*/*/parentConfirmation");
        } else {
            Mage::helper("virtualpiggy")->getUser()->addData(array(
                "selected_children" => $params["children"],
                "selected_payment_account" => $params["paymentAccount"],
                "deliver_to_children" => isset ($params["deliverToChildAddress"]),
                "notify_children" => isset ($params["notifyChild"]),
            ));
            Mage::getSingleton("customer/session")->setParentConfirmation(true);
            $this->_redirect("*/*/index");
        }
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        $quote->setTotalsCollectedFlag(true);
        ;

        return $quote;
    }

    /*
    *
    * @return Mage_Checkout_Model_Session
    */

    public function getCheckout()
    {
        return Mage::getSingleton("checkout/session");
    }

    /*
     *
     * @return Mage_Sales_Model_Quote
     */

    public function getQuote()
    {
        return Mage::helper("virtualpiggy/checkout")->getQuote();
    }

}
