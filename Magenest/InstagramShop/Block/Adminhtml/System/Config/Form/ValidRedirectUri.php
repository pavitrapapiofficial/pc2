<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;

class ValidRedirectUri extends Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $redirectUri = $this->_urlBuilder->getUrl(\Magenest\InstagramShop\Model\Client::REDIRECT_URI_PATH);
        $element->setValue($redirectUri);
        $element->setReadonly(true);
        $element->setOnclick('{this.select();document.execCommand(\'copy\')}');
        return parent::_getElementHtml($element);
    }
}