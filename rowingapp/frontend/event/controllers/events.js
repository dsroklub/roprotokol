'use strict';
// Niels Elgaard Larsen, v2

eventApp.controller(
    'eventCtrl',
    ['$scope',         '$routeParams','DatabaseService','LoginService', '$filter', 'ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout','UploadBase',
     function ($scope, $routeParams, DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout,UploadBase) {
	 $anchorScroll.yOffset = 50;
	 $scope.teams=[];
	 $scope.todpattern="[0-2]\\d:[0-5]\\d";
	 $scope.signup={act:[]};
	 $scope.messages=[];
	 $scope.message={};	 
	 $scope.public_path=$location.protocol()+"://"+$location.host()+"/public/user.php";
	 $scope.subscription={};
	 $scope.newforum={"is_public":true,"open":true};
	 $scope.eventarg=$routeParams.event;
	 $scope.rParams=$routeParams;
	 $scope.dateOptions = {
	     showWeeks: false
	 };
	 $scope.init = function() {
	     $scope.newevent={invitees:[]};
	     $scope.newevent.location="DSR";
	     $scope.newevent.max_participants="";
	     $scope.newevent.owner_in=1;
	     $scope.newevent.open=1;
	 }
	 $scope.init();
	 $scope.dbready=false;
	 $scope.dbgrace=true;
	 $timeout(function() { $scope.dbgrace = false;}, 2000);
	 
	 $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];
	 DatabaseService.init({"file":true,"message":true,"event":true,"member":true,"user":true}).then(function () {
	     $scope.boatcategories=DatabaseService.getDB('event/boat_category');
	     $scope.forum_files=DatabaseService.getDB('event/forum_files_list');
	     $scope.fora=DatabaseService.getDB('event/fora');
	     $scope.events=DatabaseService.getDB('event/events_participants');
	     $scope.destinations=DatabaseService.getDB('event/destinations')['DSR'];
	     $scope.userfora=DatabaseService.getDB('event/userfora');
	     $scope.messages=DatabaseService.getDB('event/messages');
	     $scope.member_setting=DatabaseService.getDB('event/member_setting');
	     $scope.eventcategories=DatabaseService.getDB('event/event_category');
	     $scope.eventroles=DatabaseService.getDB('event/roles');
	     $scope.newevent.category=$scope.eventcategories[0];
	     $scope.newevent.starttime=null;
	     $scope.newevent.endtime=null;
	     LoginService.check_user().promise.then(function(u) {         
		 $scope.current_user=u;
	     });
	     $scope.member_path=$location.protocol()+"://"+ $location.host()+"/backend/event/";
	     $scope.site_path=$location.protocol()+"://"+ $location.host();
	     $scope.dbready=true;

	     if ($scope.eventarg) {
		 $log.debug("look for event "+$scope.eventarg);
		 for (var i=0; i<$scope.events.length; i++){
		     if ($scope.events[i].event_id==$scope.eventarg) {
			 $scope.setCurrentEvent($scope.events[i]);
			 $log.debug("found currentevent");
		     }
		 }
	     }
	     $log.debug("events set user " + $scope.current_user);
	     LoginService.set_user($scope.current_user);
	 });
     
     $scope.subscribe = function() {
       var sr=DatabaseService.createSubmit("forum_subscribe",$scope.subscription);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var role=$scope.subscription.forum.is_open?"member":"supplicant"; // must match sql statement, cheaper than having PHP make extra DB call
           $scope.subscription.forum.role=role;
           $scope.subscription.forum.member_id=$scope.current_user.member_id;
           $scope.userfora.push($scope.subscription.forum);
         } else {
           alert(status.error);
         }
       });       
     }

     $scope.accept_forum_supplicant = function() {
       var sr=DatabaseService.createSubmit("forum_accept_supplicant",$scope.subscription);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $scope.subscription.forum.role="member";
         } else {
           alert(status.error);
         }
       });       
     }

     
     $scope.unsubscribe = function(forum) {
       var sr=DatabaseService.createSubmit("forum_unsubscribe",forum);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var ix=$scope.userfora.indexOf(forum);
           $scope.userfora.splice(ix,1);
           forum.role=null;
         } else {
           alert(status.error);
         }
       });
     }

     $scope.forum_add_member = function(arg) {
       $scope.newforummember.role="member";
       $scope.newforummember.forum=$scope.current_forum;
       var sr=DatabaseService.createSubmit("forum_subscribe_by_owner",$scope.newforummember);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $scope.forummembers.push({
             "member_id":$scope.newforummember.member.id,
             "role":$scope.newforummember.role,
             "forum":$scope.newforummember.forum.forum,
             "name":$scope.newforummember.member.name
           }
                                   );
         } else {
           alert(status.error);
         }
       }
                      );
     }

     $scope.is_cox = function (rights) {
       var is_cox=0;
       for (var i=0;i<rights.length;i++ ) {
         if (rights[i].menber_rigth="cox") {
           is_cox=1;
           break;
         }
       }
       return is_cox;
     }
     
     $scope.event_add_member = function(arg) {
       if (!$scope.neweventmember) {
         $scope.neweventmember.role="member";
       }
       $scope.neweventmember.event=$scope.currentevent;
       var sr=DatabaseService.createSubmit("event_subscribe_by_owner",$scope.neweventmember);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var is_cox=$scope.is_cox($scope.neweventmember.member.rights);           
           $scope.currentevent.participants.push(
             {
               "name":$scope.neweventmember.member.name,
               "member_id":$scope.neweventmember.member.id,
               "role":$scope.neweventmember.role,
               "enter_time":new Date().toISOString(),
               "is_cox":is_cox
             });
         } else {
           alert(status.error);
         }
       }
                      );
     }
     // EVENT CREATION
     $scope.eventcreate = function(arg) {
       var sr=DatabaseService.createSubmit("event_create",$scope.newevent);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $scope.init();
         } else if (status.status =='warning') {
           alert("Advarsel: "+status.message);
           $scope.init();
         } else {
           alert("Fejl: "+status.message);
         }
       });
     }

     $scope.set_event_status = function(event) {
       var sr=DatabaseService.createSubmit("set_event_status",event);
       sr.promise.then(function(status) {
	 if (status.status !='ok') {
           alert(status.error);
         }
       });
     }
     

     $scope.accept_event_participant = function(em) {
       em.event_id=$scope.currentevent.event_id;
       var sr=DatabaseService.createSubmit("event_accept_participant",em);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           em.role="member";
         } else {
           alert(status.error);
         }
       });       
     }

     $scope.include_member = function(eventmember) {
       eventmember.event=$scope.currentevent.event_id;
       eventmember.new_role="member";
       var sr=DatabaseService.createSubmit("set_event_member_role",eventmember);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           eventmember.role="member";
         } else {
           alert(status.error);
         }
       })
     }

     $scope.eventmessage = function() {
       $scope.newevent.comment = $scope.current_user.name + " inviterer dig til " + ($scope.newevent.name?$scope.newevent.name:"?");
       if ($scope.newevent.category)  {$scope.newevent.comment +="\ndet er en "+ $scope.newevent.category.name;}
       if ($scope.newevent.starttime)  {$scope.newevent.comment +="\ndet starter "+ $filter('date')($scope.newevent.starttime,"d MMM yyyy kl. HH:mm");}
       if ($scope.newevent.endtime) {$scope.newevent.comment += "\nog slutter " + $filter('date')($scope.newevent.endtime,"d MMM yyyy kl. HH:mm");}
       if ($scope.newevent.destination) {$scope.newevent.comment += "\nTuren går til " +$scope.newevent.destination.name}
       if ($scope.newevent.distance) {$scope.newevent.comment += "\nTuren forventes at være på ca " +$scope.newevent.distance/1000 +" km";}
       if ($scope.newevent.location) {$scope.newevent.comment += "\nVi starter i " +$scope.newevent.location;}
       if ($scope.newevent.max_participants) {$scope.newevent.comment += "\nder er plads til " +$scope.newevent.max_participants +" roere";}
       if ($scope.newevent.boat_category) {$scope.newevent.comment += "\nvi ror: " +$scope.newevent.boat_category.Name; }
     }

     
     $scope.is_event_member = function(event) {
       if (!event || ! $scope.current_user) {
         return false;
       }
       for (var i=0; i<event.participants.length; i++){
         if (event.participants[i] && event.participants[i].member_id==$scope.current_user.member_id ) {
           return true;
         }
       }       
       return false;
     }

     $scope.is_forum_owner = function(event) {
       if (!event || ! $scope.current_user) {
         return false;
       }
       for (var i=0; i<event.participants.length; i++){
         if (event.participants[i] && event.participants[i].member_id==$scope.current_user.member_id ) {
           return true;
         }
       }       
       return false;
     }
     $scope.eventjoin = function(role) {
       $scope.currentevent.new_role=role;
       var sr=DatabaseService.createSubmit("event_join",$scope.currentevent);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var p=angular.copy($scope.current_user);
           p.role=status.role;
           p.is_cox=$scope.current_user.is_cox!="";
           $scope.currentevent.participants.push(p);
         } else {
           alert(status.error);
         }
       })
     }

     $scope.event_remove_participant = function(em) {
       em.event_id=$scope.currentevent.event_id;
       var sr=DatabaseService.createSubmit("event_remove_participant",em);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var ix=$scope.currentevent.participants.indexOf(em);
           $scope.currentevent.participants.splice(ix,1)
         } else {
           alert(status.error);
         }
       });       
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
	 if (status.status == 'error') {
           alert(status.error);
         } else {
             $log.debug("forum message sent");
             $scope.message.sender="Mig";
             $scope.message.type="forum";
	     $scope.message.current=1;
	     $scope.message.source=$scope.message.forum.forum;
             $scope.message.created=new Date().toISOString();
             $scope.messages.splice(0,0,$scope.message);
             $scope.message={};
         }
         if (status.status == 'warning') {

           alert(status.warning);
         }
       });
     }



     $scope.emailflush = function() {
       $scope.member_setting.notification_email=null;
       $scope.publicsetting.$setDirty();

     }

     $scope.forumcreate = function() {
       var sr=DatabaseService.createSubmit("forum_create",$scope.newforum);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $log.debug("forum created");
           $scope.fora.push($scope.newforum);
           $scope.newforum={"is_public":true,"open":true};
         } else {
           alert(status.error);
         }
       });
     }               

     $scope.uploadFile = function(file) {
       file.upload = UploadBase.upload({
         url: '/backend/event/file_upload.php',
         data: {"expire":$scope.forumfile.expire,forum:$scope.forumfile.forum.forum, filename:$scope.forumfile.filename ,"file": file},
       });
       
       file.upload.then(function (response) {
         $scope.forumfile={};
         $timeout(function () {
           file.result = response.data;
         });
       }, function (response) {
         if (response.status > 0)
           $scope.errorMsg = response.status + ': ' + response.data;
       }, function (evt) {
         // Math.min is to fix IE which reports 200% sometimes
         file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
       });
     }
       
     $scope.file_selected = function() {
       if ($scope.forumfile.file) {
         var allowedchars=".:;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890_-#";
         var fns=$scope.forumfile.file.name.replace(" ","_");
         var fn="";
         for (var fi=0;fi<fns.length && fi<100;fi++) {
             if (allowedchars.indexOf(fns.charAt(fi))>=0) {
               fn+=fns.charAt(fi);
             }
         }         
         $scope.forumfile.filename = fn;
       }
     }

     $scope.burl=$location.$$absUrl.split("message/")[0]; // FXIME
     console.log($scope.burl);

     $scope.member_setting_update = function() {
       var sr=DatabaseService.createSubmit("member_setting_update",$scope.member_setting);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           $log.debug("settings updated");
           $scope.publicsetting.$setPristine();
         } else {
           alert(status.error);
         }
       });
          }
     

     $scope.getRowerByName = function (val) {
       // Generate list of ids that we already have added
       return DatabaseService.getRowersByNameOrId(val);
     }

     $scope.setCurrentForum = function (forum) {
       $scope.current_forum=forum;
       DatabaseService.simpleGet('event/forum_members',{"forum":forum.forum}).then(function (d) {
        $scope.forummembers=d.data;
       }
                                                                                                     );
     }
     
     $scope.setCurrentEvent = function (ev) {
       $scope.currentevent=ev;
       $anchorScroll('currenteventbox');
//       var cb=document.getElementById('currenteventbox');
  //     cb.focus();
       
     }
     
     $scope.update_distance = function () {
       $scope.newevent.distance=$scope.newevent.destination.distance;
     }

     $scope.setCurrentMessage = function (message) {
       $scope.currentmessage=message;
     }

     $scope.messagedelete = function (message) {
       var sr=DatabaseService.createSubmit("message_unlink",message);
       sr.promise.then(function(status) {
	 if (status.status =='ok') {
           var ix=$scope.messages.indexOf(message);
           $scope.messages.splice(ix,1);
         } else {
           alert(status.error);
         }
       });
     }

     $scope.addInvitee = function () {
       $scope.newevent.invitees.push($scope.newevent.invitee);
       $scope.newevent.invitee=null
     }


       $scope.toggle_forum_visibility = function (forum) {
	   var sr=DatabaseService.createSubmit("toggle_forum_visibility",forum);
	   sr.promise.then(function(status) {
	       if (status.status =='ok') {
		   forum.is_public = !forum.is_public;
	       } else {
		   alert(status.error);
               }
	   });
       }
       
	 $scope.filematch = function (filefilter) {
	     return function(file) {
		 if (!filefilter) {
		     return true
		 }
		 return (file.filename.match( new RegExp(filefilter, 'i')));
	     }
	 };
	 
	 $scope.forummatch = function (forumfilter) {
	     return function(forum) {
		 if (!forumfilter) {
		     return true
		 }
		 return (forum.forum.match( new RegExp(forumfilter, 'i')));
	     }
	 };
	 
	 $scope.forum_reply = function (message) {
	     $scope.message.forum = $scope.fora.filter (function(f) {
             return (f['forum']==message.source );
           })[0];
	     
	     $scope.message.subject = message.subject;

	     if ($scope.message.subject.indexOf("re:")!=0) {
		 $scope.message.subject = "re: "+$scope.message.subject;
	     }
	     $scope.message.old_body = message.sender + ":\n==\n"+message.body+"\n==\n";
	     $scope.message.body = message.sender + ":\n";
	     $anchorScroll('forum');
	 }
       
	 $scope.messagematch = function (messagefilter) {
	     return function(message) {
		 if (!messagefilter) {
		     return true
		 }         
		 return (
		     message.subject.match( new RegExp(messagefilter, 'i')) ||
			 message.sender.match( new RegExp(messagefilter, 'i')) ||
			 message.body.match( new RegExp(messagefilter, 'i')) 
		 );
	     }
	 };		 
     }
    ]
);
