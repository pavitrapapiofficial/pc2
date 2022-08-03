<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magento\Framework\App\Cache\Type\Config;
use Magenest\InstagramShop\Helper\Helper;
use Magenest\InstagramShop\Model\Client;

class GetAccessPage extends \Magento\Backend\App\Action
{
    protected $_clientModel;

    /** @var \Magento\Framework\App\Cache\Manager  */
    protected $_cacheManager;

    /** @var \Magento\Framework\App\Config\Storage\WriterInterface  */
    protected $_writer;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_jsonFramework;

    /**
     * AbstractClient constructor.
     *
     * @param \Magenest\InstagramShop\Model\Client $client
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writer
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\InstagramShop\Model\Client $client,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $writer,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\Serializer\Json $jsonFramework,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_clientModel = $client;
        $this->_cacheManager = $cacheManager;
        $this->_writer = $writer;
        $this->_logger = $logger;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context);
    }

    public function execute()
    {
        try{
            $params = $this->getRequest()->getParams();
            if(isset($params['message']) && isset($params['access_token'])){
                if($params['message'] == '1'){
                    $this->_writer->save(Helper::FACEBOOK_TOKEN, $params['access_token']);
                    $this->cleanConfigCache();
                    $this->getInstagramInformation();
                    $this->messageManager->addSuccessMessage(__('Get Facebook Access Token Successfully!'));
                }else{
                    $this->_logger->critical("Connect Instagram: access_token param is null");
                    $this->messageManager->addSuccessMessage(__('Cannot get Access Token!'));
                }
            }else{
                $this->_logger->critical("Connect Instagram: messager param not exit.");
                throw new \Exception(__("Something went wrong!"));
            }
        }catch (\Exception $exception){
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $this->cleanConfigCache();
        $this->_redirect(Client::INSTAGRAM_SHOP_CONFIGURATION_SECTION);
    }

    /** Clean config cache */
    public function cleanConfigCache()
    {
        $this->_cacheManager->clean([Config::TYPE_IDENTIFIER]);
    }

    /**
     * @throws \Exception
     */
    private function getInstagramInformation()
    {
        try{
            $pageId = $this->_clientModel->getPageId();
            $this->_writer->save(Helper::FACEBOOK_PAGEID, $pageId);
            $instBusinessId = $this->_clientModel->getPageIdInstagram($pageId);
            $this->_writer->save(Helper::INSTA_BUSSINESS_ID, $instBusinessId);
            $response = $this->_clientModel->getInstagramInfo($instBusinessId);
            $this->_writer->save(Helper::INSTA_ACCOUNT, $this->_jsonFramework->serialize($response));
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}