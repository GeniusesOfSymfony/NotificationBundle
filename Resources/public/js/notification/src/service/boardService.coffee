'use strict'

module.exports = ['$rootScope', 'websocketService', 'notificationCenter, configs', ($rootScope, websocketService, notificationCenter, configs) ->
    @start = 1
    @end = 15

    @config = (configs) ->
        if configs.hasOwnProperty('start')
            @start = configs.start
        if Config.hasOwnProperty('end')
            @end = configs.end
        return

    @update = ->

    @dismiss = (notification) ->
        notificationCenter.markAsViewed(notification.channel, notification.uuid)

        return

    @notificationCallback = ($scope, channel, eventName) ->
        _this = this

        notificationCenter.fetch websocketService.session, channel, _this.start, _this.end, (payload) ->
            $scope.$apply ->
                $scope.notifications = payload.result
                return

            $rootScope.$broadcast eventName, $scope.notifications
            $rootScope.$broadcast 'notification:board:rebuild'
            return
        return

    @load = ($scope) ->
        _this = this
        if websocketService.isConnected()
            for i in configs.channels
                @notificationCallback $scope, channels[i], 'notification:board:update'
        else
            $rootScope.$on 'ws:connect', (event, session) ->
                for i in configs.channels
                    _this.notificationCallback $scope, channels[i], 'notification:board:load'
                return
        return

    return
]