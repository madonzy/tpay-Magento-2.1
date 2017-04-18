<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Controller\tpay;

use Magento\Framework\App\Action\Action;

/**
 * Class Error
 *
 * @package tpaycom\tpay\Controller\tpay
 */
class Error extends Action
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->messageManager->addWarningMessage(__("Wystąpił błąd podczas płatności."));

        return $this->_redirect('checkout/cart');
    }
}
