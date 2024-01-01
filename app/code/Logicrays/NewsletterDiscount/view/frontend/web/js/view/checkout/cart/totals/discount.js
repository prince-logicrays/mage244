define(
    [
        'Logicrays_NewsletterDiscount/js/view/checkout/summary/discount'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            /**
             * @override
             * use to define amount is display setting
             */
            isDisplayed: function () {
                return true;
            },
            checkModuleIsEnable: function () {
                return window.checkoutConfig.moduleStatus;
            },
            checkCustomDeposit: function () {
                return window.checkoutConfig.customDeposit;
            }
        });
    }
);