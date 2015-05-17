'use strict'

module.exports = ['$scope', 'toastr', ($scope, toastr) ->

    $scope.$on 'notification:new', (event, args) ->
        notification = args.notification
        notifConfig =
            allowHtml: true
            timeOut: notification.timeout

        toastr[notification.type] notification.content, notification.title, notifConfig

        return
    return
]