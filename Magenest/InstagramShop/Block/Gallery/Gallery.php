<?php
namespace Magenest\InstagramShop\Block\Gallery;

use Magenest\InstagramShop\Model\Client;
use Magenest\InstagramShop\Model\PhotoFactory;
use Magenest\InstagramShop\Model\TaggedPhotoFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;
/**
 * Class Gallery
 * @package Magenest\InstagramShop\Block\Gallery
 */
class Gallery extends Template implements IdentityInterface
{
    /**
     * @var PhotoFactory
     */
    protected $_photoFactory;

    /**
     * @var TaggedPhotoFactory
     */
    protected $_taggedPhotoFactory;

    /**
     * @var Client
     */
    protected $_client;

    protected $_collection = null;
    protected $photoPerPages = 12;

    /**
     * PhotoList constructor.
     * @param Template\Context $context
     * @param PhotoFactory $photoFactory
     * @param TaggedPhotoFactory $taggedPhotoFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PhotoFactory $photoFactory,
        TaggedPhotoFactory $taggedPhotoFactory,
        Client $client,
        array $data = []
    ) {
        $this->_client = $client;
        $this->_photoFactory = $photoFactory;
        $this->_taggedPhotoFactory = $taggedPhotoFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setCollection($this->getViewParam());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [
            \Magenest\InstagramShop\Model\Photo::CACHE_TAG,
            \Magenest\InstagramShop\Model\TaggedPhoto::CACHE_TAG
        ];
        foreach ($this->getCollection()->getItems() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }

    /**
     * @return $this|Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'instagram.photo.list.pager'
        );
        $pager->setUseContainer(false)
            ->setShowPerPage(false)
            ->setShowAmounts(false)
            ->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )
            ->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )
            ->setLimit($this->photoPerPages)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }

    /**
     * Set photos collection
     */
    public function setCollection($tag)
    {
        if (empty($tag)) {
            $this->_collection = $this->_photoFactory->create()
                ->getCollection()
                ->setOrder('id', 'DESC');
        } else {
            $this->_collection = $this->_taggedPhotoFactory->create()
                ->getCollection()
                ->addFieldToFilter('tag_name', $tag)
                ->setOrder('id', 'DESC');
        }
    }

    public function getCollection()
    {

        return $this->_collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {

        return $this->getChildHtml('pager');
    }

    /**
     * return array of tags from store configuration
     * @return array
     */
    public function getTags()
    {

        return $this->_client->getTags();
    }

    /**
     * @return string
     */
    public function getViewParam()
    {

        return $this->getRequest()->getParam('view');
    }

    /**
     * @return int
     */
    public function getPageParam()
    {

        return $this->getRequest()->getParam('page');
    }
}
