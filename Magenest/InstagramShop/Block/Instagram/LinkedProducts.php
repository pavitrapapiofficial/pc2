<?php

namespace Magenest\InstagramShop\Block\Instagram;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class LinkedProducts extends Template
{
    protected $_template = 'instagram/linked_products.phtml';

    /**
     * @var Product[]
     */
    protected $productList = [];
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * LinkedProducts constructor.
     * @param Template\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $image
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $image,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->imageHelper       = $image;
    }

    /**
     * @param string $productIds
     * @return $this
     */
    public function setProductList($productIds)
    {
        if (is_string($productIds)) {
            $productIds = str_replace(' ', '', $productIds);
            $productIds = explode(',', $productIds);
        }
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                try {
                    $this->productList[] = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                }
            }
        }
        return $this;
    }

    /**
     * @return Product[]
     */
    public function getProductList()
    {
        return $this->productList;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param int $width
     * @param int $height
     * @return \Magento\Catalog\Helper\Image
     */
    public function getProductImageUrl($product, $imageId = 'product_page_image_small', $width = 150, $height = 150)
    {
        return $this->imageHelper->init($product, $imageId)
            ->setImageFile($product->getFile())
            ->resize($width, $height);
    }
}