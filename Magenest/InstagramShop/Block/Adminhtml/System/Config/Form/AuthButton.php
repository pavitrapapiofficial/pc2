<?php

namespace Magenest\InstagramShop\Block\Adminhtml\System\Config\Form;

use Magenest\InstagramShop\Model\Client;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class AuthButton
 * @package Magenest\InstagramShop\Block\System\Config\Form
 */
class AuthButton extends \Magento\Config\Block\System\Config\Form\Field
{
    const AUTHORIZATION_BUTTON_TEMPLATE_PATH = 'system/config/authorization.phtml';
    const BUTTON_LABEL = 'Get Authorization';

    /**
     * @var Client
     */
    protected $_client;

    /**
     * AuthButton constructor.
     *
     * @param Client $client
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Client $client,
        Context $context,
        array $data = array()
    )
    {
        $this->_client = $client;
        parent::__construct($context, $data);
    }


    protected function _prepareLayout()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate(self::AUTHORIZATION_BUTTON_TEMPLATE_PATH);
        }
        return parent::_prepareLayout();
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

    /**
     * @return string
     */
    public function getButtonLabel()
    {
        return self::BUTTON_LABEL;
    }

    /**
     * create element for authorization button in store configuration
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setData([
            'html_id' => $element->getHtmlId()
        ]);

        return $this->_toHtml();
    }

    public function getLogin()
    {
        try{
            $permissions = ['manage_pages','instagram_basic','instagram_manage_comments','instagram_manage_insights','business_management']; // Optional permissions
            $url = 'https://store.magenest.com/magenest/instagram/getAccessToken/';
            $clientApi = $this->getUrl('instagram/connect/getAccessPage');
            $loginUrl = "https://www.facebook.com/dialog/oauth?client_id=420724915308275&redirect_uri=".$url."&scope=manage_pages,instagram_basic&state=".$clientApi;
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
            $loginUrl = "#";
        }

        return $loginUrl;
    }
}
