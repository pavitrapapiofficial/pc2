<?php declare(strict_types=1);


namespace PurpleCommerce\ReturnForm\Controller\Index;


use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    public function __construct(Context $context, PageFactory $resultPageFactory) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        //echo "Inside Controller";
        return $resultPage = $this->resultPageFactory->create();
        //echo "After Controller";
    }
}

