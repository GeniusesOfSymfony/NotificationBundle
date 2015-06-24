'use strict'

module.exports = ['$rootScope', '$scope', 'BoardService', ($rootScope, $scope, BoardService) ->
    $scope.display = false
    $scope.notifications = []

    $scope.dismiss = (notification) -> 
       BoardService.dismiss notification

    $scope.$on 'board:display', (event, arg) ->
        $scope.display = arg
        
        if !$scope.display
            return

        BoardService.load $scope
        return
    return
]