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
     $scope.eventarg=$routeParams.event;

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
       $scope.boatcategories=DatabaseService.getDB('event/boat_category');
       $scope.fora=DatabaseService.getDB('event/fora');
       $scope.events=DatabaseService.getDB('event/events_participants');
       $scope.userfora=DatabaseService.getDB('event/userfora');
       $scope.eventcategories=DatabaseService.getDB('event/event_category');
       $scope.newevent.category=$scope.eventcategories[0];
       $scope.newevent.starttime=null;
       $scope.newevent.endtime=null;
       $scope.current_user=DatabaseService.getDB('event/current_user');

       if ($scope.eventarg) {
         for (var i=0; i<$scope.events.length; i++){
         if ($scope.events[i].id==$scope.eventarg) {
           $scope.currentevent=$scope.events[i];
         }
       }
       }

       
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

     $scope.is_event_member = function() {
       if (!$scope.currentevent) {
         return false;
       }
       for (var i=0; i<$scope.currentevent.participants.length; i++){
         if ($scope.currentevent.participants[i] && $scope.currentevent.participants[i].member_id==$scope.current_user.member_id ) {
           return true;
         }
       }       
       return false;
     }

     $scope.eventjoin = function(arg) {
       var sr=DatabaseService.createSubmit("event_join",$scope.currentevent);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $scope.currentevent.participants.push($scope.current_user);
//           FIXME Do something
         } else {
           alert(status.error);
         }
       })
     }

     $scope.eventleave = function() {
       $log.debug("leave");
       var sr=DatabaseService.createSubmit("event_leave",$scope.currentevent);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           for (var i=0; i<$scope.currentevent.participants.length; i++) {
             if ($scope.currentevent.participants[i].member_id==$scope.current_user.member_id ) {
               $scope.currentevent.participants.splice(i,1);
               break;
             }
           }
         } else {
           alert(status.error);
         }
       })
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
