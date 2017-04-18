<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Service;

use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order;
use tpaycom\tpay\Api\Sales\OrderRepositoryInterface;
use tpaycom\tpay\lib\ResponseFields;

/**
 * Class TpayService
 *
 * @package tpaycom\tpay\Service
 */
class TpayService
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Tpay constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Change order state and notify user if needed
     *
     * @param int  $orderId
     * @param bool $sendEmail
     *
     * @return Order
     */
    public function setOrderStatePendingPayment($orderId, $sendEmail)
    {
        /** @var Order $order */
        $order = $this->orderRepository->getByIncrementId($orderId);

        $order->addStatusToHistory(
            Order::STATE_PENDING_PAYMENT,
            __('Waiting for tpay.com payment.')
        );

        $order->setSendEmail($sendEmail);
        $order->save();

        return $order;
    }

    /**
     * Return payment data
     *
     * @param int $orderId
     *
     * @return array
     */
    public function getPaymentData($orderId)
    {
        /** @var Order $order */
        $order = $this->orderRepository->getByIncrementId($orderId);

        return $order->getPayment()->getData();
    }

    /**
     * Validate order and set appropriate state
     *
     * @param int   $orderId
     * @param array $validParams
     *
     * @return bool|Order
     */
    public function validateOrderAndSetStatus($orderId, array $validParams)
    {
        /** @var Order $order */
        $order = $this->orderRepository->getByIncrementId($orderId);

        if (!$order->getId()) {
            return false;
        }

        $payment = $order->getPayment();

        if ($payment) {
            $payment->setData('transaction_id', $validParams[ResponseFields::TR_ID]);
            $payment->addTransaction(Transaction::TYPE_ORDER);
        }

        $orderAmount     = (float)number_format($order->getGrandTotal(), 2);
        $transactionDesc = $this->getTransactionDesc($validParams);
        $trStatus        = $validParams[ResponseFields::TR_STATUS];
        $emailNotify     = false;

        if ($trStatus === 'TRUE' && ($validParams[ResponseFields::TR_PAID] === $orderAmount)) {
            if ($order->getState() != Order::STATE_PROCESSING) {
                $emailNotify = true;
            }
            $status = __('The payment from tpay.com has been accepted.').'</br>'.$transactionDesc;
            $state = Order::STATE_PROCESSING;
        } else {
            if ($order->getState() != Order::STATE_HOLDED) {
                $emailNotify = true;
            }
            $status = __('Payment has been canceled: ').'</br>'.$transactionDesc;
            $state = Order::STATE_HOLDED;
        }

        $order->setState($state);
        $order->addStatusToHistory($state, $status, true);

        if ($emailNotify) {
            $order->setSendEmail(true);
        }

        $order->save();

        return $order;
    }

    /**
     * Get description for transaction
     *
     * @param array $validParams
     *
     * @return bool|string
     */
    protected function getTransactionDesc(array $validParams)
    {
        if ($validParams === false) {
            return false;
        }

        $error           = $validParams[ResponseFields::TR_ERROR];
        $paid            = $validParams[ResponseFields::TR_PAID];
        $transactionDesc = '<b>'.$validParams[ResponseFields::TR_ID].'</b> ';
        $transactionDesc .= $error === 'none' ? ' ' : ' Error:  <b>'.strtoupper($error).'</b> ('.$paid.')';

        return $transactionDesc.$validParams[ResponseFields::TEST_MODE] === '1' ? '<b> TEST </b>' : ' ';
    }
}
