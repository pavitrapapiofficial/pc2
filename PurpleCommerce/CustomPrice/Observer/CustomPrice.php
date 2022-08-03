<?php

namespace PurpleCommerce\CustomPrice\Observer;

use \Magento\Framework\Event\ObserverInterface;
// use \Magento\Quote\Model\Quote;

/**
 * Class CustomPrice
 * @package PurpleCommerce\CustomPrice\Observer
 *
 */
class CustomPrice implements ObserverInterface
{
    // protected $quote;
    // public function __construct(
    // ) {
    //    $this->quote = $quote;
    // }
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getEvent()->getData('quote_item');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cqty=1;
        // Get parent product if current product is child product
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

        $productSKU = $item->getProduct()->getSku();
        $productCollection = $objectManager->create('Magento\Catalog\Model\Product')->loadByAttribute('sku', $productSKU);
        $RegularPrice = $productCollection->getPriceInfo()->getPrice('regular_price')->getValue();
        $prodid=$productCollection->getId();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
       
        
        $attribute_set_name='';
        if(!empty($productCollection)){
            $attributeSet = $objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
            $attributeSetRepository = $attributeSet->get($productCollection->getAttributeSetId());
            $attribute_set_name = $attributeSetRepository->getAttributeSetName();
        }
        
        

        //Define your Custom price here
        $_product = $productCollection;
        if(!empty($_product)){
        
            
        
            $orgprice = $RegularPrice;
            $specialprice = $_product->getData('special_price');
            $specialfromdate = $_product->getData('special_from_date');
            $specialtodate = $_product->getData('special_to_date');
            $today = time();
            // if($_product->getTierPrices())
            if (!$specialprice){
                $specialprice = $orgprice;
                if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate')
                    $price = $RegularPrice;
                else
                    $price = $RegularPrice;
            }else if ($specialprice< $orgprice) {
                
                    if ((is_null($specialfromdate) &&is_null($specialtodate)) || ($today >= strtotime($specialfromdate) &&is_null($specialtodate)) || ($today <= strtotime($specialtodate) &&is_null($specialfromdate)) || ($today >= strtotime($specialfromdate) && $today <= strtotime($specialtodate))) {
                        // $RegularPrice = $specialprice;
                        if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate')
                            $price = round($specialprice * 2.25);
                        else
                            $price = $specialprice;
                        // echo 'product has a special price';
                    }
                // }
                
            }else{
                $price = $RegularPrice;
            }
            if($_product->getTierPrices()){
                $tier_price = $_product->getTierPrice();
                // var_dump($tier_price);
                // die;
                if(count($tier_price) > 0){
                    foreach($tier_price as $k=>$v){
                        $v = (int)$tier_price[$k]['price_qty'];
                        $qty = round($v);
                        if($qty<=$cqty){
                            if($customerDataGid!=2)
                                $price = $tier_price[$k]['price'];
                            else    
                                $price = $tier_price[$k]['price'];
                        }
                    }
                }
                
            }
            

        }else{
            return;
            if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate')
                $price = round($RegularPrice * 2.25);
            else
                $price = $RegularPrice;
        }

        // $price = $RegularPrice;
        if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate')
            $price = $price;

        if($customerDataGid!=2)
            $price = round($price);
            
        // echo "price-".$price;
        
        
        //Set custom price
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }
}