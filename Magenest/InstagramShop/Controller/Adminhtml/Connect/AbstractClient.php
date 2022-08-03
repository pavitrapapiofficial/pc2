<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magento\Framework\App\Cache\Type\Config;

abstract class AbstractClient extends \Magento\Backend\App\Action
{
    protected $_clientModel;

    /** @var \Magento\Framework\App\Cache\Manager  */
    protected $_cacheManager;

    /** @var \Magento\Framework\App\Config\Storage\WriterInterface  */
    protected $_writer;

    /**
     * AbstractClient constructor.
     *
     * @param \Magenest\InstagramShop\Model\Client $client
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writer
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\InstagramShop\Model\Client $client,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $writer,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_clientModel = $client;
        $this->_cacheManager = $cacheManager;
        $this->_writer = $writer;
        parent::__construct($context);
    }

    /** Clean config cache */
    public function cleanConfigCache()
    {
        $this->_cacheManager->clean([Config::TYPE_IDENTIFIER]);
    }
}