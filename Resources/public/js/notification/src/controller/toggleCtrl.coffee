'use strict'

module.exports = ['$scope', '$rootScope', ($scope, $rootScope) ->
    $scope.open = false

    $scope.toggleNotification = ->
        $scope.open = !$scope.open
        $rootScope.$broadcast 'board:display', $scope.open
        return
    return
]
