<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    protected $_backendUrl;

    public function __construct(
        Context $context,
        UrlInterface $url,
        array $data = [])
    {
        $this->_backendUrl = $url;
        parent::__construct($context, $data);
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
}
