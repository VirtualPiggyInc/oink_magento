<?php
/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Block_Payment_Info_VirtualPiggy
        extends Mage_Payment_Block_Info
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('virtualpiggy/payment/info/virtualpiggy.phtml');
    }
    /**
     * Render as PDF
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('virtualpiggy/payment/info/virtualpiggy.phtml');
        return $this->toHtml();
    }

}
