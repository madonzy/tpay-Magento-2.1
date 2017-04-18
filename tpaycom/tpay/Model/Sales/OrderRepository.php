<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Model\Sales;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository as MagentoOrderRepository;
use tpaycom\tpay\Api\Sales\OrderRepositoryInterface;
use tpaycom\tpay\Api\TpayInterface;

/**
 * Class OrderRepository
 *
 * @package tpaycom\tpay\Model\Sales
 */
class OrderRepository extends MagentoOrderRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByIncrementId($incrementId)
    {
        if (!$incrementId) {
            throw new InputException(__('Id required'));
        }

        /** @var OrderInterface $order */
        $order = $this->metadata->getNewInstance()->loadByIncrementId($incrementId);

        if (!$order->getEntityId()) {
            throw new NoSuchEntityException(__('Requested order doesn\'t exist'));
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByTpayUniqueMd5($md5)
    {
        if (!$md5) {
            throw new InputException(__('Id required'));
        }

        /** @var OrderInterface $order */
        $order = $this->metadata->getNewInstance();
        $order->getResource()->load($order, $md5, TpayInterface::UNIQUE_MD5_KEY);

        if (!$order->getEntityId()) {
            throw new NoSuchEntityException(__('Requested order doesn\'t exist'));
        }

        return $order;
    }
}
