<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Controller;

use \tpaycom\tpay\Model\Tpay;

/**
 * Class Tpaycom
 * @package tpaycom\tpay\Controller
 */

abstract class Tpaycom extends \Magento\Framework\App\Action\Action
{


    // Check Real IP if server is proxy, balancer...
    const CHECK_REAL_IP = false;

    // Local IP address
    const LOCAL_IP = '127.0.0.1';

    // STR EMPTY
    const STR_EMPTY = '';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \tpaycom\tpay\Model\Tpay
     */

    protected $_model;

    /**
     * Locale Resolver
     *
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $localeResolver;
    private $objectManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
        ,
        \Magento\Customer\Model\Session $customerSession
        ,
        \Magento\Checkout\Model\Session $checkoutSession
        ,
        \Magento\Sales\Model\OrderFactory $orderFactory
        ,
        \tpaycom\tpay\Model\Tpay $model
        ,
        \Magento\Framework\Locale\Resolver $localeResolver
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_model = $model;
        $this->localeResolver = $localeResolver;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);

        $this->_model->setCustomerID($this->_customerSession->getCustomerId());
    }

    protected function getDotAmount()
    {
        return $this->getFormatAmount($this->_checkoutSession->getLastRealOrder()->getGrandTotal());
    }
}
