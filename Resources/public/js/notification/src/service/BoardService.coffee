'use strict'

module.exports = ['$rootScope', 'NotificationService', 'appConfigs', '$q', ($rootScope, NotificationService, appConfigs, $q) ->
    @start = 1
    @end = 15

    @update = ->
        return

    @dismiss = (notification) ->
        NotificationService.markAsViewed(notification.channel, notification.uuid, () ->
            if appConfigs.debug
                console.log('Marked as read : '+ notification.channel+':'+notification.uuid)
        )
        return

    @notificationCallback = ($scope, channel, eventName) ->
        self = @

        NotificationService.fetch channel, self.start, self.end, (payload) ->
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