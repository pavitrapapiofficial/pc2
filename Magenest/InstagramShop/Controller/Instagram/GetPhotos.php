<?php

namespace Magenest\InstagramShop\Controller\Instagram;

/**
 * @package Magenest\InstagramShop\Controller\Instagram
 */
class GetPhotos extends \Magento\Framework\App\Action\Action
{

    /** @var \Magenest\InstagramShop\Model\CronJob  */
    protected $_cronJob;

    /**
     *
     * @param \Magenest\InstagramShop\Model\CronJob $cronJob
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magenest\InstagramShop\Model\CronJob $cronJob,
        \Magento\Framework\App\Action\Context $context
    ){
        $this->_cronJob = $cronJob;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $this->_cronJob->getAllMedia();
            echo "success2";
        } catch (\Exception $e) {
            echo "error";
        }
        
        
        // try {
        //     $this->_cronJob->getAllMedia();
        //     $this->messageManager->addSuccessMessage(__("Get photos successfully!"));
        //     echo "success";
        //     die;
        // } catch (\Exception $e) {
        //     echo "failed";
        //     die;
        //     $this->messageManager->addErrorMessage($e->getMessage());
        // }

        // return $this->_redirect('instagram/instagram');
    }
}
