define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    "use strict";
    var fortvisionConfig = window.checkoutConfig.fortvision;

    return Component.extend({
        defaults: {
            template: 'Fortvision_Platform/checkout/view/subscription'
        },
        isAvailable: fortvisionConfig.isAvailable,
        isChecked: fortvisionConfig.checkboxChecked,
        checkboxText: fortvisionConfig.checkboxText

    });
});
