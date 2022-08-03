<?php
namespace Magenest\InstagramShop\Block\Adminhtml;

/**
 * Class TaggedPhoto
 * @package Magenest\InstagramShop\Block\Adminhtml
 */
class TaggedPhoto extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * TaggedPhoto constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->removeButton('add');
    }

    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_InstagramShop';
        $this->_controller = 'adminhtml_tag';
        parent::_construct();
    }
}
