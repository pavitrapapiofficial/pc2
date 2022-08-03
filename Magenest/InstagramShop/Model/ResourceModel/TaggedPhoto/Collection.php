<?php
namespace Magenest\InstagramShop\Model\ResourceModel\TaggedPhoto;

/**
 * Class Collection
 * @package Magenest\InstagramShop\Model\ResourceModel\TaggedPhoto
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\InstagramShop\Model\TaggedPhoto', 'Magenest\InstagramShop\Model\ResourceModel\TaggedPhoto');
    }
}
