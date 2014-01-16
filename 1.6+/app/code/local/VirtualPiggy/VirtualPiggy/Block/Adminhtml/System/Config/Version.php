<?php

class VirtualPiggy_VirtualPiggy_Block_Adminhtml_System_Config_Version
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $config=Mage::getConfig();
        $version=(string)$config->getNode("modules/VirtualPiggy_VirtualPiggy/version");
        return $version;
    }
}