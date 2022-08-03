<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class InputField extends Field
{
    /**
     * create element for Account Id field in store configuration
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setReadonly(true);
        return parent::_getElementHtml($element);
    }
}
