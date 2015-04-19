'use strict';

var angular = require('angular');

var notificationApp = angular.module('notificationApp', [
    require('angular-toastr'),
    require('angular-moment'),
    require('angular-scrollbar')
]);

notificationApp.constant('version', require('../package.json').version);

//Change symbol in order to be compatible with twig
notificationApp.config(['$interpolateProvider', function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
}]);

//Make symfony Request::isMethod('XmlHttRequest') properly work
notificationApp.config(['$sceProvider', '$httpProvider', function($sceProvider, $httpProvider) {
    $sceProvider.enabled(false);
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

notificationApp.constant('configs', notificationConfig);

notificationApp.run(function(websocketService) {
    websocketService.connect();
});

notificationApp.service('notificationCenter', require('./service/notificationService'));
notificationApp.service('websocketService', require('./service/websocketService'));
notificationApp.service('boardService', require('./service/boardService'));
notificationApp.controller('toggleCtrl', require('./controller/toggleCtrl'));
notificationApp.controller('realtimeCtrl', require('./controller/realtimeCtrl'));
notificationApp.controller('boardCtrl', require('./controller/boardCtrl'));
