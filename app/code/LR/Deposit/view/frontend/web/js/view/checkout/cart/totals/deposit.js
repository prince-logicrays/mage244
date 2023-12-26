define(
    [
        'LR_Deposit/js/view/checkout/summary/deposit'
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
            checkDepositValue: function () {
                return window.checkoutConfig.depositValue;
            }
        });
    }
);