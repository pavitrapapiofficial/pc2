<?php

namespace Magenest\InstagramShop\Controller\Adminhtml\Instagram;

use Magenest\InstagramShop\Model\Cron;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Update
 * @package Magenest\InstagramShop\Controller\Adminhtml\Instagram
 */
class Update extends Action
{
    const ADMIN_RESOURCE = 'Magenest_InstagramShop::instagram';

    /** @var \Magenest\InstagramShop\Model\CronJob  */
    protected $_cronJob;

    /**
     * GetPhoto constructor.
     *
     * @param Context $context
     * @param \Magenest\InstagramShop\Model\CronJob $cronJob
     */
    public function __construct(
        Context $context,
        \Magenest\InstagramShop\Model\CronJob $cronJob
    ) {
        $this->_cronJob = $cronJob;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->_cronJob->getPhotoByTags();
            $this->messageManager->addSuccessMessage(__("Get photo by tags successfully!"));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while pulling hashtag photos from Instagram.') . $e->getMessage());
        }
        return $this->_redirect('instagram/tag');
    }
}
