<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Model_User_Children
        extends VirtualPiggy_VirtualPiggy_Model_User
{

    protected $_token;
    protected $_address;
    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Virtual Piggy account user
     * @param string $password Virtual Piggy account password
     * @return VirtualPiggy_VirtualPiggy_Model_Children
     */
    public function login($user, $password)
    {
        $paymentService = $this->_getHelper()->getVirtualPiggyPaymentService();
        $credentials = Mage::helper("virtualpiggy")->getDtoCredentials();
        $credentials->userName = $user;
        $credentials->password = $password;
        $auth = $paymentService->AuthenticateChild($credentials->userName, $credentials->password);
		/*
		 * When casting a string to bool php will consider empty strings false.
		 * */
        if ((bool) $auth->Token) {
            $this->_token = $auth->Token;
            return $this;
        }else{
            Mage::log($auth, null, "", true);
            return null;
        }
    }
    /**
     * Get address from Virtual Piggy
     * 
     * @return dtoAddress
     */
    public function getAddressDto(){
        $paymentService = $this->_getHelper()->getVirtualPiggyPaymentService();
        return $paymentService->GetChildAddress($this->_token);
    }
}

?>
