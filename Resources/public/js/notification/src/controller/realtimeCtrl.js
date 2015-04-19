'use strict';

module.exports = function($scope, toastr){
    $scope.$on('notification:new', function(event, args){
        var notification = args.notification;

        console.log(notification.icon);

        toastr[notification.type](notification.content, notification.title, {
            allowHtml: true,
            progressBar: true
        });
    });
};