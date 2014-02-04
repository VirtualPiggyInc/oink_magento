<?php

class VirtualPiggy_VirtualPiggy_Block_Adminhtml_Sales_Order_View_Tab_VirtualPiggy
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /*
     * @var $_order VirtualPiggy_VirtualPiggy_Model_Order
     */
    protected $_order;
    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('virtualpiggy')->__('Oink');
    }

    public function getTabTitle()
    {
        return Mage::helper('virtualpiggy')->__('Oink');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        $helper=Mage::helper("virtualpiggy");
        $order=$this->getOrder();
        $vpOrder=Mage::helper("virtualpiggy/checkout")->getVirtualPiggyOrder($order->getId());
        $this->_order=$vpOrder;
        return !(bool)$vpOrder->getId();
    }
}
