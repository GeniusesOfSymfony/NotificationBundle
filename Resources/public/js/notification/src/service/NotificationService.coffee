'use strict'

module.exports = ['$rootScope', 'appConfigs', 'WebsocketService', ($rootScope, appConfigs, WebsocketService) ->

    @fetch = (channel, start, end, successCb) ->
        self = @
        start = start or 1
        end = end or 15

        WebsocketService.session.call('notification/fetch', start: start, end: end, channel: channel)
        .then successCb, (error) ->
            if appConfigs.debug
                console.log error
        return

    @markAsViewed = (channel, uuid, successCb) ->
        self = @

        WebsocketService.session.call('notification/markAsViewed', channel: channel, uuid: uuid)
        .then successCb, (error) ->
            if appConfigs.debug
                console.log error
        return

    $rootScope.$on 'ws:connect', (event, session) ->
        for i of appConfigs.channels
            session.subscribe appConfigs.channels[i], (uri, payload) ->
                $rootScope.$broadcast 'notification:new',
                    uri: uri
                    notification: JSON.parse(payload)
                return
            return

    return
]