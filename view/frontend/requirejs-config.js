var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Fortvision_Platform/js/checkout/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Fortvision_Platform/js/checkout/model/set-payment-information-mixin': true
            }
        }
    }
};
