<?php

namespace Magenest\InstagramShop\Model;

class CustomCron
{
    protected $_gphoto;
    protected $_logger;
    public function __construct(
        \Magenest\InstagramShop\Controller\Adminhtml\Instagram\GetPhoto $gphoto,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_gphoto = $gphoto;
        $this->_logger = $logger;
    }

	public function execute()
	{
        $this->_gphoto->execute();
		$this->_logger->debug('command run succesfully');

		return $this;

	}
}