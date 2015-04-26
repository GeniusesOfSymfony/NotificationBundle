'use strict'

module.exports = ['$rootScope', '$scope', 'boardService', ($rootScope, $scope, boardService) ->
    $scope.display = false
    $scope.notifications = []

    $scope.$on 'board:display', (event, arg) ->
        $scope.display = arg

        if !$scope.display
            return

        route =
            name: 'user_notification'
            parameters: username: 'user2'

        boardService.load $scope, route
        return
    return
]