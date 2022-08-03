<?php

namespace Magenest\InstagramShop\Model;

use Magento\Framework\DataObject;

/**
 * Class Cron
 * @package Magenest\InstagramShop\Model
 */
class Cron
{

    /**
     * @var Client
     */
    protected $_client;
    protected $_logger;

    /**
     * @var PhotoFactory
     */
    protected $_photoFactory;

    /**
     * @var TaggedPhotoFactory
     */
    protected $_taggedPhotoFactory;

    /**
     * Cron constructor.
     * @param Client $client
     * @param PhotoFactory $photoFactory
     * @param TaggedPhotoFactory $taggedPhotoFactory
     */
    public function __construct(
        \Magenest\InstagramShop\Model\Client $client,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\InstagramShop\Model\PhotoFactory $photoFactory,
        \Magenest\InstagramShop\Model\TaggedPhotoFactory $taggedPhotoFactory
    )
    {
        $this->_client = $client;
        $this->_logger = $logger;
        $this->_photoFactory = $photoFactory;
        $this->_taggedPhotoFactory = $taggedPhotoFactory;
    }

    /**
     * Get tagged photo info from Instagram
     */
    public function getTaggedPhotos()
    {
        // api: https://api.instagram.com/v1/tags/{tag-name}/media/recent?access_token=ACCESS-TOKEN
        $tags = $this->_client->getTags();

        foreach ($tags as $tag) {

            /** Get min ID (get photos after this Min Id) */
            $minTagId = $this->_taggedPhotoFactory->create()
                ->getCollection()
                ->addFieldToFilter('tag_name', $tag)
                ->getLastItem()
                ->getMinTagId();

            if (!empty($minTagId)) {
                $param = ['min_tag_id' => $minTagId];
            } else {
                $param = ['count' => 20];
            }

            $handle = sprintf('/tags/%s/media/recent', $tag);
            $photos = $this->_client->api($handle, 'GET', $param);

            if (isset($photos['pagination']['min_tag_id']) && isset($photos['data']) && count($photos['data'])) {
                $minTagId = $photos['pagination']['min_tag_id'];
                $photos = array_reverse($photos['data']);
                foreach ($photos as $photo) {
                    if ($photo['type'] == 'image' || $photo['type'] == 'carousel') {
                        $this->savePhoto($photo, $tag, $minTagId);
                    }
                }
            }
        }
    }

    /**
     * Get tagged photo info from Instagram
     */
    public function getPhotoByTags() {
        $tags = $this->_client->getTags();
        $allPhotos = $this->getPhotos();
        if (empty($allPhotos)) return;

        foreach ($tags as $tag) {
            $photos = array_reverse($allPhotos);
            foreach ($photos as $photo) {
                if (($photo['type'] == 'image' || $photo['type'] == 'carousel') &&
                    array_key_exists('tags', $photo) && !empty($photo['tags']) && in_array($tag, $photo['tags'])
                ) {
                   $this->savePhoto($photo, $tag, null);
                }
            }
        }
    }

    /**
     * Get tagged photos in instagram to save on server
     */
    protected function getPhotos()
    {
        $endpoint = '/users/self/media/recent';
        $param = ['count' => 100000];
        $photo = $this->_client->api($endpoint, 'GET', $param);
        $photoInfo = [];
        if (isset($photo['data']))
            $photoInfo = $photo['data'];

        return $photoInfo;
    }

    /**
     * save photo info to database
     * @param array $photo
     * @param string $tag
     * @param string $minTagId
     * @throws \Exception
     */
    public function savePhoto($photo, $tag, $minTagId)
    {
        $taggedPhoto = $this->_taggedPhotoFactory->create()->load($photo['id'], 'photo_id');
        if (!$taggedPhoto->getId()) {
            $data = [
                'photo_id' => $photo['id'],
                'url' => $photo['link'],
                'source' => $photo['images']['standard_resolution']['url'],
                'caption' => $photo['caption']['text'],
                'user' => '@' . $photo['user']['username'],
                'tag_name' => $tag,
                'min_tag_id' => $minTagId,
                'likes' => $photo['likes']['count'],
                'comments' => $photo['comments']['count'],
                'created_at' => date('Y-m-d', $photo['created_time']),
            ];
        } else {
            $data = [
                'likes' => $photo['likes']['count'],
                'comments' => $photo['comments']['count'],
                'caption' => $photo['caption']['text'],
                'source' => $photo['images']['standard_resolution']['url']
            ];
        }
        $taggedPhoto->addData($data)
            ->save();
    }

    /**
     * Update photos likes and comments
     */
    public function updateInfo()
    {
        /** Update instagram photo likes & comments */

        $collection = $this->_photoFactory->create()->getCollection();
        foreach ($collection as $photo) {
            $info = $this->_client->api('/media/' . $photo->getPhotoId(), 'GET');
            if (isset($info['data'])) {
                $info = $info['data'];
            } else {
                continue;
            }

            if (isset($info['likes']['count'])) {
                $photo->setLikes($info['likes']['count']);
                $photo->setComments($info['comments']['count']);
                $photo->save();
            }
        }

        /** Update tagged photo likes & comments */
        $collection = $this->_taggedPhotoFactory->create()->getCollection();
        foreach ($collection as $photo) {
            $info = $this->_client->api('/media/' . $photo->getPhotoId(), 'GET');
            if (isset($info['data'])) {
                $info = $info['data'];
            } else {
                continue;
            }

            if (isset($info['likes']['count'])) {
                $photo->setLikes($info['likes']['count']);
                $photo->setComments($info['comments']['count']);
                $photo->save();
            }
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getAllPhotos()
    {
        // api: https://api.instagram.com/v1/users/self/media/recent/?access_token=ACCESS-TOKEN
        $this->_logger->debug('command run succesfully');
        $handle = '/users/self/media/recent/';
        $response = $this->_client->api($handle);
        if (isset($response['data']) && count($response['data'])) {
            $allPhotos = $response['data'];
            foreach ($allPhotos as $data) {
                $object = new DataObject($data);
                $photo = $this->_photoFactory->create()->load($object->getId(), 'photo_id');
                if (!$photo->getId()) {
                    $photo->setPhotoId($object->getId())
                        ->setUrl($object->getLink())
                        ->setSource($object->getImages()['standard_resolution']['url'])
                        ->setCaption($object->getCaption()['text'])
                        ->setComments($object->getComments()['count'])
                        ->setLikes($object->getLikes()['count'])
                        ->save();
                } else {
                    $photo->setLikes($object->getLikes()['count'])
                        ->setComments($object->getComments()['count'])
                        ->setSource($object->getImages()['standard_resolution']['url'])
                        ->save();
                }
            }
        }
    }
}
