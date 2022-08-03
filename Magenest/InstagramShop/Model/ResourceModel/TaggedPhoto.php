<?php
namespace Magenest\InstagramShop\Model\ResourceModel;

/**
 * Class TaggedPhoto
 * @package Magenest\InstagramShop\Model\ResourceModel
 */
class TaggedPhoto extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_instagram_taggedphoto', 'id');
    }
}
