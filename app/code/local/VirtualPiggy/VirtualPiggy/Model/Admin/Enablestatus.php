<?php

class VirtualPiggy_VirtualPiggy_Model_Admin_Enablestatus {
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('virtualpiggy')->__('None')),
            array('value'=>1, 'label'=>Mage::helper('virtualpiggy')->__('All Users')),
            array('value'=>2, 'label'=>Mage::helper('virtualpiggy')->__('Only Registered Users')),
        );
    }
}