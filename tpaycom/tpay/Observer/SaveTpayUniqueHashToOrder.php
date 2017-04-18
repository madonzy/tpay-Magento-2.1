<?php
/**
 * @package   tpaycom\tpay
 * @author    Oleksandr Yeremenko <oyeremenko@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace tpaycom\tpay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use tpaycom\tpay\Api\TpayInterface;

/**
 * Class SaveTpayUniqueHashToOrder
 */
class SaveTpayUniqueHashToOrder implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(EventObserver $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getOrder();
        $quote = $this->cartRepository->get($order->getQuoteId());

        $order->setData(TpayInterface::UNIQUE_MD5_KEY, $quote->getData(TpayInterface::UNIQUE_MD5_KEY));

        return $this;
    }
}
