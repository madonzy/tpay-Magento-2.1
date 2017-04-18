<?php
/**
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Api\Sales;

use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;

/**
 * Interface OrderRepositoryInterface
 *
 * @package tpaycom\tpay\Api\Sales
 */
interface OrderRepositoryInterface extends MagentoOrderRepositoryInterface
{
    /**
     * Return new instance of Order by increment ID
     *
     * @param string $incrementId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getByIncrementId($incrementId);

    /**
     * Return new instance of Order by tpay_unique_md5
     *
     * @param string $md5
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getByTpayUniqueMd5($md5);
}
