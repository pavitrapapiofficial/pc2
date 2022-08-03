<?php
namespace Magenest\InstagramShop\Model;

class CronJob
{
    /** @var \Magenest\InstagramShop\Model\Client  */
    protected $_client;

    /** @var \Magenest\InstagramShop\Model\PhotoFactory  */
    protected $_photoModel;

    /** @var \Magenest\InstagramShop\Model\ResourceModel\Photo  */
    protected $_photoResource;

    /** @var \Magenest\InstagramShop\Model\ResourceModel\Photo\CollectionFactory  */
    protected $_photoCollectionFactory;

    protected $_photoCollection = null;

    protected $_valueToSave = [];

    protected $_valueToUpdate = [];

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resourceConnection;

    /** @var \Magenest\InstagramShop\Model\TaggedPhotoFactory  */
    protected $_taggedPhotoFactory;

    /** @var \Magenest\InstagramShop\Model\TaggedPhoto  */
    protected $_taggedPhotoResource;

    protected $taggedPhotoItems = null;

    public function __construct(
        \Magenest\InstagramShop\Model\Client $client,
        \Magenest\InstagramShop\Model\PhotoFactory $photoFactory,
        \Magenest\InstagramShop\Model\ResourceModel\Photo $photoResource,
        \Magenest\InstagramShop\Model\ResourceModel\Photo\CollectionFactory $photoCollectionFactory,
        \Magenest\InstagramShop\Model\TaggedPhotoFactory $taggedPhotoFactory,
        \Magenest\InstagramShop\Model\ResourceModel\TaggedPhoto $taggedPhotoResource
    ){
        $this->_client = $client;
        $this->_photoModel = $photoFactory;
        $this->_photoResource = $photoResource;
        $this->_photoCollectionFactory = $photoCollectionFactory;
        $this->_taggedPhotoFactory = $taggedPhotoFactory;
        $this->_taggedPhotoResource = $taggedPhotoResource;
    }
    public function getPhotoByTags()
    {
        $tags = $this->_client->getTags();
        $allPhotos = $this->_client->getAllMedia();
        foreach ($tags as $tag) {
            $tagged = "#".$tag;
            foreach ($allPhotos as $photo) {
                if(isset($photo['caption'])){
                    if(strpos($photo['caption'], $tagged) !== false){
                        $taggedModel = $this->getTaggedPhoto($photo['id']);
                        $taggedModel->setDataViaServer($photo, $tag, null);
                        $this->_taggedPhotoResource->save($taggedModel);
                    }
                }
            }
        }
    }
    /**
     * @param $photoId
     * @return TaggedPhoto
     */
    private function getTaggedPhoto($photoId)
    {
        $items = $this->getTaggedPhotoItems();
        return isset($items[$photoId]) ? $items[$photoId] : $this->_taggedPhotoFactory->create();
    }

    /**
     * @return TaggedPhoto[]
     */
    private function getTaggedPhotoItems()
    {
        if ($this->taggedPhotoItems === null) {
            $items      = [];
            $collection = $this->_taggedPhotoFactory->create()->getCollection();
            /** @var TaggedPhoto $item */
            foreach ($collection as $item) {
                $items[$item->getPhotoId()] = $item;
            }
            $this->taggedPhotoItems = $items;
        }
        return $this->taggedPhotoItems;
    }

    public function getAllMedia()
    {
        $allPhotos = $this->_client->getAllMedia();
        foreach ($allPhotos as $photo) {
            /** @var \Magenest\InstagramShop\Model\Photo $photoModel */
            $photoModel = $this->getPhotoItem($photo['id']);
            $photoModel->setDataViaServer($photo);
            $this->_photoResource->save($photoModel);
            // echo "done";
        }
    }
    private function getPhotoItem($photoId)
    {
        $items = $this->getPhotoCollection();
        return isset($items[$photoId]) ? $items[$photoId] : $this->_photoModel->create();
    }
    public function getPhotoCollection()
    {
        if($this->_photoCollection == null){
            $this->_photoCollection = $this->_photoCollectionFactory->create()->getPhotoIdItems();
        }
        return $this->_photoCollection;
    }
}