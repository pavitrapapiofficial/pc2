<?php
namespace PurpleCommerce\CustomPrice\Plugin\Model;

class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{
    public function getTierPriceList()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $customerSession = $om->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer

        
        if (null === $this->priceList) {
            $priceList = $this->getStoredTierPrices();
            $this->priceList = $this->filterTierPrices($priceList);
            // echo 'again worked'.$customerDataGid;
            if($customerDataGid!=2){
                array_walk(
                    $this->priceList,
                    function (&$priceData) {
                        /* convert string value to float */
                        $priceData['price_qty'] = $priceData['price_qty'] * 1;
                        $priceData['price'] = $this->applyAdjustment(round(2.25*$priceData['price'],0));
                        
                        
                    }
                );
            }else{
                array_walk(
                    $this->priceList,
                    function (&$priceData) {
                        /* convert string value to float */
                        $priceData['price_qty'] = $priceData['price_qty'] * 1;
                        $priceData['price'] = $this->applyAdjustment($priceData['price']);
                        
                        
                    }
                );
            }
            
        }
        return $this->priceList;
    }
}