define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
], function (Component, ko, cartCache, defaultTotal, $, quote, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_WeatherInsurance/delivery-insurance-checkbox'
        },

        //create observables
        isChecked: ko.observable(false), //checkbox state
        isVisible: ko.observable(false), //checkbox visibility

        initialize: function () {
            this._super();

            /**
             * @type {{isCheckboxVisible: boolean, isDefaultChecked: boolean, checkboxLabel: string, insurancePrice: number, checkboxDescription: string}}
             */
            let config = window.checkoutConfig.weatherInsurance; //get data from custom config provider

            //set default value
            this.isVisible(config.isCheckboxVisible);
            this.isChecked(config.isDefaultChecked);

            this.checkboxLabelText = this.formatLabel(config.checkboxLabel, config.insurancePrice);
            this.checkboxDescriptionText = config.checkboxDescription;

            //send ajax if checkbox change state
            this.isChecked.subscribe(function(value) {
                console.log('Insurance checkbox value changed:', value);
                $.ajax({
                    url: '/perspective_weather_insurance/quote/save',
                    type: 'POST',
                    data: { insurance_checkbox_state: value ? 1 : 0 },
                    success: function (response) {
                        //total update
                        cartCache.set('totals', null);
                        defaultTotal.estimateTotals();

                        console.log('AJAX delivery insurance:', response);
                    },
                    error: function (xhr) {
                        console.error('AJAX error:', xhr.responseText);
                    }
                });
            });
            return this;
        },

        /**
         * Format label like this "Add delivery insurance ($10,000.00)"
         */
        formatLabel: function(checkboxLabel, insurancePrice) {
            let priceFormatted = priceUtils.formatPrice(
                insurancePrice,
                quote.getPriceFormat()
            );
            return checkboxLabel + ' (' + priceFormatted + ')';
        }
    });
});
