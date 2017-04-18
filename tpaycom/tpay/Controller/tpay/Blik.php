<?php
/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */

namespace tpaycom\tpay\Controller\tpay;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use tpaycom\tpay\Api\TpayInterface;
use tpaycom\tpay\Model\TransactionFactory;
use tpaycom\tpay\Model\Transaction;
use tpaycom\tpay\Service\TpayService;
use Magento\Framework\App\Response\Http;

/**
 * Class Blik
 *
 * @package tpaycom\tpay\Controller\tpay
 */
class Blik extends Action
{
    /**
     * @var TpayService
     */
    protected $tpayService;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var TpayInterface
     */
    private $tpay;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * {@inheritdoc}
     *
     * @param TpayInterface      $tpayModel
     * @param TransactionFactory $transactionFactory
     * @param TpayService        $tpayService
     */
    public function __construct(
        Context $context,
        TpayInterface $tpayModel,
        TransactionFactory $transactionFactory,
        TpayService $tpayService,
        Session $checkoutSession
    ) {
        $this->tpay               = $tpayModel;
        $this->transactionFactory = $transactionFactory;
        $this->tpayService        = $tpayService;
        $this->checkoutSession    = $checkoutSession;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data                         = $this->getRequest()->getParams();
        $additionalPaymentInformation = isset($data['additional_data']) ? $data['additional_data'] : [];
        $content                      = 'TRUE';

        if (isset($additionalPaymentInformation[TpayInterface::TERMS_ACCEPT]) && $additionalPaymentInformation[TpayInterface::TERMS_ACCEPT] === 'on') {
            if (isset($additionalPaymentInformation['blik_code']) && strlen($additionalPaymentInformation['blik_code']) === 6) {
                $pass = $this->tpay->getApiPassword();
                $key  = $this->tpay->getApiKey();

                $this->transaction = $this->transactionFactory->create(['apiPassword' => $pass, 'apiKey' => $key]);

                $this->tpayService->initTpayUniqueMd5($this->checkoutSession->getQuote());
                $result = $this->makeBlikPayment($additionalPaymentInformation);

                if (!$result) {
                    $content = 'FALSE';

                    $this->tpayService->unsetTpayUniqueMd5($this->checkoutSession->getQuote());
                }
            } else {
                $this->tpayService->initTpayUniqueMd5($this->checkoutSession->getQuote());
            }
        } else {
            $content = 'FALSE';
        }

        return
            $this
                ->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent($content);
    }

    /**
     * Create  BLIK Payment for transaction data
     *
     * @param array $additionalPaymentInformation
     *
     * @return bool
     */
    protected function makeBlikPayment(array $additionalPaymentInformation)
    {
        $email    = isset($additionalPaymentInformation['email']) ? $additionalPaymentInformation['email'] : null;

        $data     = $this->tpay->getTpayFormData(null, $email);
        $blikCode = $additionalPaymentInformation['blik_code'];

        unset($additionalPaymentInformation['blik_code']);

        $data = array_merge($data, $additionalPaymentInformation);

        $blikTransactionId = $this->transaction->createBlikTransaction($data);

        if (!$blikTransactionId) {
            return false;
        }

        return $this->blikPay($blikTransactionId, $blikCode);
    }

    /**
     * Send BLIK code for transaction id
     *
     * @param string $blikTransactionId
     * @param string $blikCode
     *
     * @return bool
     */
    protected function blikPay($blikTransactionId, $blikCode)
    {
        return $this->transaction->sendBlikCode($blikTransactionId, $blikCode);
    }
}
