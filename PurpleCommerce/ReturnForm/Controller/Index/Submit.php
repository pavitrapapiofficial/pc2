<?php declare(strict_types=1);


namespace PurpleCommerce\ReturnForm\Controller\Index;


use Magento\Framework\Controller\ResultFactory;

class Submit extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    //const XML_PATH_EMAIL_RECIPIENT = 'manisha.sain@purplecommerce.com';
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $storeManager;
    protected $_escaper;
    
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
            \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
                $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }
        
        
    public function execute()
    {
        ini_set("display_errors","1");
        // 1. POST request : Get booking data
        $post = (array) $this->getRequest()->getPost();

        if (!empty($post)) {
            // Retrieve your form data
//            $firstname   = $post['firstname'];
//            $lastname    = $post['lastname'];
//            $phone       = $post['phone'];
//            $bookingTime = $post['bookingTime'];

            // Doing-something with...
        $name = $_REQUEST['name'];
        $email = $_REQUEST['email'];
        $orderno = $_REQUEST['orderno'];
        $telephone = $_REQUEST['telephone'];
        $products = $_REQUEST['productname'];
        $productsqty = $_REQUEST['productqty'];
        $reason = $_REQUEST['Reason'];
        $comment = $_REQUEST['comment'];
        $produc_html ='';
        if(count($products)>0){
            foreach ($products as $key => $product_name) {
                $produc_html .="Product name: $product_name ($productsqty[$key]),\r\n\r\n&nbsp;&nbsp;"; 
            }
        }

            
            $content['send_to_email']=array('manisha.sain@purplecommerce.com');
            $content['send_to_name']='Manisha';
            $content['orderno'] = $orderno;
                        $content['name'] = $name;
                        $content['email'] = $email;
                        $content['phone'] = $telephone;
                        $content['reason'] = $reason;
                        $content['comment'] = $comment;
            $content['contents']=rtrim($produc_html,",\r\n\r\n&nbsp;&nbsp;");
            
            $this->sendmessage(6,$content);
                        //echo "success";
                        //die;
            // Display the succes form validation message
            $this->messageManager->addSuccessMessage('Your request has been received. We will get back to you shortly.');

            // Redirect to your form page (or anywhere you want...)
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('../../../returnconfirmation');
            //$resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }
        // 2. GET request : Render the booking page 
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
    
    public function sendmessage($templateId,$content){
        try{
                //$to_name = Mage::getStoreConfig('trans_email/ident_general/name');
                //$to_mail = Mage::getStoreConfig('trans_email/ident_general/email');
                $to_name = "Jacarandaliving";
                $to_mail = 'info@jacarandaliving.com';
                
                
                $email_id=$to_mail;
                $content['send_to_name'] = $to_name;
               
                $error = false;

                $sender = [
                        'name' => $this->_escaper->escapeHtml($to_name),
                        'email' => $this->_escaper->escapeHtml($to_mail),
                ];
                
                $data = ['data' => $content['contents'], 
                        'orderno' => $content['orderno'],
                        'name' => $content['name'],
                    'email' => $content['email'],
                    'phone' => $content['phone'],
                    'reason' => $content['reason'],
                    'comment' => $content['comment']
                        ];
//                            
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
                $transport = 
                        $this->_transportBuilder
                        ->setTemplateIdentifier($templateId) // Send the ID of Email template which is created in Admin panel
                        ->setTemplateOptions(
                                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, // using frontend area to get the template file
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,]
                        )
                        //->setTemplateVars(['data' => $postObject])
                        ->setTemplateVars(['contents'=>$postObject])
                        ->setFrom($sender)
                        ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                        ->addCc('manisha.sain@purplecommerce.com')
                        ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
        
    }
}

