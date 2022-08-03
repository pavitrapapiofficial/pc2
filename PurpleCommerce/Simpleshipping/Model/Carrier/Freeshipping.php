<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace PurpleCommerce\Simpleshipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Freeshipping extends \Magento\OfflineShipping\Model\Carrier\Freeshipping
{
    
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
//        echo '<pre>';
//        print_r($request->debug());
//        echo '</pre>';
//        die;
//        
        
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->_updateFreeMethodQuote($request);
        
        $packageValue = $request->getPackageValueWithDiscount();
        
        $items = $request->getAllItems();
        $c = count($items);
        
        for ($i = 0; $i < $c; $i++) {
            
            if ($items[$i]->getProduct() instanceof \Magento\Catalog\Model\Product) {
                $products = $items[$i]->getProduct();
                $groupId = $products->getCustomerGroupId();
               
            }
        }
       
        if($groupId == 2){
			$allow = ($request->getFreeShipping())
            || ($packageValue >= $this->getConfigData('free_shipping_subtotal_wholesale'));
        }
        else
        {
		$allow = ($request->getFreeShipping())
            || ($packageValue >= $this->getConfigData('free_shipping_subtotal'));
        }

        if ($allow) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        } elseif ($this->getConfigData('showmethod')) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $errorMsg = $this->getConfigData('specificerrmsg');
            $error->setErrorMessage(
                $errorMsg ? $errorMsg : __(
                    'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                )
            );
            return $error;
        }
        return $result;
    }
}
