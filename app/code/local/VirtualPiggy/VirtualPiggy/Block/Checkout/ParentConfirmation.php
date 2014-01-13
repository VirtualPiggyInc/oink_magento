<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Checkout_ParentConfirmation
        extends Mage_Core_Block_Template
{
    /**
     *
     * @return  string
     */
    public function getFormActionUrl(){
        return $this->getUrl("virtualpiggy/checkout/processParentConfirmation");
    }

}
