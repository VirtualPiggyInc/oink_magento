<?php

/**
 * @category    VirtualPiggy
 * @package     VirtualPiggy_VirtualPiggy
 */
class VirtualPiggy_VirtualPiggy_Model_Mysql4_Order
        extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('virtualpiggy/order', 'virtualpiggy_order_id');
    }

}

?>
