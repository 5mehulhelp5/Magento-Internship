define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';


    return Component.extend({
        defaults: {
            template: 'Perspective_WeatherInsurance/delivery-insurance'
        },

        isChecked: ko.observable(false),

        initialize: function () {
            this._super();

            this.isChecked.subscribe(function(value) {
                console.log('Delivery insurance checked changed:', value);
            });

            return this;
        }
    });
});
