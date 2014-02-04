<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Checkout_ParentConfirmation_Payment
        extends Mage_Core_Block_Template
{
    public function getMethods(){
        $helper=Mage::helper("virtualpiggy");
        $user=$helper->getUser();
        $methods=$user->getPaymentMethods();
        if (!empty($methods)){
            foreach($methods as $method){
                if($method->getToken() <> null){
                    return $methods;
                }
            }
        }
        throw new Exception("Parent has no payment accounts available/activated. Please refer to Oink's dashboard and configure/activate one.");
        return;
    }
}