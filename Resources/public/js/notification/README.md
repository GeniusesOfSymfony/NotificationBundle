Gos Notification Angular Application
====================================

About
-----
It's angular application for [Gos Notification Bundle](https://github.com/GeniusesOfSymfony/NotificationBundle) to brings
real time notification, user interface to manage notification (like facebook).

Features
--------

- Notification board UI (read, mask as view)
- Title unread notification
- Real time notification with UI and audio
- Expose the same API that [NotificationCenter](https://github.com/GeniusesOfSymfony/NotificationBundle/blob/master/NotificationCenter.php)

Install
--------

Not yet registered on bower and npm.

```html
<script type="text/javascript" src="notification/dist/gos-notification-dist.js"></script>
```

Development
----------

```cmd
gulp //watch by default
gulp watch //watch less & coffee
gulp less //compile less
gulp browserify //compile angular coffee app
gulp serve //compile browersify and less
```

Production
----------

```cmd
gulp serve --production //use it also when you commit dist folder for PR to update properly files.
```

**This is currently in progress, there are still a lot of work !**