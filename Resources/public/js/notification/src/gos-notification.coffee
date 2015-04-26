'use strict'

angular = require('angular')

notificationApp = angular.module('notificationApp', [
    require('angular-toastr')
    require('angular-moment')
    require('angular-scrollbar')
])

notificationApp.constant 'version', require('../package.json').version

#Change symbol in order to be compatible with twig
notificationApp.config [
    '$interpolateProvider'
    ($interpolateProvider) ->
        $interpolateProvider.startSymbol('[[').endSymbol(']]')
        return
]

#Make symfony Request::isMethod('XmlHttRequest') properly work
notificationApp.config [
    '$sceProvider'
    '$httpProvider'
    ($sceProvider, $httpProvider) ->
        $sceProvider.enabled false
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
        return
]

notificationApp.constant 'configs', notificationConfig

notificationApp.run ['websocketService', (websocketService) ->
    websocketService.connect()
    return
]

notificationApp.service 'notificationCenter', require('./service/notificationService')
notificationApp.service 'websocketService', require('./service/websocketService')
notificationApp.service 'boardService', require('./service/boardService')
notificationApp.controller 'toggleCtrl', require('./controller/toggleCtrl')
notificationApp.controller 'realtimeCtrl', require('./controller/realtimeCtrl')
notificationApp.controller 'boardCtrl', require('./controller/boardCtrl')