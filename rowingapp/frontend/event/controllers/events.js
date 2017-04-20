'use strict';

eventApp.controller(
  'eventCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, orderBy,$log) {
     $scope.teams=[];
     $scope.todpattern="[0-2]\\d:[0-5]\\d";
     $scope.signup={act:[]};
     $scope.messages=[];
     $scope.subscription={};
     $scope.dateOptions = {
       showWeeks: false
     };

     $scope.init = function(){
       $scope.newevent={invitees:[]};
       $scope.newevent.location="DSR";
       $scope.newevent.max_participants="";
       $scope.newevent.owner_in=1;
     }
     $scope.init();
     $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];
     DatabaseService.init({"event":true,"member":true,"user":true}).then(function () {

       // DatabaseService.getDataNow('cox/aspirants/aspirants',null,function (res) {
       //   $scope.aspirants=res.data;
       // }
       //                           );

       $scope.boatcategories=DatabaseService.getDB('event/boat_category');
       $scope.fora=DatabaseService.getDB('event/fora');
       $scope.events=DatabaseService.getDB('event/events_participants');
       $scope.userfora=DatabaseService.getDB('event/userfora');
       $scope.eventcategories=DatabaseService.getDB('event/event_category');
       $scope.newevent.category=$scope.eventcategories[0];
       $scope.newevent.starttime=null;
       $scope.newevent.endtime=null;
         //new Date($scope.newevent.starttime.getTime()+3600000);
     });
     
     $scope.subscribe = function() {
       $scope.subscription.forum.role="user";
       var sr=DatabaseService.createSubmit("forum_subscribe",$scope.subscription);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
//           $scope.subscription.forum.role="user";
         } else {
           alert(status.error);
         }
       });       
     }

     $scope.unsubscribe = function(forum) {
       var sr=DatabaseService.createSubmit("forum_unsubscribe",forum);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           forum.role=null;
         } else {
           alert(status.error);
         }
       });
     }
     
     $scope.eventcreate = function(arg) {
       var sr=DatabaseService.createSubmit("event_create",$scope.newevent);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $scope.init();
         } else {
           alert(status.error);
         }
       });
     }

     $scope.messagesend = function(arg) {
       alert("message send not implemented");
     }
     
     $scope.forumcreate = function(arg) {
       var sr=DatabaseService.createSubmit("forum_create",$scope.newforum);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $log.debug("forum created");
           $scope.fora.push($scope.newforum);
           $scope.newforum={};
         } else {
           alert(status.error);
         }
       });
          }
     
     $scope.getRowerByName = function (val) {
       // Generate list of ids that we already have added
       return DatabaseService.getRowersByNameOrId(val);
     }

     $scope.setCurrentEvent = function (ev) {
       $scope.currentevent=ev;

     }
       $scope.addInvitee = function () {
       $scope.newevent.invitees.push($scope.newevent.invitee);
       $scope.newevent.invitee=null
     }

   }

  ]
);
