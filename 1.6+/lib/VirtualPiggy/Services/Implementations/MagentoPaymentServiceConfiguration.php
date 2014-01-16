<?php
/**
 * @package VirtualPiggy.Services.Implementations
 */
class MagentoPaymentServiceConfiguration implements IPaymentServiceConfiguration
{
    public function GetServiceConfiguration()
    {
        $config = new dtoPaymentGatewayConfiguration();
        return $config;            
    }
}   
?>
