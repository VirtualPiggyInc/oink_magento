<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Rewrite_Checkout_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{
    
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        Mage::getSingleton("customer/session")->unsParentConfirmation();
        $button=Mage::helper("virtualpiggy")->getCheckoutButtonHtml(array("print_form"=>true));
        return parent::_toHtml().$button;
    }
    
}
