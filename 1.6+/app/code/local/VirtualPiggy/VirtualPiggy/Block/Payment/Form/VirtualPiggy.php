<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Payment_Form_VirtualPiggy
        extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        $this->setMethodLabelAfterHtml(Mage::helper("virtualpiggy")->getCheckoutButtonHtml());
        parent::_construct();
        $this->setTemplate('virtualpiggy/payment/form/virtualpiggy.phtml');
    }

}
