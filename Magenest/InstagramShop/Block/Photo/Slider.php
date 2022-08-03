<?php

namespace Magenest\InstagramShop\Block\Photo;

use Magenest\InstagramShop\Model\Photo;
use Magenest\InstagramShop\Ui\DataProvider\Product\Form\Modifier\InstagramPhotos;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Widget\Block\BlockInterface;

class Slider extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * @var \Magenest\InstagramShop\Model\PhotoFactory
     */
    protected $_photoFactory;

    protected $_productFactory;

    protected $_registry;

    /**
     * Slider constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\InstagramShop\Model\PhotoFactory $photoFactory
     * @param ProductFactory $productFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\InstagramShop\Model\PhotoFactory $photoFactory,
        ProductFactory $productFactory,
        Registry $registry,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_productFactory = $productFactory;
        $this->_photoFactory = $photoFactory;
        $this->addData([
            'cache_lifetime' => isset($data['cache_lifetime']) ? $data['cache_lifetime'] : 86400,
            'cache_tags' => [\Magenest\InstagramShop\Model\Photo::CACHE_TAG]
            ]);
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|Photo[]
     */
    public function getPhotos()
    {
        return $this->_photoFactory->create()
            ->getCollection()
            ->addFieldToFilter('show_in_widget', 1)//only visibility items are selected
            ->setOrder('id', 'DESC')
            ->setPageSize(30)
            ->setCurPage(1);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getConfigSlider($config, $default = 'true')
    {
        return !is_null($config) && $config != '' && $config ? 'true' : $default;
    }

    public function getConfigSliderValue($config, $default)
    {
        return !is_null($config) && $config != '' ? (int)$config : (int)$default;
    }

    public function getViewFullGalleryTitle()
    {
        return $this->_scopeConfig->getValue('magenest_instagram_shop/general/button_title');
    }

    public function getViewFullGalleryCss()
    {
        return $this->_scopeConfig->getValue('magenest_instagram_shop/general/button_css');
    }

    public function getHoverText()
    {
        return $this->_scopeConfig->getValue('magenest_instagram_shop/general/hover_text');
    }
    public function getSharePopup(){
        $block = $this->_layout->createBlock(
            \Magento\Framework\View\Element\Template::class,
            '',
            ['data' => ['template' => 'Magenest_InstagramShop::shared/popup.phtml']]
        );
        return $block->toHtml();
    }
}
