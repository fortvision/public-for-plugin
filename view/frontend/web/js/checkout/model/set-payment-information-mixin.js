define([
    'jquery',
    'mage/utils/wrapper',
    'Fortvision_Platform/js/checkout/model/fortvision-subscription-assigner'
], function ($, wrapper, fortvisionSubscriptionAssigner) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            fortvisionSubscriptionAssigner(paymentData);
            return originalAction(messageContainer, paymentData);
        });
    };
});
