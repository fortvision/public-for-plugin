require([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    'use strict';
    function setFortvisionCookie(expires) {
        console.log('avva12',expires)
        if ($.cookie('fortvision_uuid') == null) {
            if (typeof(fortvisionFbWeb)==='undefined' ||  (typeof(fortvisionFbWeb)!=='undefined' && !fortvisionFbWeb.sessionData)) {
                window.setTimeout(setFortvisionCookie, 50, expires);
                return;
            }
            if (typeof(fortvisionFbWeb)!=='undefined') {
                var uuid = fortvisionFbWeb.sessionData.uuid;
                var date = new Date();
                date.setTime(date.getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);
                $.cookie('fortvision_uuid', uuid, {expires: date});
            }
        }
    }
    setFortvisionCookie(30);
});
