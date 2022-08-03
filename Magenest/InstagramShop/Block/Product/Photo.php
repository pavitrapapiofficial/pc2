<?php

namespace Magenest\InstagramShop\Block\Product;

use Magenest\InstagramShop\Model\PhotoFactory;
use Magenest\InstagramShop\Ui\DataProvider\Product\Form\Modifier\InstagramPhotos;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Photo extends Template
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var PhotoFactory
     */
    protected $_photoFactory;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Photo constructor.
     * @param Template\Context $context
     * @param PhotoFactory $photoFactory
     * @param ProductFactory $productFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PhotoFactory $photoFactory,
        ProductFactory $productFactory,
        Registry $registry,
        array $data = [])
    {
        $this->_photoFactory   = $photoFactory;
        $this->_productFactory = $productFactory;
        $this->_registry       = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param $productId
     * @return \Magenest\InstagramShop\Model\Photo[]
     */
    public function getPhotosByProduct($productId = '')
    {
        if ($productId == '')
            $productId = $this->getProduct()->getId();
        $ids    = $this->_productFactory->create()->load($productId)
            ->getData(InstagramPhotos::INSTAGRAM_PHOTOS_ATTRIBUTE_CODE);
        $result = [];
        if ($ids == '')
            return $result;
        foreach (explode(', ', $ids) as $id) {
            $result[] = $this->_photoFactory->create()->load($id);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getCurrentProductUrl()
    {
        $currentProduct = $this->getProduct();
        return $currentProduct->getUrlModel()->getUrl($currentProduct);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return boolean
     */
    public function isAddInstagramToProduct()
    {
        return (bool)$this->_scopeConfig->getValue('magenest_instagram_shop/general/add_photos_to_product_view');
    }

    public function getViewFullGalleryTitle()
    {
        return (string)$this->_scopeConfig->getValue('magenest_instagram_shop/general/button_title');
    }

    public function getViewFullGalleryCss()
    {
        return (string)$this->_scopeConfig->getValue('magenest_instagram_shop/general/button_css');
    }

    public function getBlockTitle()
    {
        return (string)$this->_scopeConfig->getValue('magenest_instagram_shop/general/block_title');
    }

    public function getBlockContent()
    {
        return (string)$this->_scopeConfig->getValue('magenest_instagram_shop/general/block_content');
    }

    public function getHashTag()
    {
        return (string)$this->_scopeConfig->getValue('magenest_instagram_shop/general/hash_tag');
    }
}