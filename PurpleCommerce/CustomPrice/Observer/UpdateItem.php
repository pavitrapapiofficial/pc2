<?php
namespace PurpleCommerce\CustomPrice\Observer;
use \Magento\Framework\Message\ManagerInterface ;
use \Magento\Checkout\Model\Cart;
class UpdateItem implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    protected $_logger;
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
        \Psr\Log\LoggerInterface $logger,
        // PurpleCommerce\CustomPrice\Observer $customPrice,
        ManagerInterface $messageManager
    ) {
        // $this->customPrice = $customPrice;
        $this->quote = $checkoutSession->getQuote();
        $this->subject = $subject;
        $this->_logger = $logger;
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session'); 
        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
        $customEmail = $customerSession->getCustomer()->getEmail();
        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer
        $quote = $this->subject->getQuote();
        $cqty = 1;
        $price=0;
        $data = $this->subject->getQuote()->getAllItems();
        foreach($data as $item) {
            $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            $cqty=1;
            $price = $_product->getPrice();
            $specialprice = $_product->getData('special_price');
            // var_dump($_product->getTierPrice());
            // die;
            if(!empty($item->getQty())){
                $cqty = $item->getQty();
            } 
            if($_product->getSpecialPrice()){
                if($customerDataGid!=2)
                    $price = $specialprice * 2.25;
                else
                    $price = $specialprice;
            }
            // if($customEmail == 'nitin.mittal@purplecommerce.com'){
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
            // }
           
                   
        }
        if($customerDataGid!=2){
            $price = round($price);
        }
        // print_r($price);
        // die;
        //Set custom price
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
        
        
    }
}