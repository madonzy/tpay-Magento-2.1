<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Api;

/**
 * Interface TpayInterface
 *
 * @package tpaycom\tpay\Api
 * @api
 */
interface TpayInterface
{
    /**
     * @var string
     */
    const CODE = 'tpaycom_tpay';

    /**
     * @var string
     */
    const CHANNEL = 'kanal';

    /**
     * @var string
     */
    const BLIK_CODE = 'blik_code';

    /**
     * @var string
     */
    const TERMS_ACCEPT = 'akceptuje_regulamin';

    /**
     * Unique field name in quote and order
     *
     * @var string
     */
    const UNIQUE_MD5_KEY = 'tpay_unique_md5';

    /**
     * Return string for redirection
     *
     * @return string
     */
    public function getRedirectURL();

    /**
     * Return data for form
     *
     * @param null|int    $orderId
     * @param null|string $email
     *
     * @return array
     */
    public function getTpayFormData($orderId = null, $email = null);

    /**
     * @return string
     */
    public function getApiPassword();

    /**
     * @return string
     */
    public function getApiKey();

    /**
     * @return string
     */
    public function getSecurityCode();

    /**
     * @return int
     */
    public function getMerchantId();

    /**
     * Check that the BLIK Level 0 should be active on a payment channels list
     *
     * @return bool
     */
    public function checkBlikLevel0Settings();

    /**
     * @return bool
     */
    public function getBlikLevelZeroStatus();

    /**
     * @return bool
     */
    public function onlyOnlineChannels();

    /**
     * @return bool
     */
    public function showPaymentChannels();

    /**
     * Return url to redirect after placed order
     *
     * @return string
     */
    public function getPaymentRedirectUrl();

    /**
     * Return url to Blik request
     *
     * @return string
     */
    public function getBlikUrl();

    /**
     * Return url for a tpay.com terms
     *
     * @return string
     */
    public function getTermsURL();
}
