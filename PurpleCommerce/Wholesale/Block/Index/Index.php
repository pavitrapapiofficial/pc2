<?php declare(strict_types=1);


namespace PurpleCommerce\Wholesale\Block\Index;


class Index extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
    }

    public function getCustomer()
    {
        return $this->_customerSession;
    }
}

