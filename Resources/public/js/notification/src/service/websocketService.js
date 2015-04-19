'use strict';

module.exports = function($rootScope, configs){
    this.websocket = null;
    this.connected = false;
    this.hasPreviousConnection = false;
    this.session = null;

    this.connect = function(){
        var _this = this;

        this.websocket = WS.connect(configs.websocketURI);

        this.websocket.on('socket/connect', function(session){
            _this.connected = true;
            _this.session = session;
            console.log('connected to ' + configs.websocketURI);
            $rootScope.$broadcast('ws:connect', session);
        });

        $rootScope.$on('socket/disconnect', function(event, error){
            console.log("Disconnected for " + error.reason + " with code " + error.code);
        });

        this.websocket.on('socket/disconnect', function(error){
            _this.connected = false;
            _this.session = null;
            _this.hasPreviousConnection = true;
            $rootScope.$broadcast('ws:disconnect', error);
        });
    };

    this.isConnected = function(){
        return this.connected;
    }
};