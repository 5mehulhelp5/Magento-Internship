define(['uiRegistry'], function (registry) {
    'use strict';

    return function () {

        function wait() {
            const allow = registry.get('product_form.product_form.price.custom_price_allow_modify');
            const price = registry.get('product_form.product_form.price.custom_price');

            if (!allow || !price) {
                setTimeout(wait, 200);
                return;
            }

            function sync() {
                price.disabled(allow.value() !== '1');
            }

            // начальное состояние
            sync();

            // реакция на клик
            allow.on('value', sync);
        }

        wait();
    };
});
