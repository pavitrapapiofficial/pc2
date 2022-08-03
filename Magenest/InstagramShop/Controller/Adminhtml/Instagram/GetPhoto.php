<?php

namespace Magenest\InstagramShop\Controller\Adminhtml\Instagram;

/**
 * Class GetPhoto
 * @package Magenest\InstagramShop\Controller\Adminhtml\Instagram
 */
class GetPhoto extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magenest_InstagramShop::instagram';

    /** @var \Magenest\InstagramShop\Model\CronJob  */
    protected $_cronJob;

    /**
     * GetPhoto constructor.
     *
     * @param \Magenest\InstagramShop\Model\CronJob $cronJob
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\InstagramShop\Model\CronJob $cronJob,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_cronJob = $cronJob;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        // echo "controller";
        // die;
        try {
            $this->_cronJob->getAllMedia();
            $this->messageManager->addSuccessMessage(__("Get photos successfully!"));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->_redirect('instagram/instagram');
    }
}
