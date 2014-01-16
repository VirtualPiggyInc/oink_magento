<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Model_User
        extends Mage_Core_Model_Abstract
{
    
    CONST USER_CODE_TYPE_CHILDREN="Child";
    CONST USER_CODE_TYPE_PARENT="Parent";

    protected $_token;
    protected $_address;
    /**
     * Authenticate child and get the the result 
     * 
     * @param string $user Virtual Piggy account user
     * @param string $password Virtual Piggy account password
     * @return VirtualPiggy_VirtualPiggy_Model_User_Children|VirtualPiggy_VirtualPiggy_Model_User_Children
     */
    public function login($user, $password)
    {
        $paymentService = $this->_getHelper()->getVirtualPiggyPaymentService();
        $credentials = Mage::helper("virtualpiggy")->getDtoCredentials();
        $credentials->userName = $user;
        $credentials->password = $password;
        $auth = $paymentService->AuthenticateUser($credentials->userName, $credentials->password);
		/*
		 * When casting a string to bool php will consider empty strings false.
		 * 
         */
        if ((bool) $auth->Token) {
            if($auth->UserType==self::USER_CODE_TYPE_CHILDREN){
                $children=Mage::getModel("virtualpiggy/user_children");
                $children->setToken($auth->Token);
                return $children;
            }elseif($auth->UserType==self::USER_CODE_TYPE_PARENT){
                $parent=Mage::getModel("virtualpiggy/user_parent");
                $parent->setToken($auth->Token);
                return $parent;
            }
        }else{
            Mage::log($auth, null, "", true);
            Mage::throwException($auth->ErrorMessage);
        }
    }
    /**
     * Get address from Virtual Piggy and change it to magento format
     * @param dtoAddress
     * @return Varien_Object
     */
    public function getAddress($virtualPiggyAddress=null)
    {
        if (is_null($this->_address)) {
            if(is_null($virtualPiggyAddress)){
                $virtualPiggyAddress = $this->getAddressDto();
            }
            $resource=Mage::getSingleton("core/resource");
            $connection=$resource->getConnection("core_read");
            $table=$resource->getTableName("directory_country_region");
            $query = "SELECT default_name,region_id FROM {$table} WHERE country_id='{$virtualPiggyAddress->Country}' and code='{$virtualPiggyAddress->State}'"; 
            $region = $connection->fetchRow($query);

            $this->_address = new Varien_Object(array(
                "street" => $virtualPiggyAddress->Address,
                "city" => $virtualPiggyAddress->City,
                "country_id" => $virtualPiggyAddress->Country,
                "firstname" => $this->_getFirstname($virtualPiggyAddress->ParentName),
                "lastname" => $this->_getLastname($virtualPiggyAddress->ParentName),
                "telephone" => (bool)$virtualPiggyAddress->Phone ? $virtualPiggyAddress->Phone : "11111111",
                "region" => $region["default_name"],
                "region_id" => $region["region_id"],
                "postcode" => $virtualPiggyAddress->Zip,
                "email" => $virtualPiggyAddress->ParentEmail,
            ));
        }
        return $this->_address;
    }
    /**
     * @return VirtualPiggy_VirtualPiggy_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper("virtualpiggy");
    }
    /**
     * Get firstname from complete name
     * 
     * @param string $name
     * @return string
     */
    protected function _getFirstname($name)
    {
        if(is_null($name)){
            return "sds";
        }
        $_name = explode(" ", $name);
        return $_name[0];
    }
    /**
     * Get lastname from complete name
     * 
     * @param string $name
     * @return string
     */
    protected function _getLastname($name)
    {
        if(is_null($name)){
            return "sds";
        }
        $_name = explode(" ", $name);
        unset($_name[0]);
        return implode(" ", $_name);
    }
    /**
     * @return string
     */

    public function getRandomMail()
    {
        return uniqid() . "@gmail.com";
    }
    /**
     * @return string
     */
    public function getToken(){
        return $this->_token;
    }
    /**
     * @param $token string
     */
    public function setToken($token){
        $this->_token=$token;
        return $this;
    }


}

?>
