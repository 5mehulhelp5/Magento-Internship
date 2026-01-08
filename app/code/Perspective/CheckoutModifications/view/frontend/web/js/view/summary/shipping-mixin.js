define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-shipping-information'
], function (quote, setShippingInformationAction) {
    'use strict';

    return function (Component) {
        return Component.extend({

            initialize: function () {
                this._super();

                quote.shippingMethod.subscribe(function (method) {
                    setShippingInformationAction();
                });
            }
        });
    };
});
