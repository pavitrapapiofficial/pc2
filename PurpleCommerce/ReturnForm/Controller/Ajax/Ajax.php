<?php declare(strict_types=1);


namespace PurpleCommerce\ReturnForm\Controller\Ajax;


use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class Ajax extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    //const XML_PATH_EMAIL_RECIPIENT = 'manisha.sain@purplecommerce.com';
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $storeManager;
    protected $_escaper;
    
    protected $_downloader;
 
    /**
     * @var Magento\Framework\Filesystem\DirectoryList
     */
    protected $_directory;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $directory,
            \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_downloader =  $fileFactory;
        $this->directory = $directory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
 
    public function execute()
    {
       ini_set("display_errors","1");
       if($this->customerSession->isLoggedIn()){
        
       $customerGroupId = $this->customerSession->getCustomer()->getGroupId();
       
       if($customerGroupId==2){
            $fileName = 'priceSheet.pdf';
            echo '<br/>'.$file = $this->directory->getPath("media")."/downloadable/".$fileName;

            // do your validations

            /**
             * do file download
             */
            return $this->_downloader->create(
                $fileName,
                    [
                    'type' => 'string',
                    'value' => @file_get_contents($file),
                    'rm' => true
                    ],

                    $this->directory::VAR_DIR
            );
       } else{
           return false;
       }
    } else{
        return false;
    }
    }
    
}

