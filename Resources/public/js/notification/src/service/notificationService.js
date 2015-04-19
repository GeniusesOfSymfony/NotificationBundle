'use strict';

module.exports = function($rootScope, configs){
    this.websocket = null;

    this.fetch = function(session, route, start, end, successCb){
        var _this = this;
        start = start || 1;
        end = end || 15;

        session.call('notification/fetch', {
            start: start,
            end: end,
            route: route
        }).then(successCb, function(error) {
            console.log(error);
        });
    };

    $rootScope.$on('ws:connect', function(event, session){
        var channels = configs.channels;

        for(var i in channels) {
            session.subscribe(channels[i], function(uri, payload){
                $rootScope.$broadcast('notification:new', {
                    uri: uri,
                    notification: JSON.parse(payload)
                });
            });
        }
    });
};