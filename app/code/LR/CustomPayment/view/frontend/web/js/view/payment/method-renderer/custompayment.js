define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/quote',
        'mage/validation'
    ],
    function ($,Component,url, errorProcessor,redirectOnSuccessAction, quote) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'LR_CustomPayment/payment/custompayment',
                instruction:'',
            },
            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe(['instruction']);
                return this;
            },
            /**
             * @return {Object}
             */
            getData: function () {
                return {
                    method: this.item.method,
                    // 'instruction': this.instruction(),
                    'additional_data': {
                        'instruction': $('#paymentinstruction').val()
                    }
                };
            },
            /**
             * @return {jQuery}
             */
            validate: function () {
                var form = 'form[data-role=instruction_form]';
                return $(form).validation() && $(form).validation('isValid');
            },
            /**
             * Display Custom Title
             */
            displayCustomTitle: function () {
                return window.checkoutConfig.payment.customTitle;
            },
            /**
             * Get payment method Logo.
             */
            getLogo: function () {
                return window.checkoutConfig.payment.logo;
            },
        });
    }
);