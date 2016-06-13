'use strict';
// Not worth caching for this
app.controller('TodayCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', function ($scope, $routeParams, DatabaseService, $interval, ngForm) {
  $scope.tripstoday=[];
  $scope.onwater=[];
  $scope.available=[];
  $scope.num4=0;
  $scope.num2=0;

  $scope.boatcoms={'i2':0,'i4':0}
  $scope.rowers=0;
  $scope.coxs=1;
  $scope.manerr="";

  
  $scope.maxcrew=0;

  $scope.critical_time = function (tx) {
    if (tx) {
      var t=tx.split(/[- :]/);
      var et=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
      return(et< new Date);
    }
    return false;
  };    

  $scope.updatecrew = function() {
    //    alert(" foo "+$scope.coxs+":"+$scope.rowers);
    var a2=$scope.available["Inrigger 2+"].amount;
    var a4=$scope.available["Inrigger 4+"].amount;
    var max2=Math.floor(($scope.rowers+$scope.coxs)/3);
    var max4=Math.floor(($scope.rowers+$scope.coxs)/5);
    var bcms=[];
    if (max4>$scope.coxs) {
      max4=$scope.coxs;
    }
    if (max2>$scope.coxs) {
      max2=$scope.coxs;
    }
    if (max4>a4) {
      max4=a4;
    }
    if (max2>a2) {
      max2=a2;
    }
    var c2;
    var c4;
    var mc=0;
    for (c2=0; c2<=max2; c2=c2+1) {
      for (c4=0; c4<=max4; c4=c4+1) {
	if (c2*3+c4*5<=$scope.rowers+$scope.coxs && c2*3+c4*5>mc && c2+c4<=$scope.coxs) {
	  mc=c2*3+c4*5;
	} 
      }
    }
    
    for (c2=0; c2<=max2; c2=c2+1) {
      for (c4=0; c4<=max4; c4=c4+1) {
	if (c2*3+c4*5==mc && c2+c4<=$scope.coxs) {
	  bcms.push({"i2":c2,"i4":c4});
	} 
      }
    }
    $scope.maxcrew=mc;
    $scope.boatcoms=bcms;
  };
  
  DatabaseService.init().then(function () {
  }
			       );
  DatabaseService.getTodaysTrips(function (res) {
    $scope.tripstoday=res.data;
  }
				);
  DatabaseService.getOnWater(function (res) {
    $scope.onwater=res.data;
  }
				);
  DatabaseService.getAvailableBoats('DSR',function (res) {
    $scope.available=res.data;
  }
				   );

  
}]);
