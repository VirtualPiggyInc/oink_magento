<?php

class VirtualPiggy_VirtualPiggy_Block_Adminhtml_System_Config_TestConnection
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $block=Mage::app()->getLayout()->createBlock("virtualpiggy/adminhtml_system_config_testConnection_html");
        return $block->_toHtml();
    }
}