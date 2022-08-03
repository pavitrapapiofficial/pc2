<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magenest\InstagramShop\Helper\Helper;
use Magenest\InstagramShop\Model\Client;

class SaveAccessToken extends \Magenest\InstagramShop\Controller\Adminhtml\Connect\AbstractClient
{
    public function execute()
    {
        try{
            $pageId = $this->_clientModel->getPageId();
            $this->_writer->save(Helper::FACEBOOK_PAGEID, $pageId);
            $instBusinessId = $this->_clientModel->getPageIdInstagram($pageId);
            $this->_writer->save(Helper::INSTA_BUSSINESS_ID, $instBusinessId);
            $response = $this->_clientModel->getInstagramInfo($instBusinessId);
            $this->_writer->save(Helper::INSTA_ACCOUNT, json_encode($response));
            $this->cleanConfigCache();
            $this->messageManager->addSuccessMessage(__('Get instagram information successfully!'));
        }catch (\Exception $exception){
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $this->_redirect(Client::INSTAGRAM_SHOP_CONFIGURATION_SECTION);
    }
}