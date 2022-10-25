'use strict';

angular.module('eventApp', [
  'event.utilities.sumWork',
  'event.utilities.sumDistance',
  'dsrcommon.utilities.onlynumber',
  'dsrcommon.utilities.dsrtime',
  'dsrcommon.utilities.dsrtimeformat',
  'dsrcommon.utilities.txttotime',
  'dsrcommon.utilities.dsrinterval',
  'dsrcommon.utilities.dsrtimestring',
  'dsrcommon.utilities.transformkm',
  'dsrcommon.utilities.safefilename',
  'dsrcommon.utilities.mtokm',
  'dsrcommon.utilities.urlencode',
  'dsrcommon.utilities.ifNull',
  'dsrcommon.utilities.subArray',
  'row.argrighttodk',
  'row.righttodk',
  'row.rightreqs',
  'row.subjecttodk',
  'row.sidetodk',
  'row.leveltodk',
  'row.rowtodk',
  'row.dk_tags',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'eventApp.version',
  'eventApp.database',
  'angular-confirm',
  'chart.js',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'angular.filter',
  'checklist-model',
  'ngRoute',
  'ngFileUpload'
])
.config([
  '$locationProvider', function($locationProvider) {

    $locationProvider.html5Mode({
      enabled: true,
      requireBase: false
    });
    //$locationProvider.html5Mode(true);
  }])
.config([
  '$animateProvider', function($animateProvider) {
    $animateProvider.classNameFilter("XXX");
  }])
.config([
  '$routeProvider', function($routeProvider) {
    $routeProvider.when('/eventsubscribe/', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    }).when('/forumsubscribe/', {
      templateUrl: 'templates/forum.html',
      controller: 'eventCtrl'
  }).when('/overview/', {
      templateUrl: 'templates/overview.html',
      controller: 'eventCtrl'
  }).when('/eventcreate/!#forum/:forum', {
      templateUrl: 'templates/eventcreate.html',
      controller: 'eventCtrl'
  }).when('/eventcreate/', {
      templateUrl: 'templates/eventcreate.html',
      controller: 'eventCtrl'
  }).when('/!#message/:message', {
      templateUrl: 'templates/message.html',
      controller: 'eventCtrl'
  }).when('/message/', {
      templateUrl: 'templates/message.html',
      controller: 'eventCtrl'
  }).when('/admin/', {
      templateUrl: 'templates/admin.html',
      controller: 'eventCtrl'
  }).when('/public/', {
      templateUrl: 'templates/public.html',
      controller: 'eventCtrl'
  }).when('/about/', {
      templateUrl: 'templates/about.html',
      controller: 'noRight'
  }).when('/!#timeline/:event', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    }).when('/timeline/', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    }).when('/member/:memberid', {
      templateUrl: 'templates/member.html',
      controller: 'eventCtrl'
    }).when('/member/', {
      templateUrl: 'templates/member.html',
      controller: 'eventCtrl'
    }).when('/work/', {
      templateUrl: 'templates/work.html',
      controller: 'workCtrl'
    }).when('#!work/', {
      templateUrl: 'templates/work.html',
      controller: 'workCtrl'
    }).when('/rowing/', {
      templateUrl: 'templates/rowing.html',
      controller: 'rowingCtrl'
    }).when('/club/', {
      templateUrl: 'templates/club.html',
      controller: 'clubCtrl'
    }).when('/yearreport/', {
      templateUrl: 'templates/year_report.html',
      controller: 'YearReportCtrl'
    }).when('#!yearreport/', {
      templateUrl: 'templates/year_report.html',
      controller: 'YearReportCtrl'
    }).when('/damages/', {
      templateUrl: 'templates/damages.html',
      controller: 'boatCtrl'
    }).when('/showevent/:event', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    }).when('/forcelogin/', {
      templateUrl: 'templates/login.html',
      controller: 'eventCtrl'
  }).when('/login/', {
      templateUrl: 'templates/login.html',
      controller: 'noRight'
  }).when('/', {redirectTo: '/login'})
      .otherwise({
        templateUrl: 'templates/notimplementet.html',
        controller: 'noRight'
      })
    ;
  }]).config(['uiSelectConfig', function(uiSelectConfig) {
    uiSelectConfig.theme = 'bootstrap';
  }])  .config(['ChartJsProvider', function (ChartJsProvider) {
    // Configure all charts
    ChartJsProvider.setOptions({
      colours: ['#FF5252', '#FF8A80'],
      animation: false
    });
    // Configure all line charts
    ChartJsProvider.setOptions('Line', {
      datasetFill: true
    });
  }]);
//  .config(['http', function(httpConfig) {  httpConfig.headers.common['Content-Type'] = 'application/json; charset=utf-8'; }])
;
