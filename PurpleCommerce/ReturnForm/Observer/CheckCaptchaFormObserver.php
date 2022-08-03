<?php
 
namespace PurpleCommerce\ReturnForm\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCaptchaFormObserver implements ObserverInterface {

   protected $_helper;
   protected $_actionFlag;
   protected $messageManager;
   protected $_session;
   protected $_urlManager;
   protected $captchaStringResolver;
   protected $redirect;

   public function __construct(\Magento\Captcha\Helper\Data $helper, //
           \Magento\Framework\App\ActionFlag $actionFlag, //
           \Magento\Framework\Message\ManagerInterface $messageManager, //
           \Magento\Framework\Session\SessionManagerInterface $session, //
           \Magento\Framework\UrlInterface $urlManager, //
           \Magento\Framework\App\Response\RedirectInterface $redirect, //
           \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver //
   ) {
       $this->_helper = $helper;
       $this->_actionFlag = $actionFlag;
       $this->messageManager = $messageManager;
       $this->_session = $session;
       $this->_urlManager = $urlManager;
       $this->redirect = $redirect;
       $this->captchaStringResolver = $captchaStringResolver;
   }

   public function execute(\Magento\Framework\Event\Observer $observer) {
       //$formId = 'user_create'; // this form ID should matched the one defined in the layout xml
        //if(isset($_POST['form_name']) && $_POST['form_name']=='Return Form'){
            $formId = 'captcha_returnform_1';
        //}

        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect CAPTCHA.'));
                //$this->getDataPersistor()->set($formId, $controller->getRequest()->getPostValue());
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $url = $this->_urlManager->getUrl('newreturnform/index', ['_nosecret' => true]);
                $controller->getResponse()->setRedirect($this->redirect->error($url));
                //$this->redirect->redirect($controller->getResponse(), 'newreturnform/index');
            }
        }
       // $captchaModel = $this->_helper->getCaptcha($formId);

       // $controller = $observer->getControllerAction();
       // if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
       //     $this->messageManager->addError(__('Incorrect CAPTCHA'));
       //     $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
       //     $this->_session->setCustomerFormData($controller->getRequest()->getPostValue());
       //     if($formId=='captcha_returnform_1')
       //        $url = $this->_urlManager->getUrl('newreturnform/index', ['_nosecret' => true]);
       //      else
       //        $url = $this->_urlManager->getUrl('*/*/create', ['_nosecret' => true]);
       //     $controller->getResponse()->setRedirect($this->redirect->error($url));
       // }

       return $this;
   }

   

}