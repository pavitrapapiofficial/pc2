<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class UpdateButton
 * @package Magenest\InstagramShop\Block\Adminhtml\System\Config\Form
 */
class UpdateButton extends Button
{
    /**
     * create element for Tags Subscribe button in store configuration
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData([
            'type' => 'button',
            'class' => 'action-default scalable action-save action-secondary',
            'value' => 'Get Photos Now',
            'onclick' => "setLocation('" . $this->_backendUrl->getUrl('instagram/instagram/update') . "')"
        ]);

        return $element->getElementHtml();
    }
}
