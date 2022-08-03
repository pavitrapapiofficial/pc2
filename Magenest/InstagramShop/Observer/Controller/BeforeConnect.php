<?php

namespace Magenest\InstagramShop\Observer\Controller;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BeforeConnect extends AbstractFlushCache implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->flushCache();
    }
}