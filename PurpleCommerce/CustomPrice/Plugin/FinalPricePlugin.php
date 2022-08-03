<?php
namespace PurpleCommerce\CustomPrice\Plugin;

class FinalPricePlugin
{
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $enable=1;
        // if ($enable) {
            if ($template == 'Magento_Catalog::product/price/final_price.phtml') {
                return ['PurpleCommerce_CustomPrice::product/price/final_price.phtml'];
            } 
            else
            {
                return [$template];
            }
        // } else {
            // return[$template];
        // }
    }
}