<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

/**
 * Class GetPhotoButton
 * @package Magenest\InstagramShop\Block\Adminhtml\System\Config\Form
 */
class GetPhotoButton extends Button
{
    /**
     * create element for Tags Subscribe button in store configuration
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setData([
            'type' => 'button',
            'class' => 'action-default scalable action-save action-secondary',
            'value' => 'Get Photos Now',
            'onclick' => "setLocation('" . $this->_backendUrl->getUrl('instagram/instagram/getPhoto') . "')"
        ]);

        return $element->getElementHtml();
    }
}
