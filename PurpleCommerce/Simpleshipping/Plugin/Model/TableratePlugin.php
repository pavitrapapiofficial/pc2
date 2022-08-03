<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace PurpleCommerce\Simpleshipping\Plugin\Model;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

class TableratePlugin
{


    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory
     */
    private $rateErrorFactory;

    /**
     * @var string
     */
    protected $_code = 'tablerate';
    /**
     * @var string
     */
    protected $_defaultConditionName = 'package_weight';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_resultMethodFactory;
    
    public function __construct(
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository       
    ){
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;     
        $this->rateErrorFactory = $rateErrorFactory;
        $this->productRepository = $productRepository;
    }        
    public function aroundCollectRates(
        \Magento\OfflineShipping\Model\Carrier\Tablerate $subject,
        \Closure $proceed,
        RateRequest $request   
    ) {
        if (!$subject->getConfigFlag('active')) {
            return false;
        } 
        $groupId = 1;
        // exclude Virtual products price from Package value if pre-configured
        if (!$subject->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        } 
 
        // Free shipping by qty
        $freeQty = 0;
        $freePackageValue = 0;

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping() || $item->getAddress()->getFreeShipping()) {
                    $freeShipping = $item->getFreeShipping() ?
                        $item->getFreeShipping() : $item->getAddress()->getFreeShipping();
                    $freeShipping = is_numeric($freeShipping) ? $freeShipping : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
                if ($item->getProduct() instanceof \Magento\Catalog\Model\Product) {
                  $products = $item->getProduct();
                  $groupId = $products->getCustomerGroupId();
                 
              }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }
 
        if (!$request->getConditionName()) {
            $conditionName = $subject->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_defaultConditionName);
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $rate = $subject->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty); 


        if (!empty($rate) && $rate['price'] >= 0 && $groupId!=2) {
            if ($request->getPackageQty() == $freeQty) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $subject->getFinalPriceWithHandlingFee($rate['price']);
            }
            $method = $this->createShippingMethod($shippingPrice, $rate['cost'], $subject,$request);
            $result->append($method);
        } elseif ($request->getPackageQty() == $freeQty) {

            /**
             * Promotion rule was applied for the whole cart.
             *  In this case all other shipping methods could be omitted
             * Table rate shipping method with 0$ price must be shown if grand total is more than minimal value.
             * Free package weight has been already taken into account.
             */
            $request->setPackageValue($freePackageValue);
            $request->setPackageQty($freeQty);
            $rate = $subject->getRate($request);
            if (!empty($rate) && $rate['price'] >= 0) {
                $method = $this->createShippingMethod(0, 0, $subject,$request);
                $result->append($method);
            }
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $subject->getConfigData('title'),
                        'error_message' => $subject->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }
       return $result;  
    }
    /**
     * Get the method object based on the shipping price and cost
     *
     * @param float $shippingPrice
     * @param float $cost
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createShippingMethod($shippingPrice, $cost, $subject,$request)
    {

        /** @var  \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_resultMethodFactory->create();

        $method->setCarrier('tablerate');
        $method->setCarrierTitle($subject->getConfigData('title'));

        $method->setMethod('bestway');
        $method->setMethodTitle($subject->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($cost);
        return $method;
    }    
}