<?php

class VirtualPiggy_VirtualPiggy_Block_Adminhtml_System_Config_TestConnection_Html
    extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("virtualpiggy/system/config/testConnection.phtml");
    }
    
    public function getAjaxUrl(){
        $url=Mage::helper("adminhtml")->getUrl("*/virtualpiggy/testConnection");
        return $url;
    }
}