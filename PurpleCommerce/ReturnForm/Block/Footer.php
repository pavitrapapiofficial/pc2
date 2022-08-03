<?php declare(strict_types=1);


namespace PurpleCommerce\ReturnForm\Block;

use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Footer extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;
    protected $_storeManager;
    protected $httpContext;

    protected $cacheTypeList;
    protected $cacheFrontendPool;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function getCustomerIsLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }

    public function getCustomerName()
    {
        return $this->httpContext->getValue('customer_name');
    }

    public function getCustomerEmail()
    {
        return $this->httpContext->getValue('customer_email');
    }

    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue('customer_group_id');
    }

    /**
     * Check customer Login or not
     */
    public function checkCustomerLogin()
    {
        $customer_data = [];

        if($this->getCustomerIsLoggedIn()){
            return $customerGroupId = $this->getCustomerGroupId();

        }
    }


}