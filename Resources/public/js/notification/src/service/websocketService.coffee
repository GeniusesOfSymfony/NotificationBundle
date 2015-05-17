'use strict'

module.exports = ['$rootScope', 'appConfigs', ($rootScope, appConfigs) ->
    @websocket = null
    @connected = false
    @hasPreviousConnection = false
    @session = null

    @connect = ->
        self = @

        @websocket = WS.connect(appConfigs.websocketURI)

        @websocket.on 'socket/connect', (session) ->
            self.connected = true
            self.session = session

            if appConfigs.debug
                console.log 'connected to ' + appConfigs.websocketURI

            $rootScope.$broadcast 'ws:connect', session
            return

        $rootScope.$on 'socket/disconnect', (event, error) ->
            if appConfigs.debug 
                console.log 'Disconnected for ' + error.reason + ' with code ' + error.code
            return

        @websocket.on 'socket/disconnect', (error) ->
            self.connected = false
            self.session = null
            self.hasPreviousConnection = true
            $rootScope.$broadcast 'ws:disconnect', error
            return
        return

    @isConnected = ->
        @connected

    return
]