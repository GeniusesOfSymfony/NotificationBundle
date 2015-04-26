'use strict'

module.exports = ['$rootScope', 'configs', ($rootScope, configs) ->
    @websocket = null

    @fetch = (session, route, start, end, successCb) ->
        _this = this
        start = start or 1
        end = end or 15
        session.call('notification/fetch',
            start: start
            end: end
            route: route).then successCb, (error) ->
        console.log error
        return
    return

    $rootScope.$on 'ws:connect', (event, session) ->
        channels = configs.channels

        for i of channels
            session.subscribe channels[i], (uri, payload) ->
                $rootScope.$broadcast 'notification:new',
                    uri: uri
                    notification: JSON.parse(payload)
                return
            return
    return
]