<?php
namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

class ReadonlyField extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * create element for Access token field in store configuration
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setReadonly(true);
        $element->setClass('readonly-field');
        return parent::_getElementHtml($element);
    }
}