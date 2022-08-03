<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace PurpleCommerce\Simpleshipping\Plugin\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;

class ShippingMethodManagement {

    public function afterEstimateByExtendedAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }
    private function filterOutput($output)
    {
//        echo "Inside filter";
//        die;
        $free = [];
        foreach ($output as $shippingMethod) {
            if ($shippingMethod->getCarrierCode() == 'freeshipping' && $shippingMethod->getMethodCode() == 'freeshipping') {
                $free[] = $shippingMethod;
                break;
            }
        }
        if ($free) {
            return $free;
        }
        return $output;
    }
}
