'use strict';

var rabbitComperator = function(mid) {
  return (mid.length >0 && mid[0]=='k');
};

angular.module('rowApp').controller(
  'StatCtrl',
  ['$scope', 'DatabaseService', 'NgTableParams', '$filter','$log','$location','$q',
   StatCtrl]
);
function StatCtrl ($scope,   DatabaseService,   NgTableParams, $filter, $log, $location,$q) {  
  $scope.seasons=[];
  $scope.burl=$location.$$absUrl.split("statoverview/")[0];
  $scope.currentseason=new Date().getFullYear();
  $scope.statseason=$scope.currentseason;
  for (var y=$scope.statseason;y>2009;y--) {
    $scope.seasons.push(y);
  }
  $scope.statseason=""+$scope.statseason; // hack, because JS mixes strings and numbers

  $scope.boattype="any";
  
  $scope.isObjectAndHasId = function (val) {
    return typeof(val) === 'string' && val.length > 3;
  };     
  

  $scope.show_normal = function () {
    $scope.tableParams.sorting({});
    $scope.tableParams.filter({'id':''});
    $scope.boattableParams.sorting({});
    $scope.boattype='any';
    $scope.rowcategory='any';
    $scope.statseason=''+$scope.currentseason;
  }

  $scope.getBoatData = function getBoatData(params) {
    var $bdefer=$q.defer();
    var filterInfo = params.filter();
    DatabaseService.getBoatStatistics($scope.boattype,$scope.statseason).then(function (rawdata) {
      var filteredData=filterInfo ? $filter('filter')(rawdata, filterInfo) : rawdata;	
      var orderedData = params.sorting() ?
	  $filter('orderBy')(filteredData, params.orderBy()) :
	  filteredData;
      if (orderedData) {
	orderedData=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
      }         
      $bdefer.resolve(orderedData);
    }
                                                                             );
    return $bdefer.promise
  }

  $scope.getRowerData = function getRowerData(params) {
    var $rdefer=$q.defer();
    var filterInfo = params.filter();
    DatabaseService.getRowerStatistics($scope.boattype,$scope.statseason).then(
      function (rawData) {
        var filteredData=filterInfo ? $filter('filter')(rawData, filterInfo) : rawData;	
        var orderedData = params.sorting() ?  $filter('orderBy')(filteredData, params.orderBy()) :  filteredData;
        if (orderedData) {
	  orderedData=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
        }
        $rdefer.resolve(orderedData);
      }
    );
    return $rdefer.promise
  }
  
  $scope.triptypestat={};
  $scope.triptypestat.labels=[];
  $scope.triptypestat.series=[];
  $scope.triptypestat.labelmap={};
  $scope.triptypestat.distance=[];
  $scope.triptypestat.numtrips=[];
  
  DatabaseService.init({"stats":true, "boat":true,"member":true, "trip":true, }).then(function () {
    $scope.boatcat2dk=DatabaseService.boatcat2dk;
    
    // (Need membership Start date, End Date for following information)
    
    // TODO: Add gray wrench when rower has more then 100KM for the year until last sunday in april next year when it turns red if we have not registred any work
    // 
    
    // TODO: Stacked Barchart - Membership turnover (Filter on Date)
    // Membership turnover per year/month, two bars per month/year joined and left the club stacked by sex
    
    // TODO: Stacked Barchart - Members per year/month (Filter on Date)
    // Members per year/month, two bars per month/year joined and left the club stacked by sex

    // TODO: Stacked Barchart - Seniority (Filter on Date)
    // Seniority, bar per year stacked by sex
    
    // TODO: Age Barchart - Seniority (Filter on Date)
    // Age, bar per year stacked by sex
    
    // TODO: Stacked Barchart - Rowed KM (Filter on Date)
    // Rowed KM, two bars rowboat and kajak stacked by sex
    
    // TODO: Barchart - Triptype - Trips (Filter on Date)
    // Number of trips per Triptype
    
    // TODO: Barchart - Triptype - KM (Filter on Date)
    // Number of KM per Triptype
    
    // TODO: Barchart - Triptype - Person trips (Filter on Date)
    // Number of person trips per Triptype
    
    // TODO: Stacked Barchart - Member activiy KM per intervals per year in % (Filter on Date)
    // Member activiy KM per intervals <100, 100-200, 200-300, 300-500, >500
    
    // TODO: Stacked Barchart - Instructions per finished rabbit       

    var ddata = DatabaseService.getDB('stats/trip_stat_year');
    $scope.triptypestat.fy=ddata[0].year;
    if (!$scope.triptypestat.fy) {
      $scope.triptypestat.fy=2010;
    }
    for (var y=$scope.triptypestat.fy;y<=ddata[ddata.length-1].year;y++) {
      $scope.triptypestat.series.push('sæson '+y);
      $scope.triptypestat.distance.push([]);
      $scope.triptypestat.numtrips.push([]);
    }
    angular.forEach(ddata, function(tt) {
      if (($scope.triptypestat.labelmap[tt.name] === undefined)) {
        $scope.triptypestat.labelmap[tt.name]=$scope.triptypestat.labels.length;
        $scope.triptypestat.labels.push(tt.name);
      }
      $scope.triptypestat.distance[tt.year-$scope.triptypestat.fy][$scope.triptypestat.labelmap[tt.name]]=tt.distance/1000.0;
      $scope.triptypestat.numtrips[tt.year-$scope.triptypestat.fy][$scope.triptypestat.labelmap[tt.name]]=tt.trips;
      //$scope.triptypestat.data[1].push(tt.trips);      
    },this);
    for (var y=0; y<$scope.triptypestat.distance.length;y++) {
      var ya=$scope.triptypestat.distance[y];
      var yt=$scope.triptypestat.numtrips[y];
      for (var l=0;l<$scope.triptypestat.labels.length;l++) {
        if (!ya[l]) {
          ya[l]=0.0;
          yt[l]=0.0;
        }
      }
    }
  }
                                                                                     );

  $scope.docats = function (val) {
    $scope.rowcategory=val;
    if (val=='kaniner') {
      $scope.tableParams.filter({'id':'k'});
      $scope.boattype='any';
    } else {
      $scope.tableParams.filter({'id':''});
      $scope.boattype=val;
      $scope.tableParams.reload();
      $scope.boattableParams.reload();
    }
  };

  $scope.tableParams = new NgTableParams({
    page: 1,            // show first page
    count: 200,          // count per page
    filter: {
      id: ''       // initial filter	
    },
    sorting: {
      rank: 'asc'     // initial sorting
    }
  }, {
    counts:[50,100,200,500],
    getData: $scope.getRowerData
  });
  
  $scope.boattableParams = new NgTableParams({
    page: 1,            // show first page
    count: 1000,
    filter: {
      boatname: ''       // initial filter	
    },
    sorting: {
      rank: 'asc',     // initial sorting
    }
  }, {
    counts:[],
    getData: $scope.getBoatData
  });
  
  $scope.changeSeason= function() {
    $log.info("Change season to " + $scope.statseason)
    if($scope.tableParams) {
      $scope.tableParams.reload();
    }
    if ($scope.boattableParams) {
      $scope.boattableParams.reload();
    }
  }
  
}
