<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Checkout_Button
        extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        if(Mage::helper("virtualpiggy/checkout")->isEnabled()){
            $this->setTemplate("virtualpiggy/checkout/button.phtml");
        }
    }
    /**
     * Get checkout action url
     *
     * @return  string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl("virtualpiggy/checkout/index");
    }
    /**
     * Get css class for button
     *
     * @return  string
     */
    public function getCssClass()
    {
        return "virtualpiggy-checkout-button";
    }
    /**
     * Get button image url
     *
     * @return  string
     */
    public function getQuickConnectImageUrl(){
        return $this->getSkinUrl("images/virtualpiggy/checkout/quick-connect.png");
    }
    /**
     * Get button image url
     *
     * @return  string
     */
    public function getImageUrl()
    {
	    return "https://cdn.virtualpiggy.com/public/images/checkout-150x49.png";

    }
    /**
     * Get login post url
     *
     * @return  string
     */
    public function getLoginPostUrl(){
        $secure = Mage::app()->getStore()->isCurrentlySecure();
        return $this->getUrl("virtualpiggy/checkout/loginPost",array("_secure"=>$secure));
    }
    /**
     * Get login post url
     *
     * @return  string
     */
    public function getQuickConnectPostUrl(){
        return $this->getUrl("virtualpiggy/checkout/quickconnect");
    }

}
