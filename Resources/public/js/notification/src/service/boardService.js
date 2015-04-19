'use strict';

module.exports = function($rootScope, websocketService, notificationCenter) {
    this.start = 1;
    this.end = 15;

    this.config = function(configs){
        if(configs.hasOwnProperty('start')){
            this.start = configs.start;
        }

        if(config.hasOwnProperty('end')){
            this.end = configs.end;
        }
    };

    this.update = function(){

    };

    this.notificationCallback = function($scope, route, eventName){
        var _this = this;

        notificationCenter.fetch(websocketService.session, route, _this.start, _this.end, function(payload){
            $scope.$apply(function(){
                $scope.notifications = payload.result;
            });

            $rootScope.$broadcast(eventName, $scope.notifications);
            $rootScope.$broadcast('notification:board:rebuild');
        });
    };

    this.load = function($scope, route){
        var _this = this;

        if(websocketService.isConnected()){
            this.notificationCallback($scope, route, 'notification:board:update');
        }else{
            $rootScope.$on('ws:connect', function(event, session){
                _this.notificationCallback($scope, route, 'notification:board:load');
            });
        }
    };
};