require([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    'use strict';
    function setFortvisionCookie(expires) {
        if ($.cookie('fortvision_uuid') == null) {
            if (!fortvisionFbWeb.sessionData) {
                window.setTimeout(setFortvisionCookie, 50, expires);
                return;
            }
            var uuid = fortvisionFbWeb.sessionData.uuid;
            var date = new Date();
            date.setTime(date.getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);
            $.cookie('fortvision_uuid', uuid, {expires: date});
        }
    }
    setFortvisionCookie(30);
});
