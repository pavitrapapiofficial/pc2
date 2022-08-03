<?php
namespace Magenest\InstagramShop\Model;

use Magento\Framework\DataObject\IdentityInterface;
/**
 * Class TaggedPhoto
 * @package Magenest\InstagramShop\Model
 */
class TaggedPhoto extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const TYPE           = 2;
    const PHOTO_ID_KEY   = 'photo_id';
    const SOURCE_KEY     = 'source';
    const TAG_NAME_KEY   = 'tag_name';
    const USER_KEY       = 'user';
    const URL_KEY        = 'url';
    const CAPTION_KEY    = 'caption';
    const MIN_TAG_ID_KEY = 'min_tag_id';
    const LIKES_KEY      = 'likes';
    const COMMENTS_KEY   = 'comments';
    const CACHE_TAG = 'magenest_instagramshop_taggedphoto';
    protected $_eventPrefix = 'magenest_instagramshop_taggedphoto';
    protected $_eventObject = 'tagged_photo';

    protected function _construct()
    {
        $this->_init('Magenest\InstagramShop\Model\ResourceModel\TaggedPhoto');
    }

    /**
     * Get identities
     * @return array|string[]
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];
        if(!$this->getId() || $this->isDeleted() || $this->dataHasChangedFor(self::PHOTO_ID_KEY)){
            $identities[] = self::CACHE_TAG;
        }
        return array_unique($identities);
    }

    /**
     * @param $mediaInfo array
     * @param $tag string
     * @param $minTagId int
     */
    public function setDataViaServer($mediaInfo, $tag, $minTagId)
    {
        if (!$this->getId()) {
            $this->setPhotoId($mediaInfo['id'])->setUrl($mediaInfo['permalink']);
        }

        $this->setLikes($mediaInfo['like_count'])
            ->setComments($mediaInfo['comments_count'])
            ->setCaption($mediaInfo['caption'])
            ->setSource($mediaInfo['media_url'])
            ->setTagName($tag)
            ->setMinTagId($minTagId);
    }
    /**
     * @return string
     */
    public function getPhotoId()
    {
        return $this->getData(self::PHOTO_ID_KEY);
    }

    /**
     * @param string $photoId
     * @return $this
     */
    public function setPhotoId($photoId)
    {
        $this->setData(self::PHOTO_ID_KEY, $photoId);
        return $this;
    }
    /**
     * @return int
     */
    public function getMinTagId()
    {
        return $this->getData(self::MIN_TAG_ID_KEY);
    }

    /**
     * @param int $minTagId
     * @return $this
     */
    public function setMinTagId($minTagId)
    {
        $this->setData(self::MIN_TAG_ID_KEY, $minTagId);
        return $this;
    }
    /**
     * @return string
     */
    public function getTagName()
    {
        return $this->getData(self::TAG_NAME_KEY);
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function setTagName($tag)
    {
        $this->setData(self::TAG_NAME_KEY, $tag);
        return $this;
    }
    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getData(self::SOURCE_KEY);
    }

    /**
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->setData(self::SOURCE_KEY, $source);
        return $this;
    }
    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->getData(self::CAPTION_KEY);
    }

    /**
     * @param string $caption
     * @return $this
     */
    public function setCaption($caption)
    {
        $this->setData(self::CAPTION_KEY, $caption);
        return $this;
    }
    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->getData(self::LIKES_KEY);
    }

    /**
     * @param int $likes
     * @return $this
     */
    public function setLikes($likes)
    {
        $this->setData(self::LIKES_KEY, $likes);
        return $this;
    }
    /**
     * @return string
     */
    public function getComments()
    {
        return $this->getData(self::COMMENTS_KEY);
    }

    /**
     * @param string $comments
     * @return $this
     */
    public function setComments($comments)
    {
        $this->setData(self::COMMENTS_KEY, $comments);
        return $this;
    }
}
