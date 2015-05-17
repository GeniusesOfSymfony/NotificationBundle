'use strict'

module.exports = ['$rootScope', 'notificationService', 'appConfigs', '$q', ($rootScope, notificationService, appConfigs, $q) ->
    @start = 1
    @end = 15

    @update = ->
        return

    @dismiss = (notification) ->
        notificationService.markAsViewed(notification.channel, notification.uuid, () -> 
            if appConfigs.debug
                console.log('Marked as read : '+ notification.channel+':'+notification.uuid)
        )
        return

    @notificationCallback = ($scope, channel, eventName) ->
        self = @

        notificationService.fetch channel, self.start, self.end, (payload) ->
            $scope.$apply ->
                $scope.notifications = payload.result
                $rootScope.$broadcast eventName, $scope.notifications
                $rootScope.$broadcast 'notification:board:rebuild'
                return
            return
        return

    @load = ($scope) ->
        self = @
        for channel in appConfigs.channels
            @notificationCallback $scope, channel, 'notification:board:update'
    return
]