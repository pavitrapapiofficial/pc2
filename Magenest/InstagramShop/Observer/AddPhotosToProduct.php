<?php

namespace Magenest\InstagramShop\Observer;

use Magenest\InstagramShop\Model\PhotoFactory;
use Magenest\InstagramShop\Ui\DataProvider\Product\Form\Modifier\InstagramPhotos;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AddPhotosToProduct
 * @package Magenest\InstagramShop\Observer
 */
class AddPhotosToProduct implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var PhotoFactory
     */
    protected $photoFactory;

    /**
     * AddPhotosToProduct constructor.
     * @param RequestInterface $request
     * @param PhotoFactory $photoFactory
     */
    public function __construct(
        RequestInterface $request,
        PhotoFactory $photoFactory
    ) {
        $this->_request     = $request;
        $this->photoFactory = $photoFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var Product $product */
            $product  = $observer->getEvent()->getProduct();
            $postData = $this->_request->getPostValue();
            $photos   = [];
            if (isset($postData['links'][InstagramPhotos::LINK_TYPE])) {
                $photos = array_column($postData['links'][InstagramPhotos::LINK_TYPE], 'id');
            }
            if (count($photos) > 3) {
                $content = __('You can only add maximum 3 photos per product');
                ObjectManager::getInstance()->get(ManagerInterface::class)->addNoticeMessage($content);
                throw new CouldNotSaveException($content);
            }
            $oldPhotoIds = $product->getData(InstagramPhotos::INSTAGRAM_PHOTOS_ATTRIBUTE_CODE);
            if ($oldPhotoIds) {
                $oldPhotoIds  = explode(', ', $oldPhotoIds);
                $diffPhotoIds = array_diff($oldPhotoIds, $photos);
                foreach ((array)$diffPhotoIds as $photoId) {
                    if ($photoId) {
                        $this->addProductToInstagram($photoId, $product, true);
                    }
                }
            }
            foreach ($photos as $photo) {
                if ($photo) {
                    $this->addProductToInstagram($photo, $product);
                }
            }
            $product->setData(InstagramPhotos::INSTAGRAM_PHOTOS_ATTRIBUTE_CODE, implode(', ', $photos));
        } catch (\Exception $e) {
            ObjectManager::getInstance()->get(LoggerInterface::class)->debug('Assign Instagram Photos Exception: ' . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param Product $product
     * @param bool $unset
     * @throws \Exception
     */
    public function addProductToInstagram($id, $product, $unset = false)
    {
        $photo      = $this->photoFactory->create()->load($id);
        $productIds = $photo->getProductIds();

        $productIds = $productIds == '' ? [] : explode(', ', $productIds);
        if (!in_array($product->getId(), array_values($productIds))) {
            $productIds[] = $product->getId();
        } else if ($unset) {
            unset($productIds[array_search($product->getId(), $productIds)]);
        }
        $photo->setProductId(implode(', ', $productIds))
            ->save();
    }
}