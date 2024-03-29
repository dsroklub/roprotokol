<?php
include "/data/roprotokol/public/inc/gitrevision.php";
$garg="?gr=".$gitrevision;
?>
<!DOCTYPE html>
<html lang="da">
  <head>
    <!--#include virtual="/public/eventbasetag.html" -->
    <meta charset="utf-8">
    <base href="/index.shtml"
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DSR roaftaler</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png"  href="media/ico.png" />
    <script>
      if (window.location.pathname.startsWith('/frontend/')) {
      var frontend=document.getElementsByTagName('base')[0].href.split('/')[3];
      window.location.pathname=window.location.pathname.replace('/frontend/','/'+frontend+'/');
      }
    </script>
    <link rel="stylesheet" href="../app/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/bower_components/angular-ui-select/dist/select.min.css">
    <link rel="stylesheet" href="../app/bower_components/ngDialog/css/ngDialog.min.css">
    <link rel="stylesheet" href="../app/bower_components/ngDialog/css/ngDialog-theme-default.css">
    <link rel="stylesheet" href="../app/bower_components/components-font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../app/bower_components/angular-ui-bootstrap-datetimepicker/datetimepicker.css">
    <!--link rel="stylesheet" href="../app/bower_components/angular-chart.js/dist/angular-chart.css"-->
    <link rel="stylesheet" href="event.css<?php echo $garg?>"/>
    <link rel="stylesheet" href="local.css"/>
    <script src="../app/bower_components/angular/angular.min.js"></script>
    <script src="components/angular-locale_da-dk.js"></script>
    <script src="../dsrcommon/components/utilities/ifNull.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/txttotime.js<?php echo $garg?>"></script>
    <script src="../app/bower_components/angular-route/angular-route.min.js"></script>
    <script src="../app/bower_components/angular-bootstrap/ui-bootstrap.js"></script>
    <!--script src="../app/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script-->
    <script src="../app/bower_components_modifications/ui-bootstrap-tpls-elgaard.js"></script>
    <script src="../app/bower_components/angular-ui-select/dist/select.min.js"></script>
    <script src="../app/bower_components/ngDialog/js/ngDialog.min.js"></script>
    <script src="../app/bower_components/ng-table/dist/ng-table.js"></script>
    <script src="../app/bower_components/angular-confirm-modal/angular-confirm.js"></script>
    <script src="../app/bower_components/checklist-model/checklist-model.js"></script>
    <script src="../app/bower_components/moment/moment.js"></script>
    <script src="../app/bower_components/moment/locale/da.js<?php echo $garg?>"></script>
    <script src="../app/bower_components/angular-momentjs/angular-momentjs.js"></script>
    <script src="../app/bower_components/angular-ui-bootstrap-datetimepicker/datetimepicker.js"></script>
    <script src="components/version/version.js"></script>
    <script src="components/version/version-directive.js"></script>
    <script src="components/version/interpolate-filter.js"></script>
    <script src="../app/bower_components/ng-file-upload/ng-file-upload.js"></script>
    <script src="components/database/database.js<?php echo $garg?>"></script>
    <script src="components/sumWork.js<?php echo $garg?>"></script>
    <script src="components/database/database-services.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/onlynumber.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/time.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/dsrtimeformat.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/interval.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/transformkm.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/safefilename.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/mtokm.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/urlencode.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/sidetodk.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/timestring.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/leveltodk.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/rowtodk.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/dk_tags.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/subjecttodk.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/rightreqs.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/righttodk.js<?php echo $garg?>"></script>
    <script src="../../public/js/gitrevision.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/argrighttodk.js<?php echo $garg?>"></script>
    <script src="components/database/login-services.js<?php echo $garg?>"></script>
    <script src="event.js<?php echo $garg?>"></script>
    <script src="node_modules/viz.js/viz.js" type="application/javascript"></script>
    <script src="node_modules/webcola/WebCola/cola.js"></script>
    <script src="node_modules/d3/dist/d3.min.js"></script>
    <script src="node_modules/d3-graphviz/build/d3-graphviz.js"></script>
    <script src="controllers/events.js<?php echo $garg?>"></script>
    <script src="controllers/boat.js<?php echo $garg?>"></script>
    <script src="controllers/work.js<?php echo $garg?>"></script>
    <script src="controllers/year_report.js<?php echo $garg?>"></script>
    <script src="controllers/rowing.js<?php echo $garg?>"></script>
    <script src="controllers/club.js<?php echo $garg?>"></script>
    <script src="controllers/noright.js<?php echo $garg?>"></script>
    <script src="controllers/menu.js<?php echo $garg?>"></script>
    <script src="../dsrcommon/components/utilities/subArray.js<?php echo $garg?>"></script>
    <script src="components/database/database-directives.js<?php echo $garg?>"></script>
    <script src="../app/bower_components/angular-filter/dist/angular-filter.js"></script>
    <script type='text/javascript' src="node_modules/chart.js/dist/Chart.js"></script>
    <script type='text/javascript' src="node_modules/angular-chart.js/dist/angular-chart.js"></script>
    <base href="/">
  </head>
  <body>
  <div class="menuholder" >
    <ul class="menu" ng-controller="menuCtrl as menu">
      <li class="logo">
        <img src="../app/assets/dsrlogo.svg">
      </li>
      <li ng-show="ccurrentuser.user.member_id>0" ng-class="{topsel: activePath=='/login/'}">
        <a href="#!login/"><i class="fa fa-sign-in"></i>Ud/pw</a>
      </li>
      <li ng-hide="ccurrentuser.user.member_id>0" ng-class="{topsel: activePath=='/login/'}">
        <a href="#!login/"><i class="fa fa-user"></i>Log ind</a>
      </li>
      <li ng-class="{topsel: activePath=='/timeline/'}"><a href="#!timeline/"><i class="fa fa-calendar"></i>Rokalender</a></li>
      <li ng-class="{topsel: activePath=='/forumsubscribe/'}"><a href="#!forumsubscribe/"><i class="fa fa-user-plus"></i>Fora</a></li>
      <li ng-hide="ccurrentuser.user.member_id=='baadhal'" ng-class="{topsel: activePath=='/eventcreate/'}"><a href="#!eventcreate/"><i class="fa fa-calendar-plus-o"></i>Begivenhed</a></li>
      <li ng-class="{topsel: activePath=='/message/'}"><a href="#!message/"><i class="fa fa-comment"></i>Beskeder</a></li>
      <li ng-hide="ccurrentuser.user.member_id=='baadhal'" ng-class="{topsel: activePath=='/admin/'}"><a href="#!admin/"><i class="fa fa-bank"></i>Admin</a></li>
      <li ng-class="{topsel: activePath=='/overview/'}"><a href="#!overview/"><i class="fa fa-binoculars"></i>Oversigt</a></li>
      <li ng-hide="ccurrentuser.user.member_id=='baadhal'" ng-class="{topsel: activePath=='/public/'}"><a href="#!public/"><i class="fa fa-user"></i>Min side</a></li>
      <li ng-class="{topsel: activePath=='/member/'}"><a href="#!member/"><i class="fa fa-female"></i><i class="fa fa-male"></i>Medlem</a></li>
      <li ng-class="{topsel: activePath=='/work/'}"><a href="#!work/"><i class="fa fa-hourglass"></i>Arbejde</a></li>
      <li ng-class="{topsel: activePath=='/club/'}"><a href="#!club/"><i class="fa fa-chart-bar"></i>Klub</a></li>
      <li ng-class="{topsel: activePath=='/damages/'}"><a href="#!damages/"><i class="fa fa-chart-bar"></i>Skader</a></li>
      <li ng-class="{topsel: activePath=='/rowing/'}"><a href="#!rowing/"><i class="fa fa-compass"></i>Ro</a></li>
      <li ng-class="{topsel: activePath=='/about/'}"><a href="#!about/"><i class="fa fa-info-circle"></i>Om</a></li>

    </ul>
  </div>
  <div ng-app="eventApp" class="main">
    <div ng-view></div>
  </div>
  </body>
</html>
