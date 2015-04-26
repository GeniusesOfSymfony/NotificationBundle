'use strict'

module.exports = ['$rootScope', 'websocketService', 'notificationCenter', ($rootScope, websocketService, notificationCenter) ->
    @start = 1
    @end = 15

    @config = (configs) ->
        if configs.hasOwnProperty('start')
            @start = configs.start
        if config.hasOwnProperty('end')
            @end = configs.end
        return

    @update = ->

    @notificationCallback = ($scope, route, eventName) ->
        _this = this
        notificationCenter.fetch websocketService.session, route, _this.start, _this.end, (payload) ->
            $scope.$apply ->
                $scope.notifications = payload.result
                return
            $rootScope.$broadcast eventName, $scope.notifications
            $rootScope.$broadcast 'notification:board:rebuild'
            return
        return

    @load = ($scope, route) ->
        _this = this
        if websocketService.isConnected()
            @notificationCallback $scope, route, 'notification:board:update'
        else
            $rootScope.$on 'ws:connect', (event, session) ->
                _this.notificationCallback $scope, route, 'notification:board:load'
                return
        return

    return
]