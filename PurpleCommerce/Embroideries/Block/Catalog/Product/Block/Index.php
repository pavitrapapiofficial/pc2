<?php
namespace PurpleCommerce\Embroideries\Block\Catalog\Product\Block;


class Index extends \Magento\Framework\View\Element\Template
{   
     
    
    protected $_embroideries;
    protected $_varFactory;
    protected $optionFactory;
    protected $_attributeOptionCollection;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \PurpleCommerce\Embroideries\Block\Index $embroideries,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection,
        \Magento\Catalog\Block\Product\ProductList\Related $block,
        array $data = []
        
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->productFactory = $productFactory;
        $this->_embroideries = $embroideries;
        $this->optionFactory = $optionFactory;
        $this->block=$block;
        $this->_attributeOptionCollection = $attributeOptionCollection;
    }
     
    

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    
    public function getEmbroideriesJson(){
        return $this->_embroideries->getVariableValue();
    }

    public function getEmbProdCollection($attributeId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection */
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollection->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('embroidery_categroy',$attributeId);
        return $this->getProductJson($collection);
    }

    public function getProductJson($collection){
        $items=$collection;
        $html='';
        foreach ($items as $_item){
            $available = '';
            if (!$_item->isComposite() && $_item->isSaleable() && $type == 'related') :
                 if (!$_item->getRequiredOptions()) :
                    $available = 'related-available'; 
                 endif; 
            endif;
            if ($type == 'related' || $type == 'upsell') :
                $html.='<li class="item product product-item" style="display: none;">';
            else :
                $html.='<li class="item product product-item">';
            endif; 
            $html.='<div class="product-item-info'.$available.'">
            <a target="_blank" href="http://pinterest.com/pin/create/button/?url='.urlencode($this->block->escapeUrl($this->block->getProductUrl($_item))).'&media='.'pub/media/catalog/product/' . $_item->getImage().'&description='.$this->block->escapeHtmlAttr($_item->getName()).'" class="pin-it"><img src="/pub/media/PinIt.png"></a>
                
                <a href="'.$this->block->escapeUrl($this->block->getProductUrl($_item)).'" class="product photo product-item-photo">
                    '.$this->block->getImage($_item, $image)->toHtml().'
                </a>
                <div class="product details product-item-details">
                    <strong class="product name product-item-name"><a class="product-item-link" title="'. $this->block->escapeHtmlAttr($_item->getName()).'" href="'.$this->block->escapeUrl($this->block->getProductUrl($_item)).'">
                            '.$this->block->escapeHtml($_item->getName()).'</a>
                    </strong>

                    '.$this->block->getProductPrice($_item).'';

                     if ($templateType) :
                        $html.=''.$this->block->getReviewsSummaryHtml($_item, $templateType);
                    endif; 

                    if ($showAddTo || $showCart) :
                        $html.='<div class="product actions product-item-actions">';
                            if ($showCart) :
                                $html.='<div class="actions-primary">';
                                    if ($_item->isSaleable()) :
                                        if ($_item->getTypeInstance()->hasRequiredOptions($_item)) :
                                            $html.='    <button class="action tocart primary" type="button" title="'.$this->block->escapeHtmlAttr(__('
                                            ')).'">
                                                <a href="'.$this->block->escapeUrl($this->block->getProductUrl($_item)).'" class="product photo product-item-photo" tabindex="-1" style="color: #fff; text-transform: capitalize;">view detail</a>
                                            </button>';
                                        else :
                                            $postDataHelper = $this->helper(Magento\Framework\Data\Helper\PostHelper::class);
                                            $postData = $postDataHelper->getPostData($this->block->escapeUrl($this->block->getAddToCartUrl($_item)), ['product' => $_item->getEntityId()]);
                                            
                                            $html.='<button class="action tocart primary"
                                                    type="button" title="View Detail">
                                                    <a href="'.$this->block->escapeUrl($this->block->getProductUrl($_item)).'" class="product photo product-item-photo" tabindex="-1" style="color: #fff; text-transform: capitalize;">view detail</a>
                                            </button>';
                                        endif; 
                                    else :
                                        if ($_item->getIsSalable()) :
                                            $html.='<div class="stock available"><span>In stock</span></div>';
                                        else :
                                            $html.='<div class="stock unavailable"><span>Out of stock</span></div>';
                                        endif; 
                                    endif; 
                                    $html.='</div>';
                            endif; 

                            if ($showAddTo) :
                                $html.='<div class="secondary-addto-links actions-secondary" data-role="add-to-links"></div>';
                            endif; 
                            $html.='</div>';
                    endif; 
                $html.='</div>
            </div>
            </li>';        
        }
    }

    public function ifWholeSaleCustomer(){
        if($this->_embroideries->getCustomerGroup()==2){
            return true;
        }
    }

    public function getAttributeOptionText($label)
    {
        $optionFactory = $this->optionFactory->create();
        $optionFactory->load($label); // load by option value
        $attributeId = $optionFactory->getAttributeId(); // atribute id of given option value
        $optionData = $this->_attributeOptionCollection
                        ->setPositionOrder('asc')
                        ->setAttributeFilter($attributeId)
                        ->setIdFilter($label)
                        ->setStoreFilter()
                        ->load(); // load option data by attribute id and given option value
       return $optionData->getData();
    }

    public function getOptionIdByLabel()
    {
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute('no_of_designs');
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId();
        }
        return $optionId;
    }
}