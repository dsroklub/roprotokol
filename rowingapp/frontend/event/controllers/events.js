'use strict';
// Niels Elgaard Larsen, v2

angular.module('eventApp').controller(
  'eventCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout','UploadBase',
   eventCtrl
  ]);

function eventCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout,UploadBase) {
  $anchorScroll.yOffset = 50;
  $scope.mate_trips=[];
  $scope.graph_message=null;
  $scope.currentevent=null;
  $scope.required_work=0;
  $scope.total_work=0;
  $scope.mytrips=null;
  $scope.mytriptypes=null;
  $scope.mates=null;
  $scope.mytripsaggregated=null;
  $scope.teams=[];
  $scope.boatObj=null;
  $scope.todpattern="[0-2]\\d:[0-5]\\d";
  $scope.signup={act:[]};
  $scope.messages=[];
  $scope.forummembers=[];
  $scope.message={};
  $scope.boatsById={};
  $scope.boatsByName={};
  $scope.forumBoat=null;
  $scope.selectedforum={};
  $scope.forumfile={"filefolder":"/"};
  $scope.public_path=$location.protocol()+"://"+$location.host()+"/public/user.php";
  $scope.subscription={};
  $scope.newforum={"is_public":true,"is_open":true,"owner_subscribe":true};
  $scope.eventarg=$routeParams.event;
  $scope.messagearg=$routeParams.message;
  $scope.forumarg=$routeParams.forum;
  $scope.includeforumarg=$routeParams.includeforum;
  $scope.memberarg=$routeParams.memberid;
  $scope.rParams=$routeParams;
  $scope.min_time=new Date();
  $scope.min_date=new Date();
  $scope.min_date.setHours(0);
  $scope.min_date.setMinutes(0);
  $scope.min_date.setSeconds(0);
  $scope.work={'start_time':$scope.min_time};
  $scope.current_forum={"forum":null};
  $scope.current_boat_type={'id':null,'name':null};
  $scope.forumhours=null;
  $scope.dateOptions = {
    showWeeks: false,
    minDate: $scope.min_date
  };
  $scope.enddateOptions = {
    showWeeks: false,
    minDate: $scope.min_time
  };
  $scope.init = function() {
    $scope.newevent={
      invitees:[],
      fora:[],
      "location":"DSR",
      max_participants:"",
      owner_in:1,
      automatic:1,
      endtime_dirty:0
    };
    $scope.neweventmember={};
  }

  $scope.dbready=false;
  $scope.init();
  $scope.dbgrace=true;
  $timeout(function() { $scope.dbgrace = false;}, 2000);

  $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];

  var wait_for_db = function (ok) {
    $log.debug("evt db init done");
    $scope.boatcategories=
      [{id:101,name:"Inriggere"},{id:102,name:"Coastal"},{id:103,name:"Outriggere"},{name:"Kajakker"}];
    $scope.forum_files=DatabaseService.getDB('event/forum_files_list');
    $scope.boatsById=DatabaseService.getDB('boatsById');
    $scope.boatsByName=DatabaseService.getDB('boatsByName');
    $scope.boats=DatabaseService.getDB('event/boats');
    $scope.damage_types=DatabaseService.getDB('event/damage_types');
    $scope.maintenance_boats=DatabaseService.getDB('event/maintenance_boats');
    $scope.events=DatabaseService.getDB('event/events_participants');
    $scope.destinations=(DatabaseService.getDB('event/destinations')['DSR']).concat([{name:"Langtur"}]);
    $scope.userfora=DatabaseService.getDB('event/userfora');
    $scope.fora=DatabaseService.getDB('event/fora');

    if($scope.forumarg) {
      for (var fi=0; fi<$scope.fora.length; fi++) {
        if ($scope.fora[fi].forum==$scope.forumarg) {
          $scope.newevent.fora.push($scope.fora[fi]);
          break;
        }
      }
    }
    if($scope.includeforumarg) {
      DatabaseService.simpleGet('event/forum_members',{"forum":$scope.includeforumarg}).then(
        function (d) {
          $scope.newevent.invitees=d.data;
          $scope.newevent.name=$scope.includeforumarg+" tur";

        },function(err) {console.log("forum member error: "+err)}
      );
    }
    $scope.privatemessage={};
//    $scope.memberid="7843";
    if($scope.memberid) {
      $scope.privatemessage.member=DatabaseService.getRower($scope.memberid);
    }
    $scope.messages=DatabaseService.getDB('event/messages');
    $scope.member_setting=DatabaseService.getDB('event/member_setting');
    $scope.eventcategories=DatabaseService.getDB('event/event_category');
    $scope.eventroles=DatabaseService.getDB('event/roles');
    $scope.newevent.category=$scope.eventcategories[0];
    $scope.newevent.starttime=null;
    //     $scope.newevent.endtime=null;
    LoginService.check_user().promise.then(function(u) {
      $scope.current_user=u;
    });
    $scope.member_path=$location.protocol()+"://"+ $location.host()+"/backend/event/";
    $scope.site_path=$location.protocol()+"://"+ $location.host();
    $log.debug("EV DB READY");
    $scope.dbready=true;

    if ($scope.eventarg) {
      $log.debug("look for event "+$scope.eventarg);
      for (var i=0; i<$scope.events.length; i++){
        if ($scope.events[i].event_id==$scope.eventarg) {
          $scope.setCurrentEvent($scope.events[i]);
          break;
          $log.debug("found currentevent");
        }
      }
    }

    if ($scope.messagearg) {
      $log.debug("look for event "+$scope.messagearg);
      for (var i=0; i<$scope.messages.length; i++){
        if ($scope.messages[i].msgid==$scope.messagearg) {
          $scope.setCurrentMessage($scope.messages[i]);
          break;
          $log.debug("found current message");
        }
      }
    }
    // $log.debug("events set user " + $scope.current_user);
    LoginService.set_user($scope.current_user);
  };

  DatabaseService.init({"fora":true,"file":true,"boat":true,"message":true,"event":true,"member":true,"user":true}).then(
    wait_for_db,
    function(err) {$log.debug("db init err "+err)},
    function(pg) {$log.debug("db init progress  "+pg)}
  );

  $scope.subscribe = function() {
    var sr=DatabaseService.createSubmit("forum_subscribe",$scope.subscription);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        var role=$scope.subscription.forum.is_open?"member":"supplicant"; // must match sql statement, cheaper than having PHP make extra DB call
        $scope.subscription.forum.role=role;
        $scope.subscription.forum.member_id=$scope.current_user.member_id;
        $scope.userfora.push($scope.subscription.forum);
        $scope.set_role(forum.forum,role);
      } else {
        alert(status.error);
      }
    },function(err) {console.log("forum subscribe err"+err)});
  }

  $scope.accept_forum_supplicant = function() {
    var sr=DatabaseService.createSubmit("forum_accept_supplicant",$scope.subscription);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $scope.subscription.forum.role="member";
      } else {
        alert(status.error);
      }
    },function(err) {console.log("forum accept err"+err)});
  }

  $scope.set_role = function(forum,role) {
    for (var fi=0;fi<$scope.fora.length;fi++ ) {
      if ($scope.fora[fi].forum==forum) {
        $scope.fora[fi].role=role;
        break;
      }
    }
  }

  $scope.unsubscribe = function(forum) {
    var sr=DatabaseService.createSubmit("forum_unsubscribe",forum);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        if ($scope.forummembers) {
          var ix=$scope.forummembers.indexOf(forum);
          $scope.forummembers.splice(ix,1);
        }
        forum.role=null;
        $scope.set_role(forum.forum,null);
      } else {
        alert(status.error);
      }
    },function(err) {console.log("forum unsub err"+err)});
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
        }                                );
        $scope.newforummember.member=null;
      } else if (status.status =='warning') {
        alert("! "+status.warning);
      } else {
        alert(status.error);
      }
    },function(err) {
      console.log("forum owner subt err "+err);}
                   );
  }

  $scope.is_cox = function (rights,longcox) {
    var is_cox=0;
    for (var i=0;i<rights.length;i++ ) {
      if (rights[i].member_right==="longdistance" || (!longcox && (rights[i].member_right==="cox"))) {
        is_cox=1;
        break;
      }
    }
    return is_cox;
  }

  $scope.event_add_member = function(arg) {
    if (!$scope.neweventmember.role) {
      $scope.neweventmember.role="member";
    }
    $scope.neweventmember.event=$scope.currentevent;
    var sr=DatabaseService.createSubmit("event_subscribe_by_owner",$scope.neweventmember);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        var is_cox=$scope.is_cox($scope.neweventmember.member.rights,false);
        var is_long_cox=$scope.is_cox($scope.neweventmember.member.rights,true);
        $scope.currentevent.participants.push(
          {
            "name":$scope.neweventmember.member.name,
            "member_id":$scope.neweventmember.member.id,
            "role":$scope.neweventmember.role,
            "enter_time":new Date().toISOString(),
            "is_long_cox":is_long_cox,
            "is_cox":is_cox
          });
      } else if (status.status =='warning') {
        alert(status.warning);
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("event owner sub err: "+err);}
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
      } else if (status.status =='error') {
        //console.log("fejl");
        alert("fejl: "+status.message);
        $scope.init();
      } else {
        alert("Fejl: "+status.message);
      }
    },function(err) {console.log("event create err: "+err)});
  }

  $scope.set_event_status = function(event) {
    var sr=DatabaseService.createSubmit("set_event_status",event);
    sr.promise.then(function(status) {
      if (status.status !='ok') {
        if (status.status =='warning') {
          alert(status.warning);
        } else {
          alert(status.error);
        }
      }
    },function(err) {console.log("set event status err: "+err)});
  }

  $scope.set_work_todo = function(forum_member) {
    var sr=DatabaseService.createSubmit("set_forum_member_hours",forum_member);
    sr.promise.then(function(status) {
      if (status.status !='ok') {
        if (status.status =='warning') {
          alert(status.warning);
        } else {
          alert(status.error);
        }
      }
    },function(err) {console.log("set forum member work hours: "+err)});
  }

  $scope.set_event_openness = function(ev) {
    var sreo=DatabaseService.createSubmit("set_event_openness",ev);
    sreo.promise.then(function(status) {
      if (status.status !='ok') {
        if (status.status =='warning') {
          alert("advarsel: "+status.warning);
        } else {
          //console.log("set openness err");
          alert("fejl "+status.error);
        }
      }
    },function(err) {console.log("evt openness err: "+err)});
  }


  $scope.accept_event_participant = function(em) {
    em.event_id=$scope.currentevent.event_id;
    var srep=DatabaseService.createSubmit("event_accept_participant",em);
    srep.promise.then(function(status) {
      if (status.status =='ok') {
        em.role="member";
        $scope.do_participants($scope.currentevent.participants,null);

      } else {
        alert(status.error);
      }
    },function(err) {console.log("evt accept p err: "+err)});
  }

  $scope.include_member = function(eventmember) {
    eventmember.event=$scope.currentevent.event_id;
    eventmember.new_role="member";
    var srim=DatabaseService.createSubmit("set_event_member_role",eventmember);
    srim.promise.then(function(status) {
      if (status.status =='ok') {
        eventmember.role="member";
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("evt member err: "+err)})
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
    if ($scope.newevent.boat_category) {$scope.newevent.comment += "\nvi ror: " +$scope.newevent.boat_category.name; }
  }


  $scope.is_event_member = function(event) {
    if (!event || !event.participants || ! $scope.current_user) {
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
    var srej=DatabaseService.createSubmit("event_join",$scope.currentevent);
    srej.promise.then(function(status) {
      if (status.status =='ok') {
        var p=angular.copy($scope.current_user);
        p.role=status.role;
        p.is_cox=$scope.current_user.is_cox!="";
        p.is_long_cox=$scope.current_user.is_long_cox!="";
        $scope.currentevent.participants.push(p);
      } else {
        alert(status.error);
      }
    },function(err) {console.log("evt join err: "+err)})
  }

  $scope.event_remove_participant = function(em) {
    em.event_id=$scope.currentevent.event_id;
    var srrp=DatabaseService.createSubmit("event_remove_participant",em);
    srrp.promise.then(function(status) {
      if (status.status =='ok') {
        var ix=$scope.currentevent.participants.indexOf(em);
        $scope.currentevent.participants.splice(ix,1)
      } else {
        alert(status.error);
      }
    },function(err) {console.log("evt rm p err: "+err)});
  }


  $scope.eventleave = function() {
    $log.debug("leave");
    var srel=DatabaseService.createSubmit("event_leave",$scope.currentevent);
    srel.promise.then(function(status) {
      if (status.status =='ok') {
        for (var i=0; i<$scope.currentevent.participants.length; i++) {
          if ($scope.currentevent.participants[i].member_id==$scope.current_user.member_id ) {
            $scope.currentevent.participants.splice(i,1);
            break;
          }
        }
        if (status.promoted) {
          for (var i=0; i<$scope.currentevent.participants.length; i++) {
            if ($scope.currentevent.participants[i].member_id==status.promoted ) {
              $scope.currentevent.participants[i].role="member";
              break;
            }
          }           }
        if (status.dirty) {
          // FIXME             $scope.events=DatabaseService.getDB('event/events_participants');
        }
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("evt leave err: "+err)})
  }

  $scope.messagesend = function(is_sticky) {
    $scope.message.sticky=is_sticky;
    var srms=DatabaseService.createSubmit("send_forum_message",$scope.message);
    srms.promise.then(function(status) {
      if (status.status == 'error') {
        alert(status.error);
      } else {
        $log.debug("forum message sent");
        $scope.message.sender="Mig";
        $scope.message.type="forum";
        $scope.message.current=1;
        $scope.message.id=status.message_id;
        $scope.message.source=$scope.message.forum.forum;
        $scope.message.created=new Date().toISOString();
        if ($scope.message.replace) {
          for (var mi=0;mi<$scope.messages.length;mi++) {
            if ($scope.messages[mi].id==$scope.message.replace) {
              $scope.messages.splice(mi,1);
              break;
            }
          }
        }
        $scope.messages.splice(0,0,$scope.message);
        $scope.message={};
      }
      if (status.status == 'warning') {

        alert(status.warning);
      }
    },function(err) {$log.debug("forum msg err: "+err)});
  }

    $scope.privatemessagesend = function() {
    var sr=DatabaseService.createSubmit("send_private_message",$scope.privatemessage);
    sr.promise.then(function(status) {
      if (status.status == 'error') {
        alert(status.error);
      } else {
        $log.debug("private message sent");
        $scope.privatemessage={};
      }
      if (status.status == 'warning') {
        alert(status.warning);
      }
    }, function(err) {$log.debug("priv msg err: "+err)});
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
        $scope.newforum.owner=$scope.current_user.member_id;
        $scope.newforum["role"]=null;
        $scope.fora.push($scope.newforum);
        $scope.newforum={"is_public":true,"is_open":true,"owner_subscribe":true};
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("forum create submit error: "+err)});
  }

  $scope.winterforumcreate = function() {
    var sr=DatabaseService.createSubmit("create_winter_team",$scope.newboatforum);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("winter boatforum created");
        var boatname=$scope.newboatforum.boat.name;
        $scope.newboatforum.owner=$scope.current_user.member_id;
        $scope.newboatforum.forum=boatname+" vintervedligehold";
        $scope.newboatforum.description=boatname+" vintervedligehold";
        $scope.newboatforum.is_public=true;
        $scope.newboatforum.boat.forum=$scope.newboatforum.forum;
        $scope.newboatforum.boat=boatname;
        $scope.newboatforum.is_open=true;
        $scope.fora.push($scope.newboatforum);
        $scope.newboatforum={"is_public":true,"is_open":true,"owner_subscribe":true};
      } else {
        alert(status.error);
      }
    },function(err) {console.log("vinter team submit error"+err)});
  }

  $scope.forumdelete = function(forum) {
    var sr=DatabaseService.createSubmit("forum_delete",forum);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("forum deleted");
        var ix=$scope.fora.indexOf(forum);
        $scope.fora.splice(ix,1);
      } else {
        alert(status.error);
      }
    },function(err) {console.log("forum del error: "+err)});
  }

  $scope.uploadFile = function(file) {
    if ($scope.current_forum && !$scope.forumfile.forum) {
      $scope.forumfile.forum=$scope.current_forum;
    }

    file.upload = UploadBase.upload({
      url: '/backend/event/file_upload.php',
      data: {
        "expire":$scope.forumfile.notexpire?"3017-04-04":$scope.forumfile.expire,
        "forum":$scope.forumfile.forum.forum,
        "filename":$scope.forumfile.filename,
        "filefolder":$scope.forumfile.filefolder,
        "file": file},
    });

    file.upload.then(
      function (response) {
        $scope.forum_files.push(
          {
            "forum":$scope.forumfile.forum.forum,
            "folder":$scope.forumfile.filefolder,
            "file_length":file.file.size,
            "filename":$scope.forumfile.filename
          }
        );
        $scope.forumfile.expire=null;
        $scope.forumfile.file=null;
        $scope.forumfile.filename=null;
        if ($scope.forumfile.forum.folders.indexOf($scope.forumfile.filefolder)<0) {
          $scope.forumfile.forum.folders.push($scope.forumfile.filefolder);
        }
        $timeout(function () {
          file.result = response.data;
        });
      }, function (response) {
        if (response.status > 0) {
          $scope.errorMsg = response.status + ': ' + response.data;
        }
      }, function (evt) {
        // Math.min is to fix IE which reports 200% sometimes
        file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
      },function(err) {console.log("file up error: "+err)});
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

  $scope.burl=$location.$$absUrl.split(/\/\#\!/)[0]; // 
  $scope.burl=$scope.burl.split(/message|forumsubscribe|eventcreate\//)[0]; //
 // $log.debug("burl="+$scope.burl);

  $scope.add_forummemberwork = function(member) {
    var work=member.work;
    var d={"forummember":member,"forum":$scope.current_forum,"work":work};
    var sr=DatabaseService.createSubmit("forummember_addwork",d);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("member value updated");
        if (!member.hours) {
          member.hours=0.0;
        }
        member.hours = 1.0*member.hours+1.*work.hours;
        if (!work.start_time) {
          var wd=new Date();
          work.start_time=wd.toISOString();
        }
        member.log.push({'work':work.done, 'hours':work.hours,'start_time':work.start_time});
        $scope.updateForumHours($scope.current_forum);
        work.start_time=null;
        work.done=null;
        work.hours=null;
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("forum mem add work err: "+err)}
                   );
  }


  $scope.member_setting_update = function() {
    var sr=DatabaseService.createSubmit("member_setting_update",$scope.member_setting);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("settings updated");
        $scope.publicsetting.$setPristine();
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("member setting upd error: "+err)});
  }

  $scope.getRowerByName = function (val,members) {
    if (!members) {
      members=[];
    }
    console.log("get ",val);
    return DatabaseService.getRowersByNameOrId(val,members);
  }

  $scope.updateForumHours = function (forum) {
    $scope.forumhours=0.0;
    for (var mi=0; mi< $scope.forummembers.length; mi++) {
      $scope.forumhours += 1.0*$scope.forummembers[mi].hours;
    }
    if ($scope.boatObj && $scope.boatObj.max_hours) {
      $scope.pctHours=(100*$scope.forumhours/$scope.boatObj.max_hours).toFixed(1);
    }
  }

  $scope.setCurrentForum = function (forum) {
    $scope.current_forum=forum;
    if (forum.boat) {
      $scope.boatObj=$scope.boatsByName[forum.boat];
    }
    for (var f=0; f<$scope.fora.length; f++) {
      if ($scope.fora[f].forum==forum.forum) {
        $scope.forumfile.forum=$scope.fora[f];
        break;
      }
    }
    //       }
    DatabaseService.simpleGet('event/forum_members',{"forum":forum.forum}).then(
      function (d) {
        $scope.forummembers=d.data;
        $scope.updateForumHours(forum);
      },function(err) {console.log("forum member error: "+err)}
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
    },function(err) {$log.debug("msg unlink error: "+err)});
  }

  $scope.sticky_delete = function (message) {
    var sr=DatabaseService.createSubmit("sticky_unlink",message);
    sr.promise.then(
      function(status) {
        if (status.status =='ok') {
          var ix=$scope.messages.indexOf(message);
          $scope.messages.splice(ix,1);
        } else {
          alert(status.error);
          }
        },
      function(err) {$log.debug("sticky unlink error: "+err)});
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
    },function(err) {console.log("toggle forum vis"+err)});
  }

  $scope.toggle_forummember_role = function (forummember) {
    forummember.old_role=forummember.role;
    if (forummember.role=="admin") {
      forummember.role="member";
    } else {
      forummember.role="admin";
    }
    var sr=DatabaseService.createSubmit("set_forum_member_role",forummember);
    sr.promise.then(function(status) {
      if (status.status !='ok') {
        forummember.role=forummember.old_role;
        alert(status.error);
      }
    }, function(err) {console.log("set forum member role error: "+err)});
  }

  $scope.filematch = function (filefilter) {
    return function(file) {
      if (!filefilter || filefilter=="") {
        return true
      }
      return (
        file.folder==filefilter ||
          file.filename.match( new RegExp(filefilter, 'i'))
      );
    }
  };

  $scope.set_file_filter = function (filefilter) {
    $scope.filefilter=filefilter;
  }

  $scope.forummatch = function (forumfilter) {
    return function(forum) {
      if (!forumfilter) {
        return true
      }
      return (forum.forum.match( new RegExp(forumfilter, 'i')));
    }
  };

  $scope.forum_show_member = function () {
      return function(m) {
        //console.log("fshow " + m.member_id)
      return (m.member_id != "baadhal");
    }
  };

  $scope.event_boat_type_match = function () {
    return function (event) {
      if (!($scope.current_boat_type && $scope.current_boat_type.name)) {
        return true;
      }
      return (event.boats==$scope.current_boat_type.name);
    }
  };

  $scope.event_forum_match = function () {
    return function (event) {
      if (!($scope.current_forum && $scope.current_forum.forum)) {
        return true;
      }
      for (var i=0;i<event.fora.length;i++) {
        if (event.fora[i].forum==$scope.current_forum.forum) {
          return true;
        }
      }
      return false;
    }
  };


  $scope.forum_reply = function (message,edit) {
    $scope.message.forum = $scope.fora.filter (function(f) {
      return (f['forum']==message.source );
    })[0];
    $scope.message.subject = message.subject;
    if (edit) {
      $scope.message.body = message.body;
      $scope.message.replace=message.id;
    } else {
      $scope.message.body = message.sender + ":\n";
      $scope.message.old_body = message.sender + ":\n==\n"+message.body+"\n==\n";
      if ($scope.message.subject.indexOf("re:")!=0) {
        $scope.message.subject = "re: "+$scope.message.subject;
      }
    }
    $anchorScroll('forum');
  }

  $scope.set_event_end = function () {
    if ($scope.newevent.endtime) {
      $scope.newevent.endtime_dirty=1;
    }
  }

  $scope.set_event_start = function () {
    if ($scope.newevent.starttime<$scope.min_time) {
      $scope.newevent.starttime=$scope.min_time;
    }
    if ($scope.newevent.starttime && ($scope.newevent.endtime < $scope.newevent.starttime   || !$scope.newevent.endtime_dirty)) {
      var tdiff=3600000;
      if ($scope.newevent.destination) {
        tdiff=$scope.newevent.destination.duration*3600000;
      }
      $scope.enddateOptions.minDate=$scope.newevent.starttime;
      $scope.newevent.endtime=new Date($scope.newevent.starttime.getTime()+tdiff);
    }
  }


  $scope.do_participants = function(participants,oldparticipants) {
    if (participants) {
      var is_longdistance=($scope.currentevent.event_category=="langtur");

      var coxs=0;
      var nr=0;
      for (var i=0; i<participants.length;i++) {
        var rw=participants[i];
        if (["member","owner"].indexOf(rw.role)>=0) {
          nr++;
          if (rw.is_long_cox>0 || (!is_longdistance && rw.is_cox>0)) {
            coxs++;
          }
        }
      }

      if ($scope.currentevent.max_participants) {
        var nr=Math.min(nr,$scope.currentevent.max_participants);
      }
      var max2=Math.floor(nr/3);
      var max4=Math.floor(nr/5);
      if (max4>coxs) {
        max4=coxs;
      }
      if (max2>coxs) {
        max2=coxs;
      }

      var mc=0;
      for (var c2=0; c2<=max2; c2++) {
        for (var c4=0; c4<=max4; c4++) {
          if (c2*3+c4*5 <= nr && c2*3+c4*5>mc && c2+c4<=coxs) {
            mc=c2*3+c4*5;
          }
        }
      }

      var bcms=[];
      for (var c2=0; c2<=max2; c2++) {
        for (var c4=0; c4<=max4; c4++) {
          if (c2*3+c4*5==mc && c2+c4<=coxs) {
            bcms.push({"i2":c2,"i4":c4});
          }
        }
      }
      //return {'configuration':bcms, maxcrew:mc};
      //         bcms=[];
      $scope.crews={"configurations":bcms, "on_water":mc,"rowers":nr, "left_out":nr-mc};
    } else {
      $scope.crews={};
    }
  }

  $scope.$watchCollection('currentevent.participants', $scope.do_participants);

  $scope.getfolders = function(fld) {
    if (!$scope.forumfile.forum || !$scope.forumfile.forum.folders) return [];
    var flds = $scope.forumfile.forum.folders.slice();
    if (fld && flds.indexOf(fld) === -1) {
      flds.unshift(fld);
    }
    // console.log(flds);
    return flds;
  }


  $scope.do_event_create = function () {
    $location.url("/eventcreate/");
    $location.search({"forum":$scope.current_forum.forum});
  }

  $scope.show_member = function (memberid) {
    $location.url("/member/");
    $location.search({"memberid":memberid});
  }

  $scope.update_mate = function (otherid) {
    console.log("update mate "+$scope.othermember.id);
    if ($scope.othermember.id) {
      DatabaseService.getDataNow('event/common_trips','otherrower='+$scope.othermember.id, function (res) {
        $scope.mate_trips=res.data;
      }
                                );
    } else {
      $scope.mate_trips=[];
    }
  }

  $scope.toggle_personal = function (fid,arg,format) {
    var sid=fid+(arg?arg:"");
    var fmt=format?format:"json";
    if ($scope.show[sid]) {
      DatabaseService.getDataNow('/event/stats/'+fid,"q="+(arg?arg:"")+"&format="+fmt,function (res) {
        $scope[sid]=res.data;
      }
                                );
    } else {
      $scope[sid]=null;
    }
  }

  $scope.toggle_data = function (dir,fid,arg,format) {
    var sid=fid+(arg?arg:"");
    var fmt=format?format:"json";
    if ($scope.show[sid] || $scope.show[fid]) {
      DatabaseService.getDataNow('/event/'+dir+'/'+fid,"q="+(arg?arg:"")+"&format="+fmt,function (res) {
        $scope[fid]=res.data;
      }
                                );
    } else {
      $scope[sid]=null;
    }
  }

  $scope.toggle_chart = function() {
    if ($scope.mo) {
      $scope.mo=null;
    } else {
      $scope.mo={};
      $scope.mo.labels=["jan","feb","mar","apr","maj","jun","jul","aug","sep","okt","nov","dec"];
      $scope.mo.series=[];
      $scope.mo.data=[];
      $scope.kmtbl=[];
      $scope.km_years=[];
      $scope.yearsum={};
      $scope.totalyearsum=0.0;
      $scope.monthsum={};
      DatabaseService.getDataNow('event/stats/mystatmonth',null,function(d) {
        if (d.data.length>0) {
          $scope.mo.fy=Math.max(d.data[0].year,2000); // Sanity to avoid year zero for null value
          for (var tm=0;tm<12;tm++) {
            $scope.kmtbl[tm]={};
            $scope.monthsum[tm]=0.0;
            for (var y=$scope.mo.fy;y<=d.data[d.data.length-1].year;y++) {
              $scope.kmtbl[tm][y]=0.0;
            }
          }
          for (var y=$scope.mo.fy;y<=d.data[d.data.length-1].year;y++) {
            $scope.km_years.push(y);
            $scope.yearsum[y]=0.0;
            $scope.mo.data.push([]);
            $scope.mo.series.push(""+y);
            for (var wn=0;wn<12;wn++) {
              $scope.mo.data[y-$scope.mo.fy][wn]=0;
            }
          }
          angular.forEach(d.data, function(w) {
            if (w.year) {
              $scope.mo.data[w.year-$scope.mo.fy][w.month]=w.distance/1000.0;
              $scope.kmtbl[w.month][w.year]=w.distance/1000.0;
              $scope.yearsum[w.year]+=w.distance/1000.0;
              $scope.monthsum[w.month]+=w.distance/1000.0;
              $scope.totalyearsum +=w.distance/1000.0;
            }
          },this);
        }
      });
    }
  }

  $scope.render_graph=function() {
    $scope.graph_message="laver graf over roere. VENT!";
    DatabaseService.getDataNow('event/stats/sumgraph',null, function (res) {
      var graphviz = d3.select("#sumgraph").graphviz().logEvents(true).tweenShapes(false).tweenPaths(false);
      graphviz.engine('fdp').dot(res.data).render().on('end',function() {
        $scope.graph_message="";
        $scope.$apply();
      });
    }
                              );
  }
  $scope.closetrip = function (tripid) {
    var sr=DatabaseService.createSubmit("closetrip",tripid);
    sr.promise.then(function(status) {
      if (status.status !='ok') {
        if (status.status =='warning') {
          alert(status.warning);
        } else {
          alert(status.error);
        }
      }
    },function(err) {console.log("set forum member work hours: "+err)});
  }

  $scope.make_event_for_forum = function (forum) {
    //FIXME
  }

  $scope.messagematch = function (messagefilter) {
    return function(message) {
      if (!messagefilter) {
        mf=null;
      } else {
        var mf=messagefilter.toLowerCase();
      }
      return (
          (!$scope.current_forum.forum || message.source == $scope.current_forum.forum ) &&
          (!$scope.message||!$scope.message.forum || message.source == $scope.message.forum.forum ) &&
          (!messagefilter ||
           message.subject.toLowerCase().indexOf(mf)>-1 ||
           (message.sender && message.sender.toLowerCase().indexOf(mf)>-1) ||
           message.body.toLowerCase().indexOf(mf)>-1
          )
      );
    }
  };
}
