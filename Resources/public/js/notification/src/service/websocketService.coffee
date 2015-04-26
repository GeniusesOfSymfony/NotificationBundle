'use strict'

module.exports = ['$rootScope', 'configs', ($rootScope, configs) ->
    @websocket = null
    @connected = false
    @hasPreviousConnection = false
    @session = null

    @connect = ->
        _this = this
        @websocket = WS.connect(configs.websocketURI)

        @websocket.on 'socket/connect', (session) ->
            _this.connected = true
            _this.session = session
            console.log 'connected to ' + configs.websocketURI
            $rootScope.$broadcast 'ws:connect', session
            return

        $rootScope.$on 'socket/disconnect', (event, error) ->
            console.log 'Disconnected for ' + error.reason + ' with code ' + error.code
            return

        @websocket.on 'socket/disconnect', (error) ->
            _this.connected = false
            _this.session = null
            _this.hasPreviousConnection = true
            $rootScope.$broadcast 'ws:disconnect', error
            return
        return

    @isConnected = ->
        @connected

    return
]