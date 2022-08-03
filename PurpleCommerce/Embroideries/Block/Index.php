<?php
namespace PurpleCommerce\Embroideries\Block;



class Index extends \Magento\Framework\View\Element\Template
{   
     
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_customerSession;

    protected $_varFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
        
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->productrepository = $productrepository;
        $this->_varFactory = $varFactory;
        $this->_customerSession = $customerSession;
        $this->_productCollectionFactory = $productCollectionFactory;
    }
     
    public function getCategory(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');// Instance of Category Model
        $categoryId = 138; // YOUR CATEGORY ID
        $category = $categoryFactory->create()->load($categoryId);
        $parentCategories = $category->getParentCategories();
        $childrenCategories = $category->getChildrenCategories();

        
        
        $alldata = [];
        $prodData = [];
        $i=0;
        foreach ($parentCategories as $pcategory){
            $alldata['parentcatname']=$pcategory->getName();
        }
        foreach ($childrenCategories as $k=>$ccategory){
            $c = 0;
            $alldata[$ccategory->getName()]['catid']=$k;
            // $alldata[$ccategory->getName()][$i]
            $categoryProdDetail = $categoryFactory->create()->load($k);
            $categoryProducts = $categoryProdDetail->getProductCollection()
                                ->addAttributeToSelect('*')
                                ->addAttributeToSort('position', 'ASC');
            foreach ($categoryProducts as $j=>$product) {
                $alldata[$ccategory->getName()][$c]['prodid'] = $j;
                $alldata[$ccategory->getName()][$c]['prodname'] = $product->getName();
                $alldata[$ccategory->getName()][$c]['prodImage'] = $this->_storeManager->getStore()->getBaseUrl().'pub/media/catalog/product'.$product->getImage();
                $c++;
            }
            $i++;
            // $alldata[$ccategory->getName()]['prod']=$prodData;
        }
        
        return $alldata;
    }

    public function getProductCollectionByCategories($ids)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => ids]);
        return $collection;
    }

    public function getConvertedDate($userDate){
        $dateTimeZone = $this->timezone->date(new \DateTime($userDate))->format('d/m/Y');
        return $dateTimeZone;
    }

    public function getVariableValue() {
        $var = $this->_varFactory->create();
        $var->loadByCode('embroideries_json');
        return $var->getValue('text');
    }
    public function setVariableValue($json){
        $var = $this->_varFactory->create();
        $var->loadByCode('embroideries_json');
        $var->setPlainValue($json)
         ->save();
    }
    public function getProductImageUsingCode($productid){
        $store = $this->_storeManager->getStore();
        $product = $this->productrepository->getById($productid);

        $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getImage();
        $productUrl = $product->getProductUrl();
        return $productUrl;
    }

    public function getCustomerGroup(){
        if($this->_customerSession->isLoggedIn()):
            $customerGroup=$this->_customerSession->getCustomer()->getGroupId();
        endif;
    }
    
}