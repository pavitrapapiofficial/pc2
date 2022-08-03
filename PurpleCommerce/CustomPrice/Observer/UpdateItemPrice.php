<?php
namespace PurpleCommerce\CustomPrice\Observer;
use \Magento\Framework\Message\ManagerInterface ;
use \Magento\Checkout\Model\Cart;
class UpdateItemPrice implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    protected $customPrice;
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;
    protected $subject;
    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $subject,
        // PurpleCommerce\CustomPrice\Observer $customPrice,
        ManagerInterface $messageManager
    ) {
        // $this->customPrice = $customPrice;
        $this->quote = $checkoutSession->getQuote();
        $this->subject = $subject;
        $this->_messageManager = $messageManager;
    }
    /**
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $data
     * @return array
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $items = $observer->getCart()->getQuote()->getItems();
        // $info = $observer->getInfo()->getData();
        $item = $observer->getEvent()->getData('quote_item');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
        $quote = $this->subject->getQuote();
        $cqty = 1;
        $data = $this->subject->getQuote()->getAllItems();
        // echo "event captured";
        $price=0;
        foreach($data as $item) {
            $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            $cqty=1;
             $price = $_product->getPrice();
            // var_dump($_product);
            // die;
            
            $specialprice = $_product->getData('special_price');
            $specialfromdate = $_product->getData('special_from_date');
            $specialtodate = $_product->getData('special_to_date');
            $today = time();
            if(!empty($item->getQty())){
                $cqty = $item->getQty();
            } 
            if($specialprice< $price){
                
                // echo "inside special";
                if ((is_null($specialfromdate) &&is_null($specialtodate)) || ($today >= strtotime($specialfromdate) &&is_null($specialtodate)) || ($today <= strtotime($specialtodate) &&is_null($specialfromdate)) || ($today >= strtotime($specialfromdate) && $today <= strtotime($specialtodate))) {
                    // $RegularPrice = $specialprice;
                    if($customerDataGid!=2)
                        $price = $specialprice * 2.25;
                    else
                        $price = $specialprice;
                    // echo 'product has a special price';
                }
                // echo $price;
                // die;
                // $_product->setCustomPrice($price);
            }
            if($_product->getTierPrices()){
                $tier_price = $_product->getTierPrice();
                if(count($tier_price) > 0){
                    foreach($tier_price as $k=>$v){
                        $v = (int)$tier_price[$k]['price_qty'];
                        $qty = round($v);
                        if($qty<=$cqty){
                            if($customerDataGid!=2)
                                $price = 2.25*$tier_price[$k]['price'];
                            else    
                                $price = $tier_price[$k]['price'];
                        }
                    }
                }
                
            }
                   
        }
        if($customerDataGid!=2){
            $price = round($price);
        }
        // echo $price;
        // die;
        //Set custom price
        // echo $price;
        if($price==0){
            return;
        }else{
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
        
        // return;
        
    }
}