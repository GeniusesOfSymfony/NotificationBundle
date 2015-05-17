'use strict'

angular = require('angular')

notificationApp = angular.module('notificationApp', [
    require('angular-toastr')
    require('angular-moment')
    require('angular-scrollbar')
])

notificationApp.constant 'Version', require('../package.json').version
notificationApp.constant 'appConfigs', notificationConfig

notificationApp.config [ '$interpolateProvider', '$sceProvider', '$httpProvider', ($interpolateProvider, $sceProvider, $httpProvider) ->
    $sceProvider.enabled false
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
    $interpolateProvider.startSymbol('[[').endSymbol(']]')
    return
]

notificationApp.run ['websocketService', (websocketService) ->
    websocketService.connect()
    return
]

notificationApp.service 'websocketService', require('./service/websocketService')
notificationApp.service 'notificationService', require('./service/notificationService')
notificationApp.service 'boardService', require('./service/boardService')
notificationApp.controller 'toggleCtrl', require('./controller/toggleCtrl')
notificationApp.controller 'realtimeCtrl', require('./controller/realtimeCtrl')
notificationApp.controller 'boardCtrl', require('./controller/boardCtrl')