'use strict';

module.exports = function($rootScope, $scope, boardService){
    $scope.display = false;
    $scope.notifications = [];

    $scope.$on('board:display', function(event, arg){
        $scope.display = arg;

        if(!$scope.display){ return; }

        var route = {
            name: 'user_notification',
            parameters: { username: 'user2'}
        };

        boardService.load($scope, route);
    });
};