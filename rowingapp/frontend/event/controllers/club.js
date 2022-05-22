'use strict';
angular.module('eventApp').controller(
  'clubCtrl',
    ['$scope','$routeParams','$route','DatabaseService','NgTableParams','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$q','$anchorScroll','$timeout',
   clubCtrl
  ]);

function make_tables(cat) {
    //console.log("make table "+cat + " sel= "+ $scope.boat_type);
    $scope.triptypestat={};
    $scope.triptypestat.labels=[];
    $scope.triptypestat.series=[];
    $scope.triptypestat.labelmap={};
    $scope.triptypestat.distance=[];
    $scope.triptypestat.numtrips=[];
    $scope.triptypestat.fy=$scope.ddata[0].year;
    if (!$scope.triptypestat.fy) {
	$scope.triptypestat.fy=2010;
    }
    for (var y=$scope.triptypestat.fy;y<=$scope.ddata[$scope.ddata.length-1].year;y++) {
	$scope.triptypestat.series.push('sæson '+y);
	$scope.triptypestat.distance.push([]);
	$scope.triptypestat.numtrips.push([]);
    }
    
    for (var di=0;di<$scope.ddata.length; di++) {
	if (($scope.triptypestat.labelmap[$scope.ddata[di].name] === undefined)) {
            var lix=$scope.triptypestat.labels.length;
            $scope.triptypestat.labelmap[$scope.ddata[di].name]=lix;
            $scope.triptypestat.labels.push($scope.ddata[di].name);
	}
    }
    for (var y=0; y<$scope.triptypestat.distance.length;y++) {
	for (var l=0;l<$scope.triptypestat.labels.length;l++) {
            $scope.triptypestat.distance[y][l]=0.0;
            $scope.triptypestat.numtrips[y][l]=0.0;
	}
    }
    angular.forEach($scope.ddata, function(tt) {
	if (cat=="any" || cat==tt.category) {
            $scope.triptypestat.distance[tt.year-$scope.triptypestat.fy][$scope.triptypestat.labelmap[tt.name]]+=tt.distance/1000.0;
            $scope.triptypestat.numtrips[tt.year-$scope.triptypestat.fy][$scope.triptypestat.labelmap[tt.name]]+=tt.trips;
	}
	//$scope.triptypestat.data[1].push(tt.trips);
    },this);
}


function clubCtrl ($scope, $routeParams,$route,DatabaseService,NgTableParams,LoginService, $filter, ngDialog, orderBy, $log, $location,$q,$anchorScroll,$timeout) {
  var dberr=function(err) {
    $log.debug("db init err "+err);
    if (err['error']) {
      alert('DB fejl '+err['error']);
    }
  }

$scope.getRowerData = function getRowerData(params) {
    var $rdefer=$q.defer();
    DatabaseService.getDataNow('event/stats/rostat',null,
			       function(rs) {
				   var orderedData = params.sorting()? $filter('orderBy')(rs.data, params.orderBy()) :  rs.data;
				   if (orderedData) {
				       orderedData=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
				   }
				   $rdefer.resolve(orderedData);
			       }
			      );
    return $rdefer.promise
}

    $scope.docats = function (val) {
	$scope.rowcategory=val;
	var catfilter={'id':''};
	if (val=='kaniner') {
	    $scope.tableParams.filter({'id':'k'});
	    $scope.boat_type='any';
	} else {
	    $scope.tableParams.filter({'id':''});
	    $scope.boat_type=val;
	}
	make_tables(val);
	console.log("cat");
	$scope.tableParams.filter(catfilter);
	$scope.boattableParams.reload();
	$scope.tableParams.reload();
    };
    

    $scope.tableParams = new NgTableParams({
	page: 1,            // show first page
	count: 100,          // count per page
	filter: {
	    id: ''       // initial filter
	},
	sorting: {
	    rank: 'asc'     // initial sorting
	}
    },{
	counts:[50,100,200,500],
	getData: $scope.getRowerData
    }
					  );
    
    
    DatabaseService.getDataNow('event/stats/sumgraphforce',null, function (res) {
	$scope.graph=res.data;    
	var width = 2200, height = 1400;
	var dcola = cola.d3adaptor(d3).linkDistance(20).symmetricDiffLinkLengths(5). size([width, height]);
	var svg = d3.select("#clubgraph").append("svg")
            .attr("width", width)
            .attr("height", height);
	$scope.triptypecolors={"blandet":"blue","Inriggerkaproning":"red","Motionsroning":"green","Puls og program":"brown","Langtur":"pink","Racerkanin":"grey","Costalroning" : "cyan3","Instruktion":"bisque","Kajakmotionsroning":"darkorange"};
	dcola
	    .nodes($scope.graph.nodes)
	    .links($scope.graph.links)
      .linkDistance(90)
      .avoidOverlaps(true)
      .start(8,10,20);        
    var link = svg.selectAll(".link")
        .data($scope.graph.links)
        .enter().append("line")
    //    .style("line-color", function (d) { return d.color; })
        .style("stroke", function (d) {
          if ($scope.triptypecolors[d.tooltip]) {
            return($scope.triptypecolors[d.tooltip]);
          }
          return $scope.triptypecolors["blandet"];
        }
              )
        .attr("class", "link");
    
    var label = svg.selectAll(".label")
        .data($scope.graph.nodes)
        .enter().append("text")
        .attr("class", "label")
        .style("stroke",function (d) { return d.me?"red":"brown"; })
        .style('opacity',0.7).style("stroke-width",0.5)
        .text(function (d) { return d.name; })
        .call(dcola.drag)
    ;
    var me=[];
    for (var i=0; i<$scope.graph.nodes.length;i++) {
      if ($scope.graph.nodes[i].me) {
        me.push($scope.graph.nodes[i]);
        break;
      }
    }
    var node=svg.selectAll(".node").data(me).enter().append("rect").attr("width", 80).attr("height", 30)  .attr("class", "node").style("fill","gold").call(dcola.drag) ;

    dcola.on("tick", function () {
      link.attr("x1", function (d) { return d.source.x; })
        .attr("y1", function (d) { return d.source.y; })
        .attr("x2", function (d) { return d.target.x; })
        .attr("y2", function (d) { return d.target.y; });
      
      label.attr("x", function (d) { return d.x; })
        .attr("y", function (d) {
          var h = this.getBBox().height;
          return d.y + h/4;
        });
      
      node.attr("x", function (d) {
        return d.x - this.getBBox().width/2;
      }) .attr("y", function (d) {return d.y- this.getBBox().height/2;});
    });        
  },dberr)
}
