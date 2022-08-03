<?php
namespace Magenest\InstagramShop\Block\Adminhtml\Photo\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Class Image
 * @package Magenest\InstagramShop\Block\Adminhtml\Photo\Renderer
 */
class Image extends AbstractRenderer
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $imageUrl = $this->_getValue($row);

        return '<img src="' . $imageUrl . '" width="150" height="150"/>';
    }
}
