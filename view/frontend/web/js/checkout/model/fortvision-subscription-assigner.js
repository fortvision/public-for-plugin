define([
    'jquery'
], function ($) {
    'use strict';
    var fortvisionConfig = window.checkoutConfig.fortvision;

    return function (paymentData) {
        var fortvisionSubscription;

        if (!fortvisionConfig.isAvailable) {
            return;
        }

        fortvisionSubscription = $('#fortvision-subscription').is(":checked");
        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        paymentData['extension_attributes']['fortvision_subscription'] = fortvisionSubscription;
    };
});
