var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Perspective_CheckoutModifications/js/model/checkout-data-payment-preselect': true
            },

            'Magento_Checkout/js/view/summary/abstract-total': {
                'Perspective_CheckoutModifications/js/view/summary/abstract-total-mixins': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Perspective_CheckoutModifications/js/view/summary/shipping-mixin': true
            }
        }
    }
};