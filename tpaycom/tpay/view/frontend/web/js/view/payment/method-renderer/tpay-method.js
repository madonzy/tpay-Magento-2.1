/**
 *
 * @category    payment gateway
 * @package     Tpaycom_Magento2.1
 * @author      Tpay.com
 * @copyright   (https://tpay.com)
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (Component, $, quote, additionalValidators) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'tpaycom_tpay/payment/tpay-form'
            },

            redirectAfterPlaceOrder: false,

            getCode: function () {
                return 'tpaycom_tpay';
            },


            afterPlaceOrder: function () {
                window.location.replace(window.checkoutConfig.tpay.payment.redirectUrl);
            },

            placeOrder: function (data, event) {
                var self = this;

                $.ajax({
                    type: 'POST',
                    url: window.checkoutConfig.tpay.payment.blikUrl,
                    data: this.getData(),
                    async: false,
                    showLoader: true,
                    success: function (data) {
                        if (data === 'TRUE') {
                            if (event) {
                                event.preventDefault();
                            }

                            if (self.validate() && additionalValidators.validate()) {
                                self.isPlaceOrderActionAllowed(false);

                                self.getPlaceOrderDeferredObject()
                                    .fail(
                                        function () {
                                            self.isPlaceOrderActionAllowed(true);
                                        }
                                    ).done(
                                        function () {
                                            self.afterPlaceOrder();
                                        }
                                    );

                                return true;
                            }
                        } else if (data === 'FALSE') {
                            var bodySelector = $('body');
                            bodySelector.append('<div class="blik-popup" style="position: fixed; z-index:99; top: 0; left:0; width: 100%; height: 100%; background: rgba(0,0,0,0.2);"> <div  style="position:absolute; top:100px; left: 50%; margin-left: -150px; width:300px; background: #fff; border: 1px #ccc solid;"><p style="padding:30px; text-align: center; margin: 0;">Błędny kod Blik albo nie zaakceptowałeś warunki, spróbuj ponownie!</p><button class="action primary" type="button" style="display: block; cursor: pointer; margin:10px auto 40px auto;"> <span>Zamknij</span></button></div></div>');

                            bodySelector.on('click','.blik-popup .action', function(){
                                $('.blik-popup').remove();
                            });
                        } else {
                            alert('Undefined error while trying to request Blik');
                        }
                    }
                });

                return false;
            },

            showChannels: function () {
                return window.checkoutConfig.tpay.payment.showPaymentChannels;
            },

            getTerms: function () {
                return window.checkoutConfig.tpay.payment.getTerms;
            },


            getLogoUrl: function () {
                return window.checkoutConfig.tpay.payment.tpayLogoUrl;
            },

            getBlikPaymentLogo: function () {
                return window.checkoutConfig.tpay.payment.getBlikPaymentLogo;
            },

            showBlikCode: function () {
                return window.checkoutConfig.tpay.payment.showBlikCode;
            },

            addCSS: function () {
                return window.checkoutConfig.tpay.payment.addCSS;
            },

            BlikInputFocus: function () {
                $("#blik_code_input").focus();
            },

            getBlikCodeInputHTML: function () {
                return window.checkoutConfig.tpay.payment.getBlikCodeInputHTML;
            },

            getData: function () {
                var parent = this._super(),
                    paymentData = {};

                paymentData['kanal'] = $('input[name="channel"]').val();
                paymentData['blik_code'] = $('input[name="blik_code"]').val();
                paymentData['akceptuje_regulamin'] = $('input[name="akceptuje_regulamin"]').val();
                paymentData['email'] = quote.guestEmail ? quote.guestEmail : window.checkoutConfig.customerData.email;

                return $.extend(true, parent, {'additional_data': paymentData});
            },

            isActive: function () {
                return true;
            },
        });
    }
);
