<?php
namespace PurpleCommerce\Simpleshipping\Model\Carrier;
//namespace Magento\OfflineShipping\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Tablerate extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'tablerate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var string
     */
    protected $_defaultConditionName = 'package_weight';

    /**
     * @var array
     */
    protected $_conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_resultMethodFactory;

    /**
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
   public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $groupId = 1;
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }

                
            }
        }
        // Free shipping by qty
        $freeQty = 0;
        
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            $customItemPrice = 0;
            $cartHasItems = false;
            $total=[];
            // exclude Virtual products price from Package value if pre-configured
            $i=0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
                $itemPrice = 0;
                $myItemPrice = 0;
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $total = $this->compareWeight($product, $total, $i, $item);
                $i++;
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
                if ($item->getProduct() instanceof \Magento\Catalog\Model\Product) {
                  $products = $item->getProduct();
                  $groupId = $products->getCustomerGroupId();
                 
              }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }
        if (!$request->getConditionName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_defaultConditionName);
        }

        ////////////////////

        // Package weight and qty free shipping
        //$weight=$total;
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();
        $rate_price=0;
        list($result, $rate) = $this->getFinalRateShip($request, $total, $oldQty, $freeQty, $rate_price);
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();
        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        
        if (!empty($rate) && $rate['price'] >= 0 && $groupId!=2) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_resultMethodFactory->create();
            $method->setCarrier('tablerate');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('bestway');
            $method->setMethodTitle($this->getConfigData('name'));
            if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            }
            $method->setPrice($shippingPrice);
            $method->setCost($rate['cost']);
            $result->append($method);
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }
        
        return $result;
    }
    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|bool
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        return $this->_tablerateFactory->create()->getRate($request);
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'condition_name' => [
                'package_weight' => __('Weight vs. Destination'),
                'package_value' => __('Price vs. Destination'),
                'package_qty' => __('# of Items vs. Destination'),
            ],
            'condition_name_short' => [
                'package_weight' => __('Weight (and above)'),
                'package_value' => __('Order Subtotal (and above)'),
                'package_qty' => __('# of Items (and above)'),
            ],
        ];

        if (!isset($codes[$type])) {
            throw new LocalizedException(__('Please correct Table Rate code type: %1.', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new LocalizedException(__('Please correct Table Rate code for type %1: %2.', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['bestway' => $this->getConfigData('name')];
    }
      public function getFinalRateShip(RateRequest $request, $total, $oldQty, $freeQty, $rate_price)
    {
        foreach ($total as $value) {
            $request->setPackageWeight($value['w']);
            $request->setPackageQty($oldQty - $freeQty);
            $result = $this->_rateResultFactory->create();
            $rate = $this->getRate($request);
            $rate_price += $rate['price'] * $value['q'];
        }
        return [$result, $rate];
    }

    /**
     * @param $product
     * @param $total
     * @param $i
     * @param $item
     * @return mixed
     */
    public function compareWeight($product, $total, $i, $item)
    {

        $width = $product->getDimensionalWidth();
        $height = $product->getDimensionalHeight();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $WeightFactor= $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('customshipping/general/display_text');
        $ShippingStatus= $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('customshipping/general/enable');
        if($WeightFactor>0)
          $dim = round(($width * $height) / $WeightFactor);  
        else
          $dim = round($width * $height);
        $total[$i]['w'] = $dim;
        $total[$i]['q'] = $item->getQty();
        return $total;

    }
}