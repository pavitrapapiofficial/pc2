<?php
namespace Magenest\InstagramShop\Model\ResourceModel;

/**
 * Class Photo
 * @package Magenest\InstagramShop\Model\ResourceModel
 */
class Photo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_instagram_photo', 'id');
    }
}
