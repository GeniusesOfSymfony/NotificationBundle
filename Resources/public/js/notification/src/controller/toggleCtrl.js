'use strict';

module.exports = function($scope, $rootScope){
    $scope.open = false;

    $scope.toggleNotification = function(){
        $scope.open = !$scope.open;
        $rootScope.$broadcast('board:display', $scope.open);
    }
};
