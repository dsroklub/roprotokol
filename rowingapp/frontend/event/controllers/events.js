'use strict';

eventApp.controller(
  'eventCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','orderByFilter','$log','$location',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, orderBy, $log, $location) {
     $scope.teams=[];
     $scope.todpattern="[0-2]\\d:[0-5]\\d";
     $scope.signup={act:[]};
     $scope.messages=[];
     $scope.public_path=$location.protocol()+"://"+$location.host()+"/public/user";
     $scope.subscription={};
     $scope.eventarg=$routeParams.event;
     $scope.rParams=$routeParams;
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
       $scope.messages=DatabaseService.getDB('event/messages');
       $scope.member_setting=DatabaseService.getDB('event/member_setting');
       $scope.eventcategories=DatabaseService.getDB('event/event_category');
       $scope.newevent.category=$scope.eventcategories[0];
       $scope.newevent.starttime=null;
       $scope.newevent.endtime=null;
       $scope.current_user=DatabaseService.getDB('event/current_user');

       if ($scope.eventarg) {
         $log.debug("look for event "+$scope.eventarg);
               for (var i=0; i<$scope.events.length; i++){
         if ($scope.events[i].event_id==$scope.eventarg) {
           $scope.currentevent=$scope.events[i];
           $log.debug("found currentevent");
         }
       }
       }
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


     // EVENT CREATION
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

     $scope.eventmessage = function() {
       $scope.newevent.comment = $scope.current_user.name + " inviterer dig til " + ($scope.newevent.name?$scope.newevent.name:"?");
       if ($scope.newevent.category)  {$scope.newevent.comment +="\ndet er en "+ $scope.newevent.category.name;}
       if ($scope.newevent.starttime)  {$scope.newevent.comment +="\ndet starter "+ $filter('date')($scope.newevent.starttime,"d MMM yyyy kl. HH:mm");}
       if ($scope.newevent.endtime) {$scope.newevent.comment += "\nog slutter " + $filter('date')($scope.newevent.endtime,"d MMM yyyy kl. HH:mm");}
       if ($scope.newevent.distance) {$scope.newevent.comment += "\nturen forventes at være på ca " +$scope.newevent.distance +" km";}
       if ($scope.newevent.location) {$scope.newevent.comment += "\nVi starter i " +$scope.newevent.location;}
       if ($scope.newevent.max_participants) {$scope.newevent.comment += "\nder er plads til " +$scope.newevent.max_participants +" roere";}
       if ($scope.newevent.boat_category) {$scope.newevent.comment += "\nvi ror i " +$scope.newevent.boat_category; }
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
     
     $scope.messagesend = function() {
       var sr=DatabaseService.createSubmit("send_forum_message",$scope.message);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $log.debug("forum created");
           $scope.message.from="Mig";
           $scope.message.created=new Date().toISOString();
           $scope.messages.push($scope.message);
           $scope.message={};
         } else {
           alert(status.error);
         }
       });
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

     $scope.member_setting_update = function() {
       var sr=DatabaseService.createSubmit("member_setting_update",$scope.member_setting);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $log.debug("settings updated");
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
