<?php
 
namespace PurpleCommerce\CustomPrice\Plugin\Model;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use \Magento\Framework\Message\ManagerInterface ;
class Product 
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */

    public function __construct(
        State $state,
        \Magento\Checkout\Model\Session $checkoutSession,
        ManagerInterface $messageManager
    ) {
        $this->state = $state;
        $this->quote = $checkoutSession->getQuote();
        $this->_messageManager = $messageManager;
    }
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {   
        // if($his->getCustomerGroup==1)
        // var_dump($result);
        // die;
        $om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $customerSession = $om->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
        
        
        $productSKU = $subject->getSKU();
        $productCollection = $om->create('Magento\Catalog\Model\Product')->loadByAttribute('sku', $productSKU);
        $attribute_set_name='Monogram';
        if(!empty($productCollection)){
            $attributeSet = $om->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
            $attributeSetRepository = $attributeSet->get($productCollection->getAttributeSetId());
            $attribute_set_name = $attributeSetRepository->getAttributeSetName();
        }
        
        if($attribute_set_name=='Gift Certificate'){
            return $result;
        }else{
            if($customerDataGid!=2)
                return round($result * 2.25);
            else
                return $result;
        }
        
        
       
    }

    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)
    {   
        // if($his->getCustomerGroup==1)
        // var_dump($result);
        // die;
        $om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $customerSession = $om->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
        $product = $om->get('Magento\Framework\Registry')->registry('current_product');
        $attribute_set_name='';
        if(!empty($product)){
            $attributeSet = $om->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
            $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
            $attribute_set_name = $attributeSetRepository->getAttributeSetName();
        }
        
        
        
        if(!empty($result)){
            if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate')
                return round($result * 2.25);
            else
                return $result;
        }
        

    }

    public function afterGetTierPrice(\Magento\Catalog\Model\Product $subject, $result)
    {   
        // if($his->getCustomerGroup==1)
        // echo "I am here";
        // var_dump($result);
        // die;
        $om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $customerSession = $om->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
        $product = $om->get('Magento\Framework\Registry')->registry('current_product');
        $attribute_set_name='';
        if(!empty($product)){
            $attributeSet = $om->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
            $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
            $attribute_set_name = $attributeSetRepository->getAttributeSetName();
        }
        
        
        if($customerDataGid!=2 && $attribute_set_name!='Gift Certificate'){
            
            return $result;
        }else{
            return $result;
        }
        return $result;

    }

    // public function beforeupdateItems(\Magento\Checkout\Model\Cart $subject,$data)
    // {
    //     $om = \Magento\Framework\App\ObjectManager::getInstance(); 
    //     $customerSession = $om->get('Magento\Customer\Model\Session'); 
    //     $customerDataGid = $customerSession->getCustomer()->getGroupId();
    //     $quote = $subject->getQuote();
    //     // var_dump($quote);
    //     foreach($data as $key=>$value){
    //         echo "key".$key;
    //         var_dump($value);
    //         $item = $quote->getItemById($key);
    //         echo $productSku= $item->getSku();
    //     }
    //     die;
    //     if($customerDataGid==1){
    //         foreach($data as $key=>$value){
    //             $item = $quote->getItemById($key);
    //              $productSku= $item->getSku();
    //              $productCollection = $om->create('Magento\Catalog\Model\Product')->loadByAttribute('sku', $productSku);
    //               $itemQty= $value['qty'];
    //                 if($data[$item]['qty']>1)
    //                 {
    //                     $data[$item]['qty'] = 1;
    //                     if($item->getQty()>1){
    //                         $this->_messageManager->addNoticeMessage('Only one item can be bought at a time');
    //                     }
    //                 }
    
    //         }
    //         return [$data];
    //     }

        
    //     return [$data];
    // }

    // public function afterGetFinalPrice($subject, $proceed, $qty, $product){
    //     if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
    //         return $product->getCalculatedFinalPrice();
    //     }

    //     $finalPrice = $this->getBasePrice($product, $qty);
    //     $product->setFinalPrice($finalPrice);

    //     $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);

    //     $finalPrice = $product->getData('final_price');
    //     $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
    //     $finalPrice = max(0, $finalPrice);
    //     $product->setFinalPrice($finalPrice);

    //     return 100;
    // }

    

}