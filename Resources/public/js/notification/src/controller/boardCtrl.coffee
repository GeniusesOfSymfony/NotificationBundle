'use strict'

module.exports = ['$rootScope', '$scope', 'boardService', ($rootScope, $scope, boardService) ->
    $scope.display = false
    $scope.notifications = []

    $scope.dismiss = (notification) -> 
       boardService.dismiss notification

    $scope.$on 'board:display', (event, arg) ->
        $scope.display = arg
        
        if !$scope.display
            return

        boardService.load $scope
        return
    return
]