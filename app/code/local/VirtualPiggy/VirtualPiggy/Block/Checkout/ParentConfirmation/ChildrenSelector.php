<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Checkout_ParentConfirmation_ChildrenSelector
        extends Mage_Core_Block_Template
{
    public function getChildrens(){
        $helper=Mage::helper("virtualpiggy");
        $user=$helper->getUser();
        $childrens=$user->getChildrens();
        return $childrens;
    }
}