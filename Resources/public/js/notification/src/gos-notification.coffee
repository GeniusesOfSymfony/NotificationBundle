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

notificationApp.run ['WebsocketService', (WebsocketService) ->
    WebsocketService.connect()
    return
]

notificationApp.directive 'notificationDirective', require('./directive/GosNotification')
notificationApp.service 'WebsocketService', require('./service/WebsocketService')
notificationApp.service 'NotificationService', require('./service/NotificationService')
notificationApp.service 'BoardService', require('./service/BoardService')
notificationApp.controller 'ToggleController', require('./controller/ToggleController')
notificationApp.controller 'RealtimeController', require('./controller/RealtimeController')
notificationApp.controller 'BoardController', require('./controller/BoardController')
